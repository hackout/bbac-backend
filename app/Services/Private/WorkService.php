<?php
namespace App\Services\Private;

use Carbon\Carbon;
use App\Models\User;
use App\Models\WorkItem;
use App\Models\Work;
use App\Services\Service;

/**
 * 任务单子项
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class WorkService extends Service
{

    public ?string $className = Work::class;

    /**
     * 更新每日员工状态
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return void
     */
    public function makePreDay()
    {
        $today = Carbon::today();
        $userIds = User::select("id")->get()->pluck("id")->toArray();
        $works = Work::where('day', $today)->whereIn('user_id', $userIds)->select('user_id')->get()->pluck('user_id')->toArray();
        if (count($userIds) != count($works)) {
            foreach ($userIds as $user_id) {
                if (!in_array($user_id, $works)) {
                    parent::create([
                        'day' => $today,
                        'user_id' => $user_id,
                        'period' => 0,
                        'available_period' => (new SystemConfigService)->getValueByCode('default_period')
                    ]);
                }
            }
        }
    }

    /**
     * 增加工时统计
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  WorkItem $workItem
     * @return void
     */
    public function addPeriodByItem(WorkItem $workItem)
    {
        $work = $workItem->work;
        $sql = [
            'period' => bcadd($work->period, $workItem->period, 2)
        ];
        if ($workItem->type == WorkItem::TYPE_PTIME) {
            $sql = [
                'available_period' => bcadd($work->available_period, $workItem->period, 2)
            ];
        }
        if ($workItem->type == WorkItem::TYPE_HOLIDAY) {
            $sql = [
                'available_period' => bcsub($work->available_period, $workItem->period, 2)
            ];
        }
        parent::update($work->id, $sql);
    }
}