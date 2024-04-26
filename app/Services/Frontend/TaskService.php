<?php
namespace App\Services\Frontend;

use App\Models\Account;
use App\Models\Examine;
use App\Models\TaskItem;
use App\Models\Task;
use App\Models\TrainingUser;
use App\Models\User;
use App\Packages\ImagePlus\ImagePlus;
use Carbon\Carbon;
use App\Services\Service;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * 员工服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class TaskService extends Service
{

    public ?string $className = Task::class;

    /**
     * 获取整车考核列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User $user
     * @return array
     */
    public function getListByService(User $user): array
    {
        parent::setQuery([
            ['user_id', '=', $user->id],
            ['type', '=', Task::TYPE_SERVICE]
        ]);
        $result = parent::list();
        $now = Carbon::now();
        $result['items'] = $result['items']->map(function ($item) use ($now) {
            return [
                'name' => $item->name,
                'id' => $item->id,
                'number' => $item->number,
                'period' => $item->period,
                'start_at' => $item->start_at,
                'end_at' => $item->end_at,
                'valid_at' => $item->valid_at,
                'overdue' => optional($item->valid_at)->lt($now) ?? false,
                'task_status' => $item->task_status,
                'plant' => $item->plant,
                'line' => $item->line,
                'engine' => $item->engine,
                'status' => $item->status,
                'assembly' => optional($item->assembly)->number
            ];
        });
        return $result;
    }

    /**
     * 在线考核-常规考核列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User $user
     * @return array
     */
    public function getListByInlineStandard(User $user): array
    {
        parent::setQuery([
            ['user_id', '=', $user->id],
            ['type', '=', Task::TYPE_INLINE],
        ]);
        parent::setHas([
            'examine' => function ($query) {
                $query->where('sub_type', Examine::SUB_TYPE_STANDARD);
            }
        ]);
        $result = parent::list();
        $now = Carbon::now();
        $result['items'] = $result['items']->map(function ($item) use ($now) {
            return [
                'name' => $item->name,
                'id' => $item->id,
                'number' => $item->number,
                'period' => $item->period,
                'start_at' => $item->start_at,
                'end_at' => $item->end_at,
                'valid_at' => $item->valid_at,
                'overdue' => optional($item->valid_at)->lt($now) ?? false,
                'task_status' => $item->task_status,
                'plant' => $item->plant,
                'line' => $item->line,
                'engine' => $item->engine,
                'status' => $item->status,
                'assembly' => optional($item->assembly)->number
            ];
        });
        return $result;
    }

    /**
     * 在线考核-涂胶考核列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User $user
     * @return array
     */
    public function getListByInlineGluing(User $user): array
    {
        parent::setQuery([
            ['user_id', '=', $user->id],
            ['type', '=', Task::TYPE_INLINE]
        ]);
        parent::setHas([
            'examine' => function ($query) {
                $query->where('sub_type', Examine::SUB_TYPE_GLUING);
            }
        ]);
        $result = parent::list();
        $now = Carbon::now();
        $result['items'] = $result['items']->map(function ($item) use ($now) {
            return [
                'name' => $item->name,
                'id' => $item->id,
                'number' => $item->number,
                'period' => $item->period,
                'start_at' => $item->start_at,
                'end_at' => $item->end_at,
                'valid_at' => $item->valid_at,
                'overdue' => optional($item->valid_at)->lt($now) ?? false,
                'task_status' => $item->task_status,
                'plant' => $item->plant,
                'line' => $item->line,
                'engine' => $item->engine,
                'status' => $item->status,
                'assembly' => optional($item->assembly)->number
            ];
        });
        return $result;
    }

    /**
     * 在线考核-动态考核列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User $user
     * @return array
     */
    public function getListByInlineDynamic(User $user): array
    {
        parent::setQuery([
            ['user_id', '=', $user->id],
            ['type', '=', Task::TYPE_INLINE]
        ]);
        parent::setHas([
            'examine' => function ($query) {
                $query->where('sub_type', Examine::SUB_TYPE_DYNAMIC);
            }
        ]);
        $result = parent::list();
        $now = Carbon::now();
        $result['items'] = $result['items']->map(function ($item) use ($now) {
            return [
                'name' => $item->name,
                'id' => $item->id,
                'number' => $item->number,
                'period' => $item->period,
                'start_at' => $item->start_at,
                'end_at' => $item->end_at,
                'valid_at' => $item->valid_at,
                'overdue' => optional($item->valid_at)->lt($now) ?? false,
                'task_status' => $item->task_status,
                'plant' => $item->plant,
                'line' => $item->line,
                'engine' => $item->engine,
                'status' => $item->status,
                'assembly' => optional($item->assembly)->number
            ];
        });
        return $result;
    }

    /**
     * 产品考核-考核记录
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User $user
     * @return array
     */
    public function getListByProduct(User $user, array $data): array
    {
        $condition = [
            'status' => ['eq', 'task_status']
        ];
        $extra = [
            ['user_id', '=', $user->id],
            ['type', '=', Task::TYPE_PRODUCT]
        ];
        parent::listQuery($data, $condition, $extra);
        $sub_type = intval($data['sub_type']) ? intval($data['sub_type']) : null;
        parent::setHas([
            'examine' => function ($query) use ($sub_type) {
                if ($sub_type) {
                    $query->where('sub_type', $sub_type);
                } else {
                    $query->where('sub_type', '>', 0);
                }
            }
        ]);
        $result = parent::list();
        $now = Carbon::now();
        $result['items'] = $result['items']->map(function ($item) use ($now) {
            return [
                'name' => $item->name,
                'id' => $item->id,
                'number' => $item->number,
                'period' => $item->period,
                'start_at' => $item->start_at,
                'end_at' => $item->end_at,
                'valid_at' => $item->valid_at,
                'overdue' => optional($item->valid_at)->lt($now) ?? false,
                'task_status' => $item->task_status,
                'plant' => $item->plant,
                'line' => $item->line,
                'engine' => $item->engine,
                'status' => $item->status,
                'assembly' => optional($item->assembly)->number
            ];
        });
        return $result;
    }

    /**
     * 在线考核-考核记录
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User $user
     * @return array
     */
    public function getListByInline(User $user, array $data): array
    {
        $condition = [
            'status' => ['eq', 'task_status']
        ];
        $extra = [
            ['user_id', '=', $user->id],
            ['type', '=', Task::TYPE_INLINE]
        ];
        parent::listQuery($data, $condition, $extra);
        $sub_type = intval($data['sub_type']) ? intval($data['sub_type']) : null;
        parent::setHas([
            'examine' => function ($query) use ($sub_type) {
                if ($sub_type) {
                    $query->where('sub_type', $sub_type);
                } else {
                    $query->where('sub_type', '>', 0);
                }
            }
        ]);
        $result = parent::list();
        $now = Carbon::now();
        $result['items'] = $result['items']->map(function ($item) use ($now) {
            return [
                'name' => $item->name,
                'id' => $item->id,
                'number' => $item->number,
                'period' => $item->period,
                'start_at' => $item->start_at,
                'end_at' => $item->end_at,
                'valid_at' => $item->valid_at,
                'overdue' => optional($item->valid_at)->lt($now) ?? false,
                'task_status' => $item->task_status,
                'plant' => $item->plant,
                'line' => $item->line,
                'engine' => $item->engine,
                'status' => $item->status,
                'assembly' => optional($item->assembly)->number
            ];
        });
        return $result;
    }

    /**
     * 产品考核-拆检考核列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User $user
     * @return array
     */
    public function getListByProductOverhaul(User $user): array
    {
        parent::setQuery([
            ['user_id', '=', $user->id],
            ['type', '=', Task::TYPE_PRODUCT]
        ]);
        parent::setHas([
            'examine' => function ($query) {
                $query->where('sub_type', Examine::SUB_TYPE_OVERHAUL);
            }
        ]);
        $result = parent::list();
        $now = Carbon::now();
        $result['items'] = $result['items']->map(function ($item) use ($now) {
            return [
                'name' => $item->name,
                'id' => $item->id,
                'number' => $item->number,
                'period' => $item->period,
                'start_at' => $item->start_at,
                'end_at' => $item->end_at,
                'valid_at' => $item->valid_at,
                'overdue' => optional($item->valid_at)->lt($now) ?? false,
                'task_status' => $item->task_status,
                'plant' => $item->plant,
                'line' => $item->line,
                'engine' => $item->engine,
                'status' => $item->status,
                'assembly' => optional($item->assembly)->number
            ];
        });
        return $result;
    }

    /**
     * 产品考核-装配考核列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User $user
     * @return array
     */
    public function getListByProductAssembling(User $user): array
    {
        parent::setQuery([
            ['user_id', '=', $user->id],
            ['type', '=', Task::TYPE_PRODUCT]
        ]);
        parent::setHas([
            'examine' => function ($query) {
                $query->where('sub_type', Examine::SUB_TYPE_ASSEMBLING);
            }
        ]);
        $result = parent::list();
        $now = Carbon::now();
        $result['items'] = $result['items']->map(function ($item) use ($now) {
            return [
                'name' => $item->name,
                'id' => $item->id,
                'number' => $item->number,
                'period' => $item->period,
                'start_at' => $item->start_at,
                'end_at' => $item->end_at,
                'valid_at' => $item->valid_at,
                'overdue' => optional($item->valid_at)->lt($now) ?? false,
                'task_status' => $item->task_status,
                'plant' => $item->plant,
                'line' => $item->line,
                'engine' => $item->engine,
                'status' => $item->status,
                'assembly' => optional($item->assembly)->number
            ];
        });
        return $result;
    }

    /**
     * 产品考核-动态考核列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User $user
     * @return array
     */
    public function getListByProductDynamic(User $user): array
    {
        parent::setQuery([
            ['user_id', '=', $user->id],
            ['type', '=', Task::TYPE_PRODUCT]
        ]);
        parent::setHas([
            'examine' => function ($query) {
                $query->where('sub_type', Examine::SUB_TYPE_DYNAMIC);
            }
        ]);
        $result = parent::list();
        $now = Carbon::now();
        $result['items'] = $result['items']->map(function ($item) use ($now) {
            return [
                'name' => $item->name,
                'id' => $item->id,
                'number' => $item->number,
                'period' => $item->period,
                'start_at' => $item->start_at,
                'end_at' => $item->end_at,
                'valid_at' => $item->valid_at,
                'overdue' => optional($item->valid_at)->lt($now) ?? false,
                'task_status' => $item->task_status,
                'plant' => $item->plant,
                'line' => $item->line,
                'engine' => $item->engine,
                'status' => $item->status,
                'assembly' => optional($item->assembly)->number
            ];
        });
        return $result;
    }

    /**
     * 扫码进入订单
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User  $user
     * @param  array $data
     * @return array
     */
    public function getProductEnter(User $user, array $data): array
    {
        $product = (new ProductService)->find(['number' => $data['number']]);

        return [];
    }

    /**
     * 获取详情-整车考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User   $user
     * @param  string $id
     * @return array
     */
    public function serviceDetail(User $user, string $id): array
    {
        $task = parent::find([
            'user_id' => $user->id,
            'id' => $id
        ]);
        $process = 0;
        $items = $task->items->map(function ($item) use (&$process) {
            if (!empty ($item->content)) {
                $process += $item->extra['process'];
            }
            $thumbnails = [];
            foreach ($item->getMedia(TaskItem::MEDIA_FILE) as $media) {
                $thumbnails[] = $media->getUrl();
            }
            return [
                'id' => $item->id,
                'content' => $item->content,
                'sort_order' => $item->sort_order,
                'remark' => $item->remark,
                'extra' => $item->extra,
                'thumbnails' => $thumbnails
            ];
        });
        $result = [
            'id' => $task->id,
            'number' => $task->number,
            'task_status' => $task->task_status,
            'plant' => $task->plant,
            'line' => $task->line,
            'engine' => $task->engine,
            'status' => $task->status,
            'assembly' => optional($task->assembly)->number,
            'progress' => $process,
            'items' => $items
        ];

        return $result;
    }

    /**
     * 获取详情-在线考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User   $user
     * @param  string $id
     * @return array
     */
    public function inlineDetail(User $user, string $id): array
    {
        $task = parent::find([
            'user_id' => $user->id,
            'id' => $id
        ]);
        $process = 0;
        $items = $task->items->map(function ($item) use (&$process) {
            if (!empty ($item->content)) {
                $process += $item->extra['process'];
            }
            $thumbnails = [];
            foreach ($item->getMedia(TaskItem::MEDIA_FILE) as $media) {
                $thumbnails[] = $media->getUrl();
            }
            return [
                'id' => $item->id,
                'content' => $item->content,
                'sort_order' => $item->sort_order,
                'remark' => $item->remark,
                'extra' => $item->extra,
                'thumbnails' => $thumbnails
            ];
        });
        $result = [
            'id' => $task->id,
            'number' => $task->number,
            'task_status' => $task->task_status,
            'plant' => $task->plant,
            'line' => $task->line,
            'engine' => $task->engine,
            'status' => $task->status,
            'assembly' => optional($task->assembly)->number,
            'progress' => $process,
            'items' => $items
        ];

        return $result;
    }

    /**
     * 获取详情-产品考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User   $user
     * @param  string $id
     * @return array
     */
    public function productDetail(User $user, string $id): array
    {
        $task = parent::find([
            'user_id' => $user->id,
            'id' => $id
        ]);
        $process = 0;
        $items = $task->items->map(function ($item) use (&$process) {
            if (!empty ($item->content)) {
                $process += $item->extra['process'];
            }
            $thumbnails = [];
            foreach ($item->getMedia(TaskItem::MEDIA_FILE) as $media) {
                $thumbnails[] = $media->getUrl();
            }
            return [
                'id' => $item->id,
                'content' => $item->content,
                'sort_order' => $item->sort_order,
                'remark' => $item->remark,
                'extra' => $item->extra,
                'thumbnails' => $thumbnails
            ];
        });
        $result = [
            'id' => $task->id,
            'number' => $task->number,
            'task_status' => $task->task_status,
            'plant' => $task->plant,
            'line' => $task->line,
            'engine' => $task->engine,
            'status' => $task->status,
            'assembly' => optional($task->assembly)->number,
            'progress' => $process,
            'items' => $items
        ];

        return $result;
    }

    /**
     * 提交在线考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User    $user
     * @param  string  $id
     * @param  array   $data
     * @return integer
     */
    public function inlineUpdate(User $user, string $id, array $data): int
    {
        $task = parent::find([
            'user_id' => $user->id,
            'id' => $id
        ]);
        $item = $task->items->where('id', $data['item_id'])->first();
        $content = $data['content'];
        if ($content == 1) {
            $item->fill([
                'content' => 1,
                'user_id' => $user->id,
                'remark' => $data['remark']
            ]);
            $item->save();
            if (array_key_exists('image', $data) && $data['image']) {
                foreach ($data['image'] as $image) {
                    $item->addMedia($image)->toMediaCollection(TaskItem::MEDIA_IMAGE);
                }
            }
            return $item->extra['process'];
        }
        $item->fill([
            'content' => 0,
            'user_id' => $user->id,
            'remark' => $data['remark']
        ]);
        $item->save();
        (new IssueService)->makeByInline($user, $task, $item, $data);
        return 0;
    }

    /**
     * 提交在线考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User    $user
     * @param  string  $id
     * @param  array   $data
     * @return integer
     */
    public function productUpdate(User $user, string $id, array $data): int
    {
        $task = parent::find([
            'user_id' => $user->id,
            'id' => $id
        ]);
        $item = $task->items->where('id', $data['item_id'])->first();
        $content = $data['content'];
        if ($content == 1) {
            $item->fill([
                'content' => 1,
                'user_id' => $user->id,
                'remark' => $data['remark']
            ]);
            $item->save();
            if (array_key_exists('image', $data) && $data['image']) {
                foreach ($data['image'] as $image) {
                    $item->addMedia($image)->toMediaCollection(TaskItem::MEDIA_IMAGE);
                }
            }
            return $item->extra['process'];
        }
        $item->fill([
            'content' => 0,
            'user_id' => $user->id,
            'remark' => $data['remark']
        ]);
        $item->save();
        (new IssueService)->makeByProduct($user, $task, $item, $data);
        return 0;
    }



    /**
     * 提交在线考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User    $user
     * @param  string  $id
     * @param  array   $data
     * @return integer
     */
    public function serviceUpdate(User $user, string $id, array $data): int
    {
        $task = parent::find([
            'user_id' => $user->id,
            'id' => $id
        ]);
        $item = $task->items->where('id', $data['item_id'])->first();
        $content = $data['content'];
        if ($content == 1) {
            $item->fill([
                'content' => 1,
                'user_id' => $user->id,
                'remark' => $data['remark']
            ]);
            $item->save();
            if (array_key_exists('image', $data) && $data['image']) {
                foreach ($data['image'] as $image) {
                    $item->addMedia($image)->toMediaCollection(TaskItem::MEDIA_IMAGE);
                }
            }
            return $item->extra['process'];
        }
        $item->fill([
            'content' => 0,
            'user_id' => $user->id,
            'remark' => $data['remark']
        ]);
        $item->save();
        (new IssueService)->makeByService($user, $task, $item, $data);
        return 0;
    }

    public function serviceAll(User $user, string $id, array $data): array
    {
        $result = [];

        return $result;
    }

    public function inlineAll(User $user, string $id, array $data): array
    {
        $task = parent::find([
            'user_id' => $user->id,
            'id' => $id
        ]);

        $canAll = $task->items->filter(fn($n) => optional($n->extra)['type'] == 2)->values();
        if ($canAll->count()) {
            throw ValidationException::withMessages(['can_all' => __('task.inline_all.cannot_all')]);
        }

        $result = [];

        return $result;
    }

    public function productAll(User $user, string $id, array $data): array
    {
        $result = [];

        return $result;
    }

    public function serviceReset(User $user, string $id, array $data): array
    {
        $result = [];

        return $result;
    }

    public function inlineReset(User $user, string $id, array $data): array
    {
        $result = [];

        return $result;
    }

    public function productReset(User $user, string $id, array $data): array
    {
        $result = [];

        return $result;
    }

    public function serviceItemDetail(User $user, string $id, array $data): array
    {
        $result = [];

        return $result;
    }

    public function inlineItemDetail(User $user, string $id, array $data): array
    {
        $result = [];

        return $result;
    }

    public function productItemDetail(User $user, string $id, array $data): array
    {
        $result = [];

        return $result;
    }

}