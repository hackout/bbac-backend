<?php
namespace App\Services\Backend;

use Str;
use App\Models\Task;
use App\Models\TaskItem;
use App\Models\User;
use Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Packages\Department\DepartmentRole;
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
            Task::TYPE_VEHICLE => 'service_order_rules',
        ];
    }
    public function getServiceType()
    {
        return [
            Task::TYPE_INLINE => new ExamineInlineService,
            Task::TYPE_PRODUCT => new ExamineProductService,
            Task::TYPE_VEHICLE => new ExamineVehicleService,
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
        if (!$examine) {
            throw ValidationException::withMessages(['id.exists' => '考核模板不存在']);
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

    /**
     * 获取整车服务考核订单
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User  $user
     * @param  array $data
     * @return array
     */
    public function getVehicleList(User $user, array $data): array
    {
        if (!DepartmentRole::checkVehicle($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }
        $condition = [
            'keyword' => ['search', ['name', 'id', 'number']],
            'engine' => 'eq',
            'status' => 'eq'
        ];
        parent::listQuery($data, $condition, [['type', '=', Task::TYPE_VEHICLE]]);
        $result = parent::list();
        $result['items'] = $result['items']->map(function (Task $item) {
            return [
                'id' => $item->id,
                'engine' => $item->engine,
                'user_id' => $item->user_id,
                'auditor' => optional(optional($item->user)->profile)->name ?? optional($item->user)->number,
                'assembly_id' => $item->assembly_id,
                'assembly' => optional($item->assembly)->number,
                'eb_number' => $item->eb_number,
                'finding' => optional($item->extra)['defect_category'],
                'remark' => $item->remark,
                'created_at' => $item->created_at,
                'status' => $item->status
            ];
        });
        return $result;
    }

    /**
     * 删除整车服务考核订单
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User   $user
     * @param  string $id
     * @return void
     */
    public function deleteVehicle(User $user, string $id)
    {
        if (!DepartmentRole::checkVehicle($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }
        parent::delete($id);
    }

    /**
     * 获取整车考核单
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User   $user
     * @param  string $id
     * @return array
     */
    public function getVehicleDetail(User $user, string $id): array
    {
        if (!DepartmentRole::checkVehicle($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }
        $item = parent::findById($id);
        return [
            'id' => $item->id,
            'engine' => $item->engine,
            'user_id' => $item->user_id,
            'auditor' => optional(optional($item->user)->profile)->name ?? optional($item->user)->number,
            'assembly_id' => $item->assembly_id,
            'assembly' => optional($item->assembly)->number,
            'eb_number' => $item->eb_number,
            'finding' => optional($item->extra)['defect_category'],
            'level' => optional($item->extra)['defect_level'],
            'eight' => optional($item->extra)['eight'],
            'resp' => optional($item->extra)['resp'],
            'description' => optional($item->extra)['description'],
            'next' => optional($item->extra)['next'],
            'purpose' => optional($item->extra)['purpose'],
            'plant' => $item->plant,
            'line' => $item->line,
            'remark' => $item->remark,
            'created_at' => $item->created_at,
            'status' => $item->status,
            'thumbnails' => $item->thumbnails,

        ];
    }

    public function updateVehicle(User $user, string $id, array $data)
    {
        if (!DepartmentRole::checkVehicle($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }
        $item = parent::findById($id);
        $sql = [
            'line' => $data['line'],
            'engine' => $data['engine'],
        ];

        if (array_key_exists('eb_number', $data)) {
            $sql['eb_number'] = $data['eb_number'];
        }
        $hasExtra = false;
        $extra = $item->extra ?? [];
        if (array_key_exists('purpose', $data)) {
            $extra['purpose'] = $data['purpose'];
            $hasExtra = true;
        }
        if (array_key_exists('eight', $data)) {
            $extra['eight'] = $data['eight'];
            $hasExtra = true;
        }
        if (array_key_exists('level', $data)) {
            $extra['defect_level'] = $data['level'];
            $hasExtra = true;
        }
        if (array_key_exists('description', $data)) {
            $extra['description'] = $data['description'];
            $hasExtra = true;
        }
        if (array_key_exists('resp', $data)) {
            $extra['resp'] = $data['resp'];
            $hasExtra = true;
        }
        if (array_key_exists('next', $data)) {
            $extra['next'] = $data['next'];
            $hasExtra = true;
        }
        if ($hasExtra) {
            $sql['extra'] = $extra;
        }
        if (parent::update($id, $sql)) {
            $thumbnails = array_key_exists('thumbnails', $data) ? (array) $data['thumbnails'] : [];
            $media = array_key_exists('media', $data) ? (array) $data['media'] : [];
            if ($media) {
                Media::whereIn('uuid', $media)->get()->each(fn($n) => $n->delete());
            }
            if ($thumbnails) {
                foreach ($thumbnails as $file) {
                    if (!Str::isUuid($file['uuid'])) {
                        if ($this->item->addMedia(Storage::path('public/images/' . $file['uuid']))->toMediaCollection(Task::MEDIA_FILE)) {
                            Storage::delete('public/images/' . $file['uuid']);
                        }
                    }
                }
            }
        }
    }

    /**
     * 上传图片到考核单
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  UploadedFile $fileBag
     * @return array
     */
    public function upload(UploadedFile $fileBag): array
    {
        $result = [];
        $file = Storage::putFile('public/images', $fileBag);
        $result = [
            'url' => Storage::url($file),
            'name' => $fileBag->getClientOriginalName(),
            'uuid' => Str::afterLast($file, '/')
        ];
        return $result;
    }
}