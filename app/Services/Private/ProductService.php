<?php
namespace App\Services\Private;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Product;
use App\Services\Service;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * 发动机服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class ProductService extends Service
{
    public ?string $className = Product::class;

    /**
     * 添加导入数据
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @return void
     */
    public function importCreate(array $data)
    {
        if (!parent::find(['number' => $data['number']])) {
            parent::create($data);
        }
    }

    /**
     * 发动机录入
     * 将发动机/电池号符合规则的录入系统
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $number
     * @return void
     */
    public function createOrUpdateByNumber(string $number)
    {
        if (!parent::find(['number' => $number])) {
            list($assemblyNumber, $productNumber) = array_pad(explode("  ", $number), 2, null);
            if ($assemblyNumber && $productNumber) {
                if ($assembly = (new AssemblyService)->find(['number' => $assemblyNumber])) {
                    $sql = [
                        'line' => $assembly->line,
                        'plant' => $assembly->plant,
                        'engine' => $assembly->type,
                        'status' => $assembly->status,
                        'assembly_id' => $assembly->id,
                        'number' => $productNumber
                    ];
                    parent::create($sql);
                }
            }
        }
    }
}