<?php
namespace App\Services\Backend;

use App\Imports\UserImport;
use App\Jobs\PushNoticeToDepartment;
use App\Jobs\RetractNoticeToDepartment;
use App\Models\User;
use App\Models\Notice;
use App\Services\Service;
use App\Traits\ImportTemplateTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * 消息服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class NoticeService extends Service
{
    public ?string $className = Notice::class;

    /**
     * 标记已读
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User           $user
     * @param  string $id
     * @return void
     */
    public function markRead(User $user, string $id)
    {
        DB::table('users_notices')->where(['user_id' => $user->id, 'notice_id' => $id])->update(['is_read' => 1]);
    }

    /**
     * 获取未读消息数
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User    $user
     * @return integer
     */
    public function getUnreadCountByUser(User $user): int
    {
        return DB::table('users_notices')->where(['user_id' => $user->id, 'is_read' => 0])->count();
    }

    /**
     * 通过用户查询消息记录
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User  $user
     * @return array
     */
    public function getListByUser(User $user): array
    {
        $notices = DB::table('users_notices')->where(['user_id' => $user->id])->get();
        $noticeIds = $notices->pluck('notice_id')->toArray();
        $readList = $notices->pluck('is_read', 'notice_id')->toArray();
        if (!$noticeIds)
            $noticeIds = [0];
        $this->setQuery(function ($query) use ($noticeIds) {
            $query->whereIn('id', $noticeIds);
        });
        $result = parent::list();
        $result['items'] = $result['items']->map(function ($item) use ($readList) {
            $needApprove = optional($item->model)->status == 1 ?? false;
            $userName = optional(optional($item->user)->profile)->name ?? $item->user->number;
            return [
                'id' => $item->id,
                'user_id' => $item->user_id,
                'user' => $userName,
                'from' => $item->from ?? $userName,
                'title' => $item->title,
                'type' => $item->type,
                'extra_type' => $item->extra_type,
                'created_at' => $item->created_at,
                'extra' => $item->extra,
                'is_read' => $readList[$item->id] == 1,
                'need_approve' => $needApprove
            ];
        });
        return $result;
    }


    /**
     * 获取消息详情
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User   $user
     * @param  string $id
     * @return array
     */
    public function getDetail(User $user, string $id): array
    {
        $notice = parent::findById($id);
        if (!$notice->users->where('id', $user->id)->first()) {
            throw ValidationException::withMessages(['id.exists' => '参数错误']);
        }
        $name = null;
        if ($notice->extra_type == 'examine') {
            $name = optional(optional($notice->model)->commit)->name;
        }
        if ($notice->extra_type == 'torque') {
            $name = optional(optional($notice->model)->torque)->number;
            $extraField = [
                'plant' => 'plant',
                'line' => 'assembly_line',
                'engine' => 'engine_type',
                'vehicle_type' => 'motorcycle_type',
                'model' => 'bolt_model',
                'type' => 'bolt_type',
                'status' => 'bolt_status',
                'stage' => 'assembly_status',
                'special' => 'special',
            ];
            if ($notice->extra) {
                $extra = collect($notice->extra);
                $dictService = new DictService;
                $assemblyService = new AssemblyService;
                $extra = $extra->map(function ($ext) use ($dictService, $assemblyService, $extraField) {
                    if (array_key_exists($ext['field'], $extraField)) {
                        $ext = [
                            'field' => $ext['field'],
                            'before' => $dictService->getNameByCode($extraField[$ext['field']], $ext['before']) ?? '',
                            'content' => $dictService->getNameByCode($extraField[$ext['field']], $ext['content']) ?? '',
                        ];
                    }
                    if ($ext['field'] == 'assembly_id') {
                        $before = $assemblyService->findById($ext['before']);
                        $content = $assemblyService->findById($ext['content']);
                        $ext = [
                            'field' => $ext['field'],
                            'before' => optional($before)->number,
                            'content' => optional($content)->number,
                        ];
                    }
                    return $ext;
                });
                $notice->extra = $extra->toArray();
            }

        }
        return [
            'name' => $name,
            'type' => $notice->extra_type,
            'content' => optional($notice->model)->content ?? $notice->content,
            'influence' => optional($notice->model)->influence,
            'concerns' => optional($notice->model)->concerns,
            'extra' => $notice->extra ?? []
        ];
    }

    /**
     * 删除消息关联
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User  $user
     * @param  string|int $id
     * @return void
     */
    public function deleteMessage(User $user, string|int $id)
    {
        DB::table("users_notices")->where([
            'notice_id' => $id,
            'user_id' => $user->id
        ])->delete();
    }

    /**
     * 处理审核消息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User           $user
     * @param  integer|string $id
     * @param  array          $data
     * @return void
     */
    public function approveMessage(User $user, int|string $id, array $data)
    {
        $notice = parent::findById($id);
        if ($notice->type != Notice::APPROVE || !in_array($notice->extra_type, ['examine', 'torque'])) {
            throw ValidationException::withMessages(['item.incorrect' => '参数错误']);
        }
        $pass = (bool) $data['pass'];
        $description = array_key_exists('description', $data) ? trim($data['description']) : null;
        if ($notice->extra_type == 'examine') {
            (new CommitApproveService)->passApprove($notice->model, $pass, $description);
        } else {
            (new TorqueChangeRecordService)->passApprove($notice->model, $pass);
        }
    }

    /**
     * 获取消息列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User  $user
     * @param  array $data
     * @return array
     */
    public function getList(User $user, array $data): array
    {
        if (!$user->is_super && !optional($user->department)->has_top) {
            $data['user_id'] = $user->id;
        }
        $condition = [
            'is_valid' => 'eq',
            'type' => 'eq',
            'user_id' => 'eq'
        ];
        parent::listQuery($data, $condition, [['type', '<', Notice::TASK]]);
        $result = parent::list();
        $result['items'] = $result['items']->map(function ($item) {
            return [
                'id' => $item->id,
                'user_id' => $item->user_id,
                'author' => optional(optional($item->user)->profile)->name ?? optional($item->user)->number,
                'title' => $item->title,
                'created_at' => $item->created_at,
                'type' => $item->type,
                'is_valid' => $item->is_valid,
                'is_sent' => $item->is_sent,
            ];
        });
        return $result;
    }

    /**
     * 批量撤回消息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @return void
     */
    public function retract(array $data)
    {
        foreach($data['ids'] as $id)
        {
            if($notice = parent::findById($id))
            {
                RetractNoticeToDepartment::dispatch($notice);
            }
        }
    }

    /**
     * 批量发布消息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @return void
     */
    public function push(array $data)
    {
        foreach($data['ids'] as $id)
        {
            if($notice = parent::findById($id))
            {
                PushNoticeToDepartment::dispatch($notice);
            }
        }
    }

    /**
     * 批量删除消息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User  $user
     * @param  array $data
     * @return void
     */
    public function batchDelete(User $user, array $data)
    {
        $sql = [
            [
                function ($query) use ($data) {
                    $query->whereIn('id', $data['ids']);
                }
            ]
        ];
        if (!$user->is_super && !optional($user->department)->has_top && !$user->departments->count()) {
            $sql[] = ['user_id', '=', $user->id];
        }
        parent::setQuery($sql);
        parent::getAll()->each(fn($item) => $item->delete());
        $this->clearCache();
    }

    /**
     * 添加消息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User  $user
     * @param  array $data
     * @return void
     */
    public function createByUser(User $user, array $data): void
    {
        $sql = array_merge([
            'user_id' => $user->id,
            'is_sent' => false,
            'from' => optional($user->profile)->name ?? $user->number
        ], $data);
        parent::create($sql);
    }

    public function detail(string $id): array
    {
        $item = parent::findById($id);
        $result = $item->toArray();
        return $result;
    }
}