<?php
namespace App\Services\Backend;

use App\Models\User;
use App\Services\Service;
use App\Models\CommitProduct;
use Illuminate\Support\Collection;
use App\Traits\ExportTemplateTrait;
use App\Traits\ImportTemplateTrait;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CommitProductImport;
use App\Packages\CommitPlus\CommitPlus;
use App\Packages\Department\DepartmentRole;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * 产品考核-考核模板-历史版本
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class CommitProductService extends Service
{
    use ExportTemplateTrait, ImportTemplateTrait;

    public ?string $className = CommitProduct::class;

    /**
     * 获取选项列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array      $data
     * @return Collection
     */
    public function getOption(array $data): Collection
    {
        $condition = [
            'engine' => 'eq'
        ];
        $extra = [
            ['is_valid', '=', true]
        ];
        parent::listQuery($data, $condition, $extra);
        return parent::getAll([
            'id as value',
            'name',
            'version',
            'engine'
        ]);
    }

    /**
     * 获取列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User  $user
     * @param  array $data
     * @return array
     */
    public function getList(User $user, array $data): array
    {
        if (!DepartmentRole::checkProduct($user)) {
            return [];
        }
        $condition = [
            'keyword' => ['search', ['name', 'description']],
            'engine' => 'eq',
            'type' => 'eq',
            'status' => 'eq'
        ];
        parent::listQuery($data, $condition);
        $result = parent::list();
        $result['items'] = $result['items']->map(function (CommitProduct $item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'user_id' => $item->user_id,
                'user' => optional(optional($item->user)->profile)->name ?? optional($item->user)->number,
                'author_id' => $item->author_id,
                'author' => optional(optional($item->author)->profile)->name ?? optional($item->author)->number,
                'engine' => $item->engine,
                'version' => $item->version,
                'last_version' => optional($item->parent)->version,
                'description' => $item->description,
                'status' => $item->status,
                'type' => $item->type,
                'period' => (float) $item->period,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
                'can_export' => $item->items->count() > 0,
                'can_approve' => $item->items->count() > 0 && $item->status == CommitProduct::STATUS_DRAFT,
            ];
        });
        return $result;
    }

    public function createByDepartment(User $user, array $data)
    {
        if (!DepartmentRole::checkProduct($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }
        $sql = array_merge([
            'user_id' => $user->id,
            'author_id' => $user->id,
            'is_valid' => false,
            'status' => CommitProduct::STATUS_DRAFT
        ], $data);
        $commit = null;
        if ($data['parent_id']) {
            $commit = parent::findById($data['parent_id']);
        }
        if ($commit) {
            $sql['author_id'] = $commit->author_id;
            $sql['examine_product_id'] = $commit->examine_product_id;
        }
        parent::create($sql);
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
            'last_version' => optional($item->parent)->version,
            'description' => $item->description,
            'status' => $item->status,
            'period' => (float) $item->period,
            'created_at' => $item->created_at,
            'updated_at' => $item->updated_at,
            'can_export' => $item->items->count() > 0,
            'can_approve' => $item->items->count() > 0 && $item->status == CommitProduct::STATUS_DRAFT,
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
     * 按类型导入数据
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $type
     * @param  UploadedFile $file
     * @return void
     */
    public function importFile(User $user, UploadedFile $file)
    {
        Excel::import(new CommitProductImport($user, $file), $file);
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

    /**
     * 上传记录模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $type
     * @param  integer      $engine
     * @param  UploadedFile $file
     * @return void
     */
    public function uploadTemplate(string $type,int $engine,UploadedFile $file)
    {
        $engine = (new DictService())->getNameByCode('engine_type',$engine);
        $fileName = $engine.ucfirst($type).'.'.$file->getClientOriginalExtension();
        $file->move(resource_path('templates'),$fileName);
    }
}
