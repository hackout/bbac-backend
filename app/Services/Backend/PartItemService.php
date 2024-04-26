<?php
namespace App\Services\Backend;

use App\Models\User;
use App\Models\PartItem;
use App\Services\Service;
use App\Packages\Department\DepartmentRole;
use Illuminate\Validation\ValidationException;

/**
 * 零件清单
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class PartItemService extends Service
{

    public ?string $className = PartItem::class;

    /**
     * 查询数据列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @return array
     */
    public function getListById(User $user, string $id): array
    {
        if (!DepartmentRole::checkProduct($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }
        parent::setQuery(['part_id' => $id]);
        return parent::getAll()->map(function (PartItem $item) {
            return [
                'id' => $item->id,
                'user_id' => $item->user_id,
                'user' => optional(optional($item->user)->profile)->name ?? optional($item->user)->number,
                'product_id' => $item->product_id,
                'product' => optional($item->product)->number,
                'assembly_id' => $item->assembly_id,
                'assembly' => optional($item->assembly)->number,
                'updated_at' => $item->updated_at,
                'number' => $item->number
            ];
        })->toArray();
    }

}