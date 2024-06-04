<?php
namespace App\Services\Backend;

use App\Models\ExamineProduct;
use App\Models\IssueVehicle;
use App\Models\Task;
use App\Models\TaskItem;
use App\Models\User;
use App\Models\Department;
use App\Services\Service;
use App\Traits\ExportTemplateTrait;
use App\Traits\ImportTemplateTrait;
use Illuminate\Validation\ValidationException;

/**
 * 控制台服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class DashboardService
{
    public function dashboard():array
    {
        $result = [
            'inline' => [
                'processing' => Task::where('type',Task::TYPE_INLINE)->whereNull('end_at')->count(),
                'finish' => Task::where('type',Task::TYPE_INLINE)->whereNotNull('end_at')->count(),
                'dynamic' => Task::where('type',Task::TYPE_INLINE)->where('original_examine->type',ExamineProduct::TYPE_DYNAMIC)->count(),
            ],
            'product' => [
                'processing' => Task::where('type',Task::TYPE_PRODUCT)->whereNull('end_at')->count(),
                'finish' => Task::where('type',Task::TYPE_PRODUCT)->whereNotNull('end_at')->count(),
                'dynamic' => Task::where('type',Task::TYPE_PRODUCT)->where('original_examine->type',ExamineProduct::TYPE_DYNAMIC)->count(),
            ],
            'vehicle' => [
                'processing' => IssueVehicle::where('block_status','!=',IssueVehicle::BLOCK_STATUS_SUCCESS)->count(),
                'finish' => IssueVehicle::where('block_status',IssueVehicle::BLOCK_STATUS_SUCCESS)->count(),
                'dynamic' => Task::where('type',Task::TYPE_VEHICLE)->count(),
            ],
        ];

        return $result;
    }
}