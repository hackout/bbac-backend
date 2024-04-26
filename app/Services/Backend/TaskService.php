<?php
namespace App\Services\Backend;

use App\Models\ExamineItem;
use App\Models\Task;
use App\Models\TaskItem;
use App\Packages\Task\OrderNumber;
use App\Services\Service;

/**
 * 考核单服务
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
            Task::TYPE_SERVICE => 'service_order_rules',
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
        $conditions = [
            'keyword' => ['search', ['name']],
            'date' => ['datetime_range', 'created_at'],
            'examine_type' => ['eq', 'type'],
            'task_status' => 'eq'
        ];
        parent::listQuery($data, $conditions);
        return parent::list([
            'id',
            'name',
            'examine_id',
            'type',
            'plant',
            'line',
            'engine',
            'status',
            'assembly_id',
            'task_status',
            'number',
            'period',
            'start_at',
            'items_count',
        ]);
    }

    /**
     * 查询选项列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @return array
     */
    public function getOption(array $data): array
    {
        $conditions = [
            'keyword' => ['search', ['name']],
            'date' => ['datetime_range', 'created_at'],
            'examine_type' => ['eq', 'type'],
            'task_status' => 'eq'
        ];
        parent::listQuery($data, $conditions,[['user_id','IS NULL']]);
        return parent::getAll([
            'id as value',
            'name',
            'type',
            'number',
            'period',
        ])->toArray();
    }


    public function createByNumber(array $data)
    {
        $sql = [
            'assembly_id' => $data['assembly_id'],
            'name' => $data['name'],
            'examine_id' => $data['examine_id'],
            'type' => $data['type'],
            'plant' => $data['plant'],
            'line' => $data['line'],
            'engine' => $data['engine'],
            'status' => $data['status'],
            'period' => $data['period'],
            'task_status' => Task::STATUS_UNDER
        ];
        $number = (int) $data['number'];
        $rules = (new DictService)->getOptionByCode($this->getNumberRuleType()[$data['type']]);
        $examine = (new ExamineService)->findById($data['examine_id']);
        $rule = $rules->where('value', $examine->sub_type)->value('name');
        $orderNumber = new OrderNumber($rule);
        $assembly = (new AssemblyService)->findById($data['assembly_id']);
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
        for ($i = 0; $i < $number; $i++) {
            $sql['number'] = $orderNumber->makeOrder($data['engine'], $data['line'], '', $data['status'], $data['plant'], $assembly->number, $i);
            if (parent::create($sql)) {
                foreach ($items as $_item) {
                    $thumbnails = $_item['thumbnails'];
                    unset($_item['thumbnails']);
                    if ($item = $this->item->items()->create($_item)) {
                        if ($thumbnails->count()) {
                            $thumbnails->each(function ($thumbnail) use ($item) {
                                if(file_exists($thumbnail->getPath()))
                                {
                                    $item->addMedia($thumbnail->getPath())->toMediaCollection(TaskItem::MEDIA_FILE);
                                }
                            });
                        }
                    }
                }
            }
        }
    }

}