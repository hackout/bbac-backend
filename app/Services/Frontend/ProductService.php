<?php
namespace App\Services\Frontend;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Product;
use App\Services\Service;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * 字典服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class ProductService extends Service
{
    public ?string $className = Product::class;

    /**
     * 更新或添加车辆
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array   $data
     * @return Product
     */
    public function updateByIssue(array $data): Product
    {
        $status = $data['delay'] ? Product::STATUS_VERIFY : Product::STATUS_OVERDUE;
        if (!$item = parent::find(['number' => $data['number']])) {
            $sql = [
                'assembly_id' => null,
                'shift' => $data['shift'],
                'plant' => $data['plant'],
                'eb_type' => $data['eb_type'],
                'pn_number' => $data['pn_number'],
                'number' => $data['number'],
                'beginning_at' => Carbon::now(),
                'inlined_at' => null,
                'qc_at' => null,
                'assembled_at' => null,
                'status' => $status,
            ];
            if (parent::create($sql)) {
                $item = $this->item;
            }
        }else{
            $sql = [
                
                'shift' => $data['shift'],
                'plant' => $data['plant'],
                'eb_type' => $data['eb_type'],
                'pn_number' => $data['pn_number'],
                'status' => $status,
            ];
            parent::update($item->id,$sql);
        }

        return $item;
    }
}