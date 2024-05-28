<?php
namespace App\Services\Frontend;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Part;
use App\Models\PartItem;
use App\Services\Service;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * 字典服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class PartItemService extends Service
{
    public ?string $className = PartItem::class;

    /**
     * 增加零件扫码
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $part_id
     * @param  string $assembly_id
     * @param  string $user_id
     * @param  string $product_id
     * @param  string $part_number
     * @return void
     */
    public function createByPart(string $part_id,string $assembly_id,string $user_id,string $product_id,string $part_number)
    {
        $sql = [
            'part_id' => $part_id,
            'assembly_id' => $assembly_id,
            'user_id' => $user_id,
            'product_id' => $product_id,
            'part_number' => $part_number,
        ];
        parent::create($sql);
    }
}