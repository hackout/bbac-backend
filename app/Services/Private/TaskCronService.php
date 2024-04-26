<?php
namespace App\Services\Private;

use Carbon\Carbon;
use App\Models\Task;
use App\Models\TaskCron;
use App\Models\User;
use App\Services\Service;

/**
 * 任务配置服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class TaskCronService extends Service
{

    public ?string $className = TaskCron::class;

    public function makeTask()
    {
        parent::setQuery(['is_valid' => true]);
        $result = collect([]);
        $now = Carbon::now();
        parent::getAll([
            'id',
            'days',
            'yield',
            'yield_unit'
        ])->each(function ($item) use (&$result) {
            foreach ($item['days'] as $_item) {
                $result->push([
                    'id' => $item['id'],
                    'yield' => $item['yield'],
                    'yield_unit' => $item['yield_unit'],
                    'start' => Carbon::parse($_item['date'][0]),
                    'end' => Carbon::parse($_item['date'][1]),
                    'day' => $_item['day'],
                    'unit' => $_item['unit']
                ]);
            }
        });
        $items = $result->filter(fn($n)=>$n['start']->lte($now) && $n['end']->gt($now))->values()->toArray();
        foreach($items as $item)
        {
            if($taskCron = parent::findById($item['id']))
            {
                $days = $now->diffInDays($item['start']);
                $dayNum = (int) ($days / $item['day']);
                $divider = (int) ($days % $item['day']);
                if(!$divider)
                {
                    $total = $dayNum * $item['unit'] * $item['yield'] * $item['yield_count'];
                    (new TaskService)->checkTotalAndCreate($taskCron,$total);
                }
            }
        }
    }

}