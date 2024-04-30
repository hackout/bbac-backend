<?php
namespace App\Services\Backend;

use App\Models\Task;
use App\Models\TaskItem;
use App\Services\Service;
use App\Models\ExamineInlineItem;
use App\Models\ExamineProductItem;
use App\Models\ExamineVehicleItem;
use App\Packages\Task\OrderNumber;
use Illuminate\Validation\ValidationException;

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
    public function getServiceType()
    {
        return [
            Task::TYPE_INLINE => new ExamineInlineService,
            Task::TYPE_PRODUCT => new ExamineProductService,
            Task::TYPE_SERVICE => new ExamineVehicleService,
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
        parent::listQuery($data, $conditions, [['user_id', 'IS NULL']]);
        return parent::getAll([
            'id as value',
            'name',
            'type',
            'number',
            'period',
        ])->toArray();
    }

    /**
     * 创建考核任务
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @return void
     * 
     * @throws ValidationException
     */
    public function createByNumber(array $data)
    {
        $examine = ($this->getServiceType()[$data['type']])->findById($data['examine_id']);
        if(!$examine)
        {
            throw ValidationException::withMessages(['id.exists'=>'考核模板不存在']);
        }
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
        $rule = $rules->where('value', $data['type'] == 3 ? 0 : $examine->type)->value('name');
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
            'type' => $examine->type
        ];
        $items = $examine->items->map(function (ExamineInlineItem|ExamineProductItem|ExamineVehicleItem $item) {
            return [
                'examine_item_id' => $item->id,
                'sort_order' => $item->sort_order,
                'remark' => null,
                'content' => null,
                'thumbnails' => $item->getMedia(Task::MEDIA_FILE),
                'extra' => $item->toArray()
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
                                if (file_exists($thumbnail->getPath())) {
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