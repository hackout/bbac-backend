<?php
namespace App\Services\Frontend;

use Carbon\Carbon;
use App\Models\Part;
use App\Models\Product;
use App\Services\Service;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * 字典服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class PartService extends Service
{
    public ?string $className = Part::class;
}