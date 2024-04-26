<?php
namespace App\Services\Backend;

use App\Models\TorqueItemDetail;
use App\Models\User;
use App\Models\TorqueChangeRecord;
use App\Services\Service;
use Illuminate\Support\Collection;

/**
 * 扭矩详情服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class TorqueItemDetailService extends Service
{
    public ?string $className = TorqueItemDetail::class;

}