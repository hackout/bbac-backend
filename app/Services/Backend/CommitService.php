<?php
namespace App\Services\Backend;

use App\Imports\CommitImportSheet;
use App\Models\User;
use App\Models\Commit;
use App\Packages\CommitPlus\CommitPlus;
use App\Services\Service;
use App\Traits\ExportTemplateTrait;
use App\Traits\ImportTemplateTrait;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * 版本服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class CommitService extends Service
{
    use ExportTemplateTrait, ImportTemplateTrait;

    public ?string $className = Commit::class;

    /**
     * 获取对应考核类型
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return array
     */
    public function getTypeOptions(): array
    {
        return [
            'inline' => Commit::TYPE_INLINE,
            'product' => Commit::TYPE_PRODUCT,
            'service' => Commit::TYPE_VEHICLE
        ];
    }

    /**
     * 在线考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return array
     */
    public function getInlineOptions(): array
    {
        return [
            Commit::SUB_TYPE_STANDARD,
            Commit::SUB_TYPE_GLUING,
            Commit::SUB_TYPE_DYNAMIC
        ];
    }

    /**
     * 产品考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return array
     */
    public function getProductOptions(): array
    {
        return [
            Commit::SUB_TYPE_OVERHAUL,
            Commit::SUB_TYPE_ASSEMBLING,
            Commit::SUB_TYPE_DYNAMIC
        ];
    }

    /**
     * 整车考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return array
     */
    public function getServiceOptions(): array
    {
        return [
            Commit::SUB_TYPE_DYNAMIC
        ];
    }

    /**
     * 查询历史版本
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array      $data
     * @return Collection
     */
    public function getOption(array $data): Collection
    {
        $conditions = [
            'engine' => 'eq',
            'type' => 'eq',
            'sub_type' => 'eq'
        ];
        if(array_key_exists('type',$data) && $data['type'] && !is_numeric($data['type']))
        {
            $data['type'] = $this->getTypeOptions()[$data['type']];
        }
        parent::listQuery($data, $conditions);
        return parent::getAll([
            'id as value',
            'name',
            'version',
            'period',
            'description'
        ]);
    }

    /**
     * 创建版本记录
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User   $user
     * @param  string $type
     * @param  array  $data
     * @return void
     */
    public function createByType(User $user, string $type, array $data)
    {
        $type = $this->getTypeOptions()[$type];
        $sql = array_merge([
            'type' => $type,
            'user_id' => $user->id,
            'author_id' => $user->id,
            'is_valid' => false,
            'status' => Commit::STATUS_DRAFT
        ], $data);
        if ($data['parent_id']) {
            $commit = parent::findById($data['parent_id']);
            if ($commit) {
                $sql['author_id'] = $commit->author_id;
                $sql['examine_id'] = $commit->examine_id;
            }
        }
        parent::create($sql);
    }

    public function updateByLast(Commit $commit, array $data)
    {
        $sql = [
            'name' => $data['name'],
            'engine' => $data['engine'],
            'version' => $data['version'],
            'description' => $data['description'],
            'period' => $data['period'],
        ];
        parent::update($commit->id, $sql);
    }

    public function getList(string $type, array $data): array
    {
        $condition = [
            'keyword' => ['search', ['name', 'description']],
            'type' => 'eq',
            'sub_type' => 'eq',
            'engine' => 'eq',
            'status' => 'eq'
        ];
        $data['type'] = $this->getTypeOptions()[$type];
        parent::listQuery($data, $condition);
        $result = parent::list();
        $result['items'] = $result['items']->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'user_id' => $item->user_id,
                'user' => optional(optional($item->user)->profile)->name ?? optional($item->user)->number,
                'author_id' => $item->author_id,
                'author' => optional(optional($item->author)->profile)->name ?? optional($item->author)->number,
                'engine' => $item->engine,
                'version' => $item->version,
                'last_version' => optional($item->lasted)->version,
                'description' => $item->description,
                'sub_type' => $item->sub_type,
                'status' => $item->status,
                'period' => $item->period,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
                'can_export' => $item->items->count() > 0,
                'can_approve' => $item->items->count() > 0 && $item->status == Commit::STATUS_DRAFT,
            ];
        });
        return $result;
    }

    public function detail(string $id): array
    {
        $item = parent::findById($id);
        $result = [
            'id' => $item->id,
            'name' => $item->name,
            'user_id' => $item->user_id,
            'user' => optional(optional($item->user)->profile)->name ?? optional($item->user)->number,
            'author_id' => $item->author_id,
            'author' => optional(optional($item->author)->profile)->name ?? optional($item->author)->number,
            'engine' => $item->engine,
            'version' => $item->version,
            'last_version' => optional($item->lasted)->version,
            'description' => $item->description,
            'sub_type' => $item->sub_type,
            'status' => $item->status,
            'period' => $item->period,
            'created_at' => $item->created_at,
            'updated_at' => $item->updated_at,
            'can_export' => $item->items->count() > 0,
            'can_approve' => $item->items->count() > 0 && $item->status == Commit::STATUS_DRAFT,
            'items_count' => $item->items->count(),
            'approves' => $item->approves->map(function ($approve) {
                return [
                    'id' => $approve->id,
                    'user_id' => $approve->user_id,
                    'user' => optional(optional($approve->user)->profile)->name ?? optional($approve->user)->number,
                    'messenger_id' => $approve->messenger_id,
                    'messenger' => optional(optional($approve->messenger)->profile)->name ?? optional($approve->messenger)->number,
                    'approver_id' => $approve->approver_id,
                    'approver' => optional(optional($approve->approver)->profile)->name ?? optional($approve->approver)->number,
                    'content' => $approve->content,
                    'influence' => $approve->influence,
                    'concerns' => $approve->concerns,
                    'description' => $approve->description,
                    'status' => $approve->status,
                    'created_at' => $approve->created_at,
                    'updated_at' => $approve->updated_at,
                ];
            })
        ];

        return $result;
    }

    /**
     * 根据类型获取导入模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $type
     * @return BinaryFileResponse
     */
    public function getTemplateByType(string $type): BinaryFileResponse
    {
        switch ($type) {
            case 'inline':
                $this->template = 'CommitInline';
                break;
            case 'product':
                $this->template = 'CommitProduct';
                break;
            case 'service':
                $this->template = 'CommitService';
                break;
        }
        return $this->downloadImportTemplate();
    }

    /**
     * 按类型导入数据
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $type
     * @param  UploadedFile $file
     * @return void
     */
    public function importByType(string $type, UploadedFile $file)
    {
        Excel::import(new CommitImportSheet($this->getTypeOptions()[$type]), $file);
    }

    /**
     * 获取模板变更项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $id
     * @return array
     */
    public function getChanged(string $id): array
    {
        return (new CommitPlus(parent::findById($id)))->getChanged();
    }
}