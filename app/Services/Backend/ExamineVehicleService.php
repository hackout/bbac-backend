<?php
namespace App\Services\Backend;

use App\Services\Service;
use App\Models\ExamineVehicle;

/**
 * 整车服务-模板服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class ExamineVehicleService extends Service
{

    public ?string $className = ExamineVehicle::class;

}