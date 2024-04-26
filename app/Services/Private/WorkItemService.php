<?php
namespace App\Services\Private;

use Carbon\Carbon;
use App\Models\CommitApprove;
use App\Models\WorkItem;
use App\Models\TorqueChangeRecord;
use App\Services\Service;
use Illuminate\Support\Facades\DB;

/**
 * 任务单子项
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class WorkItemService extends Service
{

    public ?string $className = WorkItem::class;

    /**
     * 检查超期任务
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return void
     */
    public function checkAdvance()
    {
        parent::setQuery([
            ['status','=', WorkItem::STATUS_PROCESSING]
        ]);
        $result = parent::getAll();
        $now = Carbon::now();
        $result->each(function($work_item) use($now){
            if($work_item->task->valid_at->lt($now))
            {
                parent::setValue($work_item->id,'status',WorkItem::STATUS_TIMEOUT);
            }
        });
    }
}