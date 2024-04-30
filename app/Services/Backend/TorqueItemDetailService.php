<?php
namespace App\Services\Backend;

use App\Services\Service;
use App\Models\TorqueItemDetail;

/**
 * 扭矩详情服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class TorqueItemDetailService extends Service
{
    public ?string $className = TorqueItemDetail::class;

}