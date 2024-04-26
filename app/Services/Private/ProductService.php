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
        if(!parent::find(['number'=>$data['number']]))
        {
            parent::create($data);
        }
    }
}