<?php
namespace App\Services\Backend;

use App\Services\Service;
use App\Models\ExamineProduct;

/**
 * 产品考核-模板服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class ExamineProductService extends Service
{

    public ?string $className = ExamineProduct::class;

}