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

}