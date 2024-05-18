<?php
namespace App\Services\Backend;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Work;
use App\Models\WorkItem;
use App\Services\Service;
use Illuminate\Validation\ValidationException;

/**
 * 任务配置服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class WorkService extends Service
{

    public ?string $className = Work::class;

    private function getWorkType(): array
    {
        return [
            WorkItem::TYPE_NORMAL,
            WorkItem::TYPE_DYNAMIC,
            WorkItem::TYPE_OTHER,
            WorkItem::TYPE_CAMPAIGN,
            WorkItem::TYPE_TRAINING
        ];
    }


    private function getSubType(): array
    {
        return [
            WorkItem::TYPE_PTIME,
            WorkItem::TYPE_HOLIDAY
        ];
    }


    /**
     * 查询数据列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @return array
     */
    public function getList(array $data): array
    {
        $department = (new DepartmentService)->findById($data['department_id']);
        $month = Carbon::parse($data['month'])->firstOfMonth();
        $date = [];
        $items = collect([]);
        $workItemService = new WorkItemService;

        for ($i = 0; $i < $month->clone()->lastOfMonth()->day; $i++) {
            if ($i) {
                $now = $month->clone()->addDays($i);
            } else {
                $now = $month->clone();
            }
            $date[] = [
                'label' => $now->day,
                'disabled' => $now->isSaturday() || $now->isSunday(),
                'date' => $now
            ];
        }
        if ($department->users->count()) {
            $userIds = $department->users->pluck('id')->toArray();
            $workItemService->setQuery(function ($query) use ($userIds) {
                $query->whereIn('user_id', $userIds);
            });
            $works = $workItemService->getAll();
            $workTypes = $this->getWorkType();
            $subTypes = $this->getSubType();
            $array = [
                [12],
                WorkItem::TYPE_NORMAL => [0, 1],
                WorkItem::TYPE_DYNAMIC => [2, 3],
                WorkItem::TYPE_OTHER => [4, 5],
                WorkItem::TYPE_CAMPAIGN => [6, 7],
                WorkItem::TYPE_TRAINING => [8, 9],
                WorkItem::TYPE_PTIME => [10],
                WorkItem::TYPE_HOLIDAY => [11],
            ];
            $that = $this;
            $department->users->each(function ($user) use ($works, &$items, $date, $that, $workTypes, $subTypes, $array) {
                $dates = $that->makeOne($user, $date);
                $total = 0;
                $addTotal = 0;
                $subTotal = 0;
                foreach ($date as $rs) {
                    $_total = 0;
                    $_works = $works->filter(fn($n) => $n->user_id == $user->id && !$n->work_date->diffInDays($rs['date']))->values();
                    foreach ($_works as $work) {
                        $keys = $array[$work->type];
                        if (count($keys) == 2) {
                            $dates[$keys[0]][$rs['label']] = optional($work->task)->name ?? $work->content;
                            $dates[$keys[1]][$rs['label']] = $work->period ?? 0;
                            $total = bcadd($total, $dates[$keys[1]][$rs['label']], 2);
                            $_total = bcadd($_total, $dates[$keys[1]][$rs['label']], 2);
                        }
                        if (count($keys) == 1) {
                            $dates[$keys[0]][$rs['label']] = $work->period ?? 0;
                            if ($keys[0] == 10) {
                                $total = bcadd($total, $dates[$keys[0]][$rs['label']], 2);
                                $_total = bcadd($_total, $dates[$keys[0]][$rs['label']], 2);
                                $addTotal = bcadd($addTotal, $dates[$keys[0]][$rs['label']], 2);
                            } else {
                                $total = bcsub($total, $dates[$keys[0]][$rs['label']], 2);
                                $_total = bcadd($_total, $dates[$keys[0]][$rs['label']], 2);
                                $subTotal = bcadd($subTotal, $dates[$keys[0]][$rs['label']], 2);
                            }
                        }
                    }
                    $dates[12][$rs['label']] = $_total;
                }
                $dates[10]['sub'] = $addTotal;
                $dates[11]['sub'] = $subTotal;
                $dates[12]['sub'] = $total;
                $items = $items->concat($dates);
            });
        }
        return [
            'dates' => $date,
            'items' => $items
        ];
    }

    private function makeOne(User $user, array $date)
    {
        $name = optional($user->profile)->name ?? $user->number;
        $detailDate = [];
        $periodDate = [];
        foreach ($date as $r) {
            $detailDate[$r['label']] = null;
            $periodDate[$r['label']] = 0;
        }
        return [
            array_merge($detailDate, [
                'name' => $name,
                'type' => WorkItem::TYPE_NORMAL,
                'sub' => WorkItem::TYPE_NORMAL,
                'mark' => 'detail',
                'user_id' => $user->id,
            ]),

            array_merge($periodDate, [
                'name' => $name,
                'type' => null,
                'sub' => WorkItem::TYPE_NORMAL,
                'mark' => 'period',
                'user_id' => $user->id,
            ]),
            array_merge($detailDate, [
                'name' => $name,
                'type' => WorkItem::TYPE_DYNAMIC,
                'sub' => WorkItem::TYPE_DYNAMIC,
                'mark' => 'detail',
                'user_id' => $user->id,
            ]),
            array_merge($periodDate, [
                'name' => $name,
                'type' => null,
                'sub' => WorkItem::TYPE_DYNAMIC,
                'mark' => 'period',
                'user_id' => $user->id,
            ]),
            array_merge($detailDate, [
                'name' => $name,
                'type' => WorkItem::TYPE_OTHER,
                'sub' => WorkItem::TYPE_OTHER,
                'mark' => 'detail',
                'user_id' => $user->id,
            ]),
            array_merge($periodDate, [
                'name' => $name,
                'type' => null,
                'sub' => WorkItem::TYPE_OTHER,
                'mark' => 'period',
                'user_id' => $user->id,
            ]),
            array_merge($detailDate, [
                'name' => $name,
                'type' => WorkItem::TYPE_CAMPAIGN,
                'sub' => WorkItem::TYPE_CAMPAIGN,
                'mark' => 'detail',
                'user_id' => $user->id,
            ]),
            array_merge($periodDate, [
                'name' => $name,
                'type' => null,
                'sub' => WorkItem::TYPE_CAMPAIGN,
                'mark' => 'period',
                'user_id' => $user->id,
            ]),
            array_merge($detailDate, [
                'name' => $name,
                'type' => WorkItem::TYPE_TRAINING,
                'sub' => WorkItem::TYPE_TRAINING,
                'mark' => 'detail',
                'user_id' => $user->id,
            ]),
            array_merge($periodDate, [
                'name' => $name,
                'type' => null,
                'sub' => WorkItem::TYPE_TRAINING,
                'mark' => 'period',
                'user_id' => $user->id,
            ]),
            array_merge($periodDate, [
                'name' => $name,
                'type' => WorkItem::TYPE_PTIME,
                'sub' => 0,
                'mark' => '0',
                'user_id' => $user->id,
            ]),
            array_merge($periodDate, [
                'name' => $name,
                'type' => WorkItem::TYPE_HOLIDAY,
                'sub' => 0,
                'mark' => '0',
                'user_id' => $user->id,
            ]),
            array_merge($periodDate, [
                'name' => '总工时',
                'type' => 0,
                'sub' => null,
                'mark' => 0,
                'user_id' => $user->id,
            ])
        ];
    }

    /**
     * 分配任务
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @return void
     */
    public function createWork(array $data)
    {
        if (!$work = parent::find(['day' => Carbon::parse($data['date']),'user_id' => $data['user_id']])) {
            $sql = [
                'day' => Carbon::parse($data['date']),
                'user_id' => $data['user_id'],
                'period' => 0,
                'available_period' => (new SystemConfigService)->getValueByCode('default_period')
            ];
            if (parent::create($sql)) {
                $work = $this->item;
            }
        }
        $available = (float) bcsub($work->available_period, $work->period, 2);
        if (WorkItem::TYPE_PTIME != $data['type']) {
            if ($available < (float) $data['period']) {
                throw ValidationException::withMessages(['period.out' => '剩余工时不足,剩余可分配:' . $available]);
            }
        }
        $task = null;
        if ($data['type'] == WorkItem::TYPE_NORMAL || $data['type'] == WorkItem::TYPE_DYNAMIC) {
            $task = (new TaskService)->findById($data['task_id']);
        }
        $extra = null;
        if ($task) {
            $extra = [
                'id' => $task->id,
                'name' => $task->name,
                'type' => $task->type,
                'plant' => $task->plant,
                'line' => $task->line,
                'engine' => $task->engine,
                'status' => $task->status,
                'assembly_id' => $task->assembly_id,
                'number' => $task->number,
                'original_examine' => $task->original_examine,
            ];
        }
        $period = (float) $data['period'];
        $type = (int) $data['type'];
        $status = WorkItem::STATUS_PENDING;
        if ($type == WorkItem::TYPE_HOLIDAY) {
            $period = $period * -1;
            $status = WorkItem::STATUS_ADVANCE;
        }
        $sql = [
            'user_id' => $data['user_id'],
            'task_id' => optional($task)->id,
            'work_id' => $work->id,
            'content' => array_key_exists('content',$data) ? trim($data['content']) : optional($task)->name,
            'type' => $type,
            'status' => $status,
            'period' => $period,
            'extra' => $extra,
            'work_date' => Carbon::parse($data['date']),
        ];
        (new WorkItemService)->create($sql);
    }
}