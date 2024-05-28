<?php
namespace App\Services\Private;

use App\Models\ExamineItem;
use App\Models\WorkItem;
use App\Models\TaskItem;
use App\Packages\Task\OrderNumber;
use Carbon\Carbon;
use App\Models\Task;
use App\Models\TaskCron;
use App\Services\Service;

/**
 * 任务配置服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class TaskService extends Service
{

    public ?string $className = Task::class;

    public function getNumberRuleType()
    {
        return [
            Task::TYPE_INLINE => 'inline_order_rules',
            Task::TYPE_PRODUCT => 'product_order_rules',
            Task::TYPE_VEHICLE => 'service_order_rules',
        ];
    }

    public function checkTotalAndCreate(TaskCron $taskCron, $total = 0)
    {
        $count = $taskCron->tasks->count();
        $last = $total - $count;


        $sql = [
            'assembly_id' => $taskCron->assembly_id,
            'name' => $taskCron->name,
            'examine_id' => $taskCron->examine_id,
            'type' => $taskCron->type,
            'plant' => $taskCron->plant,
            'line' => $taskCron->line,
            'engine' => $taskCron->engine,
            'status' => $taskCron->status,
            'period' => $taskCron->period,
            'task_status' => Task::STATUS_UNDER
        ];
        $rules = (new DictService)->getOptionByCode($this->getNumberRuleType()[$taskCron->type]);
        $examine = (new ExamineService)->findById($taskCron->examine_id);
        $rule = $rules->where('value', $examine->sub_type)->value('name');
        $orderNumber = new OrderNumber($rule);
        $assembly = (new AssemblyService)->findById($taskCron->assembly_id);
        $sql['original_examine'] = [
            'commit_id' => $examine->commit_id,
            'version' => $examine->version,
            'name' => $examine->name,
            'description' => $examine->description,
            'engine' => $examine->engine,
            'period' => $examine->period,
            'is_valid' => $examine->is_valid,
            'status' => $examine->status,
            'type' => $examine->type,
            'sub_type' => $examine->sub_type
        ];
        $items = $examine->items->map(function ($item) {
            return [
                'examine_item_id' => $item->id,
                'sort_order' => $item->sort_order,
                'remark' => null,
                'content' => null,
                'thumbnails' => $item->getMedia(ExamineItem::MEDIA_FILE),
                'extra' => [
                    'station' => $item->station,
                    'sub_station' => $item->sub_station,
                    'name_zh' => $item->name_zh,
                    'name_en' => $item->name_en,
                    'content_zh' => $item->content_zh,
                    'content_en' => $item->content_en,
                    'standard_zh' => $item->standard_zh,
                    'standard_en' => $item->standard_en,
                    'eye_zh' => $item->eye_zh,
                    'eye_en' => $item->eye_en,
                    'number' => $item->number,
                    'special' => $item->special,
                    'gluing' => $item->gluing,
                    'bolt_number' => $item->bolt_number,
                    'bolt_model' => $item->bolt_model,
                    'bolt_type' => $item->bolt_type,
                    'bolt_status' => $item->bolt_status,
                    'bolt_close' => $item->bolt_close,
                    'lower_limit' => $item->lower_limit,
                    'upper_limit' => $item->upper_limit,
                    'unit' => $item->unit,
                    'is_scan' => $item->is_scan,
                    'is_camera' => $item->is_camera,
                    'part_number' => $item->part_number,
                    'process' => $item->process,
                    'type' => $item->type,
                    'sort_order' => $item->sort_order,
                    'options' => $item->options->map(function ($option) {
                        return [
                            'sort_order' => $option->sort_order,
                            'name_zh' => $option->name_zh,
                            'name_en' => $option->name_en
                        ];
                    })
                ]
            ];
        })->toArray();
        for ($i = 0; $i < $last; $i++) {
            $sql['number'] = $orderNumber->makeOrder($taskCron->engine, $taskCron->line, '', $taskCron->status, $taskCron->plant, $assembly->number, $i);
            if (parent::create($sql)) {
                foreach ($items as $_item) {
                    $thumbnails = $_item['thumbnails'];
                    unset($_item['thumbnails']);
                    if ($item = $this->item->items()->create($_item)) {
                        if ($thumbnails->count()) {
                            $thumbnails->each(function ($thumbnail) use ($item) {
                                $item->addMedia($thumbnail->getPath())->toMediaCollection(TaskItem::MEDIA_FILE);
                            });
                        }
                    }
                }
            }
        }
    }

    /**
     * 更新任务单绑定人员
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  WorkItem $workItem
     * @return void
     */
    public function addUserByWorkItem(WorkItem $workItem)
    {
        parent::update($workItem->task_id, ['user_id' => $workItem->user_id, 'task_status' => Task::STATUS_TYPESET]);
    }

    public function checkStartByItem(TaskItem $taskItem)
    {
        if (!$taskItem->task->start_at) {
            $period = $taskItem->task->period;
            $hours = intval($period);
            $minutes = intval(($period - $hours) * 60);
            $sql = [
                'start_at' => Carbon::now(),
                'valid_at' => Carbon::now()->addHours($hours)->addMinutes($minutes)
            ];
            parent::update($taskItem->task_id, $sql);
        }
    }
}