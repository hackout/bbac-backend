<?php
namespace App\Services\Backend;

use App\Models\IssueVehicleLog;
use App\Services\Service;
use Illuminate\Support\Collection;

/**
 * 整车服务-问题日志服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class IssueVehicleLogService extends Service
{
    public ?string $className = IssueVehicleLog::class;

    /**
     * 通过问题ID获取日志
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string     $id
     * @return Collection
     */
    public function getListById(string $id): Collection
    {
        parent::setQuery(['issue_vehicle_id' => $id]);
        return parent::getAll()->map(function (IssueVehicleLog $item) {
            return [
                'id' => $item->id,
                'user' => optional(optional($item->user)->profile)->name ?? optional($item->user)->number,
                'code' => $item->code,
                'extra' => $item->extra,
                'created_at' => $item->created_at
            ];
        });
    }
}