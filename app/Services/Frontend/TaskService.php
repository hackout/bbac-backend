<?php
namespace App\Services\Frontend;

use App\Models\Account;
use App\Models\ExamineInline;
use App\Models\Product;
use App\Models\ExamineProduct;
use App\Models\ExamineVehicle;
use App\Models\WorkItem;
use App\Models\TaskItem;
use App\Models\IssueProduct;
use App\Models\Task;
use App\Models\CommitProduct;
use App\Models\User;
use App\Packages\Department\DepartmentRole;
use App\Packages\Excel\ExcelReader;
use App\Packages\ImagePlus\ImagePlus;
use Carbon\Carbon;
use App\Services\Service;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * 任务单服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class TaskService extends Service
{

    public ?string $className = Task::class;

    /**
     * 整车服务-考核单列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User $user
     * @return array
     */
    public function getVehicleList(User $user): array
    {
        if (!DepartmentRole::checkVehicle($user)) {
            return ['total' => 0, 'items' => []];
        }
        parent::setQuery([
            ['user_id', '=', $user->id],
            ['type', '=', Task::TYPE_VEHICLE]
        ]);
        $result = parent::list();
        $now = Carbon::now();
        $result['items'] = $result['items']->map(function ($item) use ($now) {
            return [
                'name' => $item->name,
                'id' => $item->id,
                'number' => $item->number,
                'period' => $item->period,
                'start_at' => $item->start_at,
                'end_at' => $item->end_at,
                'valid_at' => $item->valid_at,
                'overdue' => optional($item->valid_at)->lt($now) ?? false,
                'lastTime' => optional($item->valid_at)->diffInSeconds($now, true),
                'task_status' => $item->task_status,
                'plant' => $item->plant,
                'line' => $item->line,
                'engine' => $item->engine,
                'status' => $item->status,
                'assembly' => optional($item->assembly)->number
            ];
        });
        return $result;
    }


    /**
     * 获取详情-整车考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User   $user
     * @param  string $id
     * @return array
     */
    public function detailVehicle(User $user, string $id): array
    {
        if (!DepartmentRole::checkVehicle($user)) {
            throw ValidationException::withMessages(['permission' => __('issue_vehicle.missing_permission')]);
        }
        $task = parent::findById($id);
        $process = 0;
        $items = $task->items->map(function ($item) use (&$process) {
            $thumbnails = [];
            foreach ($item->getMedia(TaskItem::MEDIA_FILE) as $media) {
                $thumbnails[] = $media->getUrl();
            }
            return [
                'id' => $item->id,
                'content' => $item->content ? json_decode($item->content, true) : [],
                'sort_order' => $item->sort_order,
                'remark' => $item->remark,
                'extra' => $item->extra,
                'thumbnails' => $thumbnails
            ];
        });
        $workItem = WorkItem::where(['user_id' => $user->id, 'task_id' => $id])->first();
        if (!$workItem) {
            throw ValidationException::withMessages(['permission' => __('issue_vehicle.missing_permission')]);
        }
        if ($workItem->status == WorkItem::STATUS_PENDING) {
            $now = Carbon::now();
            $task->fill([
                'task_status' => Task::STATUS_PROCESSING,
                'start_at' => $now,
                'valid_at' => $now->clone()->addHours($task->period)
            ]);
            $task->save();
            $workItem->fill([
                'status' => WorkItem::STATUS_PROCESSING,
                'work_date' => Carbon::now()
            ]);
            $workItem->save();
            $this->clearCache();
        }
        $result = [
            'id' => $task->id,
            'number' => $task->number,
            'task_status' => $task->task_status,
            'plant' => $task->plant,
            'line' => $task->line,
            'remark' => $task->remark,
            'thumbnails' => $task->thumbnails,
            'engine' => $task->engine,
            'status' => $workItem->status,
            'start_date' => $workItem->work_date,
            'assembly' => optional($task->assembly)->number,
            'progress' => $process,
            'items' => $items
        ];

        return $result;
    }


    /**
     * 提交整车服务-动态考核单
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User   $user
     * @param  string $id
     * @param  array  $data
     * @return void
     */
    public function vehicleUpdate(User $user, string $id, array $data)
    {
        if (!DepartmentRole::checkVehicle($user)) {
            throw ValidationException::withMessages(['permission' => __('issue_vehicle.missing_permission')]);
        }
        $task = parent::find([
            'type' => Task::TYPE_VEHICLE,
            'id' => $id
        ]);
        $now = Carbon::now();
        $timeStatus = $now->diffInMinutes($task->valid_at, true) > 0 ? WorkItem::STATUS_ADVANCE : WorkItem::STATUS_ENDED;
        $sql = [
            'remark' => array_key_exists('remark', $data) ? $data['remark'] : $task->remark,
            'end_at' => $now,
            'task_status' => Task::STATUS_COMPLETED
        ];
        $status = (bool) $data['status'];
        $task->fill($sql);
        if ($task->save()) {
            if (array_key_exists('image', $data) && $data['image'] instanceof UploadedFile) {
                $task->addMedia($data['image'])->toMediaCollection(Task::MEDIA_FILE);
            }
            foreach ($data['number'] as $rs) {
                if ($item = $task->items->where('id', $rs['id'])->first()) {
                    $item->fill([
                        'content' => json_encode([
                            'number' => $rs['number'],
                            'status' => $status
                        ])
                    ]);
                    $item->save();
                }
            }
            $workList = WorkItem::where(['user_id' => $user->id, 'task_id' => $task->id])->get();
            $workList->each(fn(WorkItem $workItem) => $workItem->setFinish($timeStatus));
        }
        $this->clearCache();

    }

    /**
     * 在线考核-常规考核列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User $user
     * @return array
     */
    public function getListByInlineStandard(User $user): array
    {
        parent::setQuery([
            ['user_id', '=', $user->id],
            ['type', '=', Task::TYPE_INLINE],
        ]);
        parent::setHas([
            'examine' => function ($query) {
                $query->where('sub_type', ExamineInline::TYPE_STANDARD);
            }
        ]);
        $result = parent::list();
        $now = Carbon::now();
        $result['items'] = $result['items']->map(function ($item) use ($now) {
            return [
                'name' => $item->name,
                'id' => $item->id,
                'number' => $item->number,
                'period' => $item->period,
                'start_at' => $item->start_at,
                'end_at' => $item->end_at,
                'valid_at' => $item->valid_at,
                'overdue' => optional($item->valid_at)->lt($now) ?? false,
                'task_status' => $item->task_status,
                'plant' => $item->plant,
                'line' => $item->line,
                'engine' => $item->engine,
                'status' => $item->status,
                'assembly' => optional($item->assembly)->number
            ];
        });
        return $result;
    }

    /**
     * 在线考核-涂胶考核列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User $user
     * @return array
     */
    public function getListByInlineGluing(User $user): array
    {
        parent::setQuery([
            ['user_id', '=', $user->id],
            ['type', '=', Task::TYPE_INLINE]
        ]);
        parent::setHas([
            'examine' => function ($query) {
                $query->where('type', ExamineInline::TYPE_GLUING);
            }
        ]);
        $result = parent::list();
        $now = Carbon::now();
        $result['items'] = $result['items']->map(function ($item) use ($now) {
            return [
                'name' => $item->name,
                'id' => $item->id,
                'number' => $item->number,
                'period' => $item->period,
                'start_at' => $item->start_at,
                'end_at' => $item->end_at,
                'valid_at' => $item->valid_at,
                'overdue' => optional($item->valid_at)->lt($now) ?? false,
                'task_status' => $item->task_status,
                'plant' => $item->plant,
                'line' => $item->line,
                'engine' => $item->engine,
                'status' => $item->status,
                'assembly' => optional($item->assembly)->number
            ];
        });
        return $result;
    }

    /**
     * 在线考核-动态考核列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User $user
     * @return array
     */
    public function getListByInlineDynamic(User $user): array
    {
        if (!DepartmentRole::checkInline($user)) {
            return ['total' => 0, 'items' => []];
        }
        $sql = [
            ['user_id', '=', $user->id],
            ['type', '=', Task::TYPE_INLINE]
        ];
        $examineIdList = ExamineInline::where('type', ExamineInline::TYPE_DYNAMIC)->select('id')->get()->pluck('id')->toArray();

        $sql[] = [
            function ($query) use ($examineIdList) {
                if ($examineIdList) {
                    $query->whereIn('examine_id', $examineIdList);
                } else {
                    $query->whereIn('examine_id', ['-1']);
                }
            }
        ];
        parent::setQuery($sql);
        $result = parent::list();
        $now = Carbon::now();
        $result['items'] = $result['items']->map(function ($item) use ($now) {
            return [
                'name' => $item->name,
                'id' => $item->id,
                'number' => $item->number,
                'period' => $item->period,
                'start_at' => $item->start_at,
                'end_at' => $item->end_at,
                'valid_at' => $item->valid_at,
                'overdue' => optional($item->valid_at)->lt($now) ?? false,
                'task_status' => $item->task_status,
                'plant' => $item->plant,
                'line' => $item->line,
                'engine' => $item->engine,
                'status' => $item->status,
                'assembly' => optional($item->assembly)->number
            ];
        });
        return $result;
    }

    /**
     * 产品考核-考核记录
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User $user
     * @return array
     */
    public function getListByProduct(User $user, array $data): array
    {
        $condition = [
            'status' => ['eq', 'task_status']
        ];
        $extra = [
            ['user_id', '=', $user->id],
            ['type', '=', Task::TYPE_PRODUCT]
        ];
        parent::listQuery($data, $condition, $extra);
        $sub_type = intval($data['sub_type']) ? intval($data['sub_type']) : null;
        parent::setHas([
            'examine' => function ($query) use ($sub_type) {
                if ($sub_type) {
                    $query->where('sub_type', $sub_type);
                } else {
                    $query->where('sub_type', '>', 0);
                }
            }
        ]);
        $result = parent::list();
        $now = Carbon::now();
        $result['items'] = $result['items']->map(function ($item) use ($now) {
            return [
                'name' => $item->name,
                'id' => $item->id,
                'number' => $item->number,
                'period' => $item->period,
                'start_at' => $item->start_at,
                'end_at' => $item->end_at,
                'valid_at' => $item->valid_at,
                'overdue' => optional($item->valid_at)->lt($now) ?? false,
                'task_status' => $item->task_status,
                'plant' => $item->plant,
                'line' => $item->line,
                'engine' => $item->engine,
                'status' => $item->status,
                'assembly' => optional($item->assembly)->number
            ];
        });
        return $result;
    }

    /**
     * 在线考核-考核记录
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User $user
     * @return array
     */
    public function getListByInline(User $user, array $data): array
    {
        $condition = [
            'status' => ['eq', 'task_status']
        ];
        $extra = [
            ['user_id', '=', $user->id],
            ['type', '=', Task::TYPE_INLINE]
        ];
        parent::listQuery($data, $condition, $extra);
        $sub_type = intval($data['sub_type']) ? intval($data['sub_type']) : null;
        parent::setHas([
            'examine' => function ($query) use ($sub_type) {
                if ($sub_type) {
                    $query->where('sub_type', $sub_type);
                } else {
                    $query->where('sub_type', '>', 0);
                }
            }
        ]);
        $result = parent::list();
        $now = Carbon::now();
        $result['items'] = $result['items']->map(function ($item) use ($now) {
            return [
                'name' => $item->name,
                'id' => $item->id,
                'number' => $item->number,
                'period' => $item->period,
                'start_at' => $item->start_at,
                'end_at' => $item->end_at,
                'valid_at' => $item->valid_at,
                'overdue' => optional($item->valid_at)->lt($now) ?? false,
                'task_status' => $item->task_status,
                'plant' => $item->plant,
                'line' => $item->line,
                'engine' => $item->engine,
                'status' => $item->status,
                'assembly' => optional($item->assembly)->number
            ];
        });
        return $result;
    }

    /**
     * 产品考核-拆检考核列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User $user
     * @return array
     */
    public function getProductOverhaulList(User $user): array
    {
        if (!DepartmentRole::checkProduct($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }
        parent::setQuery([
            ['user_id', '=', $user->id],
            ['type', '=', Task::TYPE_PRODUCT],
            ['original_examine->type', '=', ExamineProduct::TYPE_OVERHAUL]
        ]);
        $result = parent::list();
        $now = Carbon::now();
        $result['items'] = $result['items']->map(function ($item) use ($now) {
            return [
                'name' => $item->name,
                'id' => $item->id,
                'number' => $item->number,
                'period' => $item->period,
                'start_at' => $item->start_at,
                'end_at' => $item->end_at,
                'valid_at' => $item->valid_at,
                'overdue' => optional($item->valid_at)->lt($now) ?? false,
                'lastTime' => optional($item->valid_at)->diffInSeconds($now, true),
                'task_status' => $item->task_status,
                'plant' => $item->plant,
                'line' => $item->line,
                'engine' => $item->engine,
                'status' => $item->status,
                'assembly' => optional($item->assembly)->number
            ];
        });
        return $result;
    }

    /**
     * 产品考核-装配考核列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User $user
     * @return array
     */
    public function getProductAssemblingList(User $user): array
    {
        parent::setQuery([
            ['user_id', '=', $user->id],
            ['type', '=', Task::TYPE_PRODUCT],
            ['original_examine->type', '=', ExamineProduct::TYPE_ASSEMBLING]
        ]);
        $result = parent::list();
        $now = Carbon::now();
        $result['items'] = $result['items']->map(function ($item) use ($now) {
            return [
                'name' => $item->name,
                'id' => $item->id,
                'number' => $item->number,
                'period' => $item->period,
                'start_at' => $item->start_at,
                'end_at' => $item->end_at,
                'valid_at' => $item->valid_at,
                'overdue' => optional($item->valid_at)->lt($now) ?? false,
                'lastTime' => optional($item->valid_at)->diffInSeconds($now, true),
                'task_status' => $item->task_status,
                'plant' => $item->plant,
                'line' => $item->line,
                'engine' => $item->engine,
                'status' => $item->status,
                'assembly' => optional($item->assembly)->number
            ];
        });
        return $result;
    }

    /**
     * 产品考核-动态考核列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User $user
     * @return array
     */
    public function getProductDynamicList(User $user): array
    {
        parent::setQuery([
            ['user_id', '=', $user->id],
            ['type', '=', Task::TYPE_PRODUCT],
            ['original_examine->type', '=', ExamineProduct::TYPE_DYNAMIC]
        ]);
        $result = parent::list();
        $now = Carbon::now();
        $result['items'] = $result['items']->map(function ($item) use ($now) {
            return [
                'name' => $item->name,
                'id' => $item->id,
                'number' => $item->number,
                'period' => $item->period,
                'start_at' => $item->start_at,
                'end_at' => $item->end_at,
                'valid_at' => $item->valid_at,
                'overdue' => optional($item->valid_at)->lt($now) ?? false,
                'lastTime' => optional($item->valid_at)->diffInSeconds($now, true),
                'task_status' => $item->task_status,
                'plant' => $item->plant,
                'line' => $item->line,
                'engine' => $item->engine,
                'status' => $item->status,
                'assembly' => optional($item->assembly)->number
            ];
        });
        return $result;
    }

    /**
     * 扫码进入订单
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User  $user
     * @param  array $data
     * @return void
     * 
     * @throws ValidationException
     */
    public function getProductEnter(User $user, array $data): void
    {
        if (!DepartmentRole::checkProduct($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }
        $item = parent::findById($data['id']);
        $assemblyService = new AssemblyService();
        $productService = new ProductService();
        $product = $productService->find(['number' => $data['number']]);
        if (!$product) {
            list($assemblyNumber) = array_pad(explode(" ", $data['number']), 2, null);
            $assembly = $assemblyService->find(['number' => $assemblyNumber]);
            if (!$assembly) {
                $assemblySql = [
                    'type' => $item->engine,
                    'plant' => $item->plant,
                    'line' => $item->line,
                    'status' => 1,
                    'number' => $assemblyNumber,
                    'remark' => '',
                ];
                if ($assemblyService->create($assemblySql)) {
                    $assembly = $assemblyService->item;
                } else {
                    throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
                }
            }
            $insertSql = [
                'line' => $assembly->line,
                'plant' => $assembly->plant,
                'engine' => $assembly->type,
                'status' => $assembly->status,
                'assembly_id' => $assembly->id,
                'number' => $data['number'],
                'beginning_at' => Carbon::now(),
                'examine_at' => null,
                'qc_at' => null,
                'assembled_at' => null,
            ];
            if ($productService->create($insertSql)) {
                $product = $productService->item;
            } else {
                throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
            }
        }
        if ($item->eb_number && $item->eb_number != $data['number']) {
            throw ValidationException::withMessages(['permission' => '当前考核提供的发动机号不正确']);
        }
        if (!$item->eb_number) {
            $item->fill(['eb_number' => $data['number']]);
            $item->save();
            $this->clearCacheData();
        }
    }

    /**
     * 确定提交订单
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User  $user
     * @param  string $id
     * @return void
     * 
     * @throws ValidationException
     */
    public function saveProduct(User $user, string $id): void
    {
        if (!DepartmentRole::checkProduct($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }
        $sql = [
            'end_at' => Carbon::now(),
            'status' => Task::STATUS_COMPLETED,
            'task_status' => Task::STATUS_COMPLETED
        ];
        parent::update($id,$sql);
    }

    /**
     * 获取详情-整车考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User   $user
     * @param  string $id
     * @return array
     */
    public function serviceDetail(User $user, string $id): array
    {
        $task = parent::find([
            'user_id' => $user->id,
            'id' => $id
        ]);
        $process = 0;
        $items = $task->items->map(function ($item) use (&$process) {
            if (!empty ($item->content)) {
                $process += $item->extra['process'];
            }
            $thumbnails = [];
            foreach ($item->getMedia(TaskItem::MEDIA_FILE) as $media) {
                $thumbnails[] = $media->getUrl();
            }
            return [
                'id' => $item->id,
                'content' => $item->content,
                'sort_order' => $item->sort_order,
                'remark' => $item->remark,
                'extra' => $item->extra,
                'thumbnails' => $thumbnails
            ];
        });
        $result = [
            'id' => $task->id,
            'number' => $task->number,
            'task_status' => $task->task_status,
            'plant' => $task->plant,
            'line' => $task->line,
            'engine' => $task->engine,
            'status' => $task->status,
            'assembly' => optional($task->assembly)->number,
            'progress' => $process,
            'items' => $items
        ];

        return $result;
    }

    /**
     * 获取详情-在线考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User   $user
     * @param  string $id
     * @return array
     */
    public function inlineDetail(User $user, string $id): array
    {
        if (!DepartmentRole::checkInline($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }
        $task = parent::find([
            'user_id' => $user->id,
            'id' => $id
        ]);
        $process = 0;
        $items = $task->items->map(function ($item) use (&$process) {
            if (!empty ($item->content)) {
                $process += $item->extra['process'];
            }
            $thumbnails = [];
            foreach ($item->getMedia(TaskItem::MEDIA_FILE) as $media) {
                $thumbnails[] = $media->getUrl();
            }
            return [
                'id' => $item->id,
                'content' => $item->content,
                'sort_order' => $item->sort_order,
                'remark' => $item->remark,
                'extra' => $item->extra,
                'thumbnails' => $thumbnails
            ];
        });
        $result = [
            'id' => $task->id,
            'number' => $task->number,
            'task_status' => $task->task_status,
            'plant' => $task->plant,
            'line' => $task->line,
            'engine' => $task->engine,
            'status' => $task->status,
            'assembly' => optional($task->assembly)->number,
            'progress' => $process,
            'items' => $items
        ];

        return $result;
    }

    /**
     * 获取详情-产品考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User   $user
     * @param  string $id
     * @return array
     */
    public function productDetail(User $user, string $id): array
    {
        if (!DepartmentRole::checkProduct($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }
        $task = parent::find([
            'user_id' => $user->id,
            'id' => $id
        ]);
        $process = 0;
        $items = $task->items->map(function (TaskItem $item) use (&$process, &$isFull) {
            $defaultContent = [
                'status' => null,
                'process' => 0,
                'value' => null,
                'issue_id' => null,
                'finished' => false,
                'defect_level' => '',
                'defect_description' => '',
                'defect_part' => '',
                'defect_position' => '',
                'defect_cause' => '',
                'part_number' => '',
                'file' => []
            ];
            if (!$item->extra['number'] || $item->extra['number'] < 2) {
                $defaultContent['value'] = '';
            } else {
                $defaultContent['value'] = array_pad([], $item->extra['number'], '');
            }
            $content = array_merge($defaultContent,json_decode($item->content, true) ?? []);
            if ($content && array_key_exists('process', $content)) {
                $process += floatval($content['process']);
            }
            if ($content && array_key_exists('file', $content) && $content['file']) {
                foreach ($content['file'] as $fileIndex => $file) {
                    $content['file'][$fileIndex] = url($file);
                }
            }
            return [
                'id' => $item->id,
                'remark' => $item->remark,
                'sort_order' => $item->sort_order,
                'content' => $content,
                'thumbnails' => $item->thumbnails,
                'files' => $item->files,
                'extra' => collect($item->extra)->only([
                    'eye',
                    'name',
                    'scan',
                    'type',
                    'unit',
                    'is_ds',
                    'camera',
                    'eye_en',
                    'number',
                    'record',
                    'torque',
                    'content',
                    'is_scan',
                    'name_en',
                    'options',
                    'part_id',
                    'process',
                    'standard',
                    'is_camera',
                    'content_en',
                    'sort_order',
                    'lower_limit',
                    'standard_en',
                    'upper_limit',
                ])->toArray()
            ];
        });
        $product = Product::where('number', $task->eb_number ?? '')->first();
        $result = [
            'id' => $task->id,
            'number' => $task->number,
            'task_status' => $task->task_status,
            'plant' => $task->plant,
            'line' => $task->line,
            'engine' => $task->engine,
            'status' => $task->status,
            'stage' => optional($product)->status,
            'eb_number' => $task->eb_number,
            'assembled_at' => optional($product)->assembled_at,
            'qc_at' => optional($product)->qc_at,
            'assembly' => optional($task->assembly)->number,
            'progress' => $process,
            'start_at' => $task->start_at,
            'end_at' => $task->end_at,
            'valid_at' => $task->valid_at,
            'is_full' => $items->filter(fn($item) => array_key_exists('finished', $item['content']) && $item['content']['finished'])->count() == $items->count(),
            'examine_type' => $task->original_examine['type'],
            'order_sort' => $task->extra && array_key_exists('order_sort', $task->extra) ? $task->extra['order_sort'] : 0,
            'items' => $items
        ];
        return $result;
    }

    private function getPathDetail(string $id):array
    {
        $item = (new TaskItemService())->findById($id);
        $defaultContent = [
            'status' => null,
            'process' => 0,
            'value' => null,
            'issue_id' => null,
            'finished' => false,
            'defect_level' => '',
            'defect_description' => '',
            'defect_part' => '',
            'defect_position' => '',
            'defect_cause' => '',
            'part_number' => '',
            'file' => []
        ];
        if (!$item->extra['number'] || $item->extra['number'] < 2) {
            $defaultContent['value'] = '';
        } else {
            $defaultContent['value'] = array_pad([], $item->extra['number'], '');
        }
        $content = array_merge($defaultContent,json_decode($item->content, true));
        if ($content && array_key_exists('file', $content) && $content['file']) {
            foreach ($content['file'] as $fileIndex => $file) {
                $content['file'][$fileIndex] = url($file);
            }
        }
        return [
            'id' => $item->id,
            'remark' => $item->remark,
            'sort_order' => $item->sort_order,
            'content' => $content,
            'thumbnails' => $item->thumbnails,
            'files' => $item->files,
            'extra' => collect($item->extra)->only([
                'eye',
                'name',
                'scan',
                'type',
                'unit',
                'is_ds',
                'camera',
                'eye_en',
                'number',
                'record',
                'torque',
                'content',
                'is_scan',
                'name_en',
                'options',
                'part_id',
                'process',
                'standard',
                'is_camera',
                'content_en',
                'sort_order',
                'lower_limit',
                'standard_en',
                'upper_limit',
            ])->toArray()
        ];
    }

    public function productPartUpdate(User $user, string $id, array $data): array
    {
        if (!DepartmentRole::checkProduct($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }
        $items = $this->productFullPart($user, $id);
        $item = $this->getItem();
        $taskItem = $items[$data['order']];
        $product = (new ProductService)->find(['number' => $item->eb_number]);
        $process = $taskItem['extra']['process'];
        if ($taskItem['extra']['is_scan']) {
            if (array_key_exists('part_number', $data)) {
                $part = (new PartService)->findById($taskItem['extra']['part_id']);
                if (strpos($data['part_number'], $part->number) === false) {
                    throw ValidationException::withMessages(['permission' => '扫描零件错误']);
                }
                if (!$part->items->where('number', $data['part_number'])->first()) {
                    (new PartItemService)->createByPart($part->id, $item->assembly_id, $user->id, optional($product)->id, $data['part_number']);
                }
            }
        }
        if ($taskItem['extra']['is_camera']) {
            if (!array_key_exists('images', $data) || !$data['images']) {
                throw ValidationException::withMessages(['permission' => '拍照记录错误']);
            }
        }
        $isOk = $data['status'] == 1;
        if (!$isOk) {
            $sql = [
                'author_id' => $user->id,
                'user_id' => $user->id,
                'task_id' => $item->id,
                'plant' => $item->plant,
                'line' => $item->line,
                'engine' => $item->engine,
                'stage' => $item->status,
                'assembly_id' => $item->assembly_id,
                'product_id' => Product::where('number', $item->eb_number)->value('id'),
                'part_id' => $taskItem['extra']['part_id'],
                'defect_description' => intval($data['defect_description']),
                'defect_level' => intval($data['defect_level']),
                'defect_part' => intval($data['defect_part']),
                'defect_position' => intval($data['defect_position']),
                'defect_cause' => intval($data['defect_cause']),
                'note' => trim($data['remark']),
                'type' => $item->original_examine['type'],
                'is_ok' => false
            ];
            $issue_id = (new IssueProductService())->createProduct($taskItem['id'],$sql, array_key_exists('images',$data) ? $data['images'] : []);
            $itemSql = [
                'content' => [
                    'status' => $isOk,
                    'process' => $process,
                    'issue_id' => $issue_id,
                    'finished' => true,
                    'value' => $data['value'],
                    'defect_level' => $data['defect_level'],
                    'defect_description' => $data['defect_description'],
                    'defect_part' => $data['defect_part'],
                    'defect_position' => $data['defect_position'],
                    'defect_cause' => $data['defect_cause'],
                    'part_number' => $data['part_number'],
                    'file' => array_key_exists('files',$data) && is_array($data['files']) && $data['files'] ? $data['files'] : []
                ],
                'remark' => $data['remark']
            ];
        } else {
            $itemSql = [
                'content' => [
                    'status' => $isOk,
                    'process' => $process,
                    'issue_id' => null,
                    'finished' => true,
                    'value' => $data['value'],
                    'defect_level' => null,
                    'defect_description' => null,
                    'defect_part' => null,
                    'defect_position' => null,
                    'defect_cause' => null,
                    'part_number' => $data['part_number'],
                    'file' => array_key_exists('files',$data) && is_array($data['files']) && $data['files'] ? $data['files'] : []
                ],
                'remark' => $data['remark']
            ];
        }
        (new TaskItemService())->createProduct($taskItem['id'], $itemSql,array_key_exists('images',$data) ? $data['images'] : []);
        $sql = array_merge([
            'order_sort' => 0,
            'value' => array_pad([], $item->items->count(), ''),
            'progress' => 0
        ], $item->extra ?? []);
        $sql['value'][$data['order']] = $isOk;
        $sql['progress'] += $process;
        $isFull = true;
        if ($data['order'] < $item->items->count() - 1 && $sql['order_sort'] < $item->items->count() - 1) {
            $isFull = false;
            $sql['order_sort']++;
        }
        parent::update($item->id, ['extra'=>$sql]);
        return [
            'item' => $this->getPathDetail($taskItem['id']),
            'process' => $sql['progress'],
            'order_sort' => $sql['order_sort'],
            'is_full' => $isFull
        ];
    }

    /**
     * 获取详情-产品考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User   $user
     * @param  string $id
     * @return array
     */
    public function productPart(User $user, string $id, int $order): array
    {
        if (!DepartmentRole::checkProduct($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }
        if (!$item = $this->getItem()) {
            $this->setItem(parent::find([
                'user_id' => $user->id,
                'id' => $id
            ]));
            $item = $this->getItem();
        }
        $taskItem = $item->items->get($order);
        return $taskItem->toArray();
    }

    /**
     * 获取详情-产品考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User   $user
     * @param  string $id
     * @return array
     */
    public function productFullPart(User $user, string $id): array
    {
        parent::setItem(parent::find([
            'user_id' => $user->id,
            'id' => $id
        ]));
        $item = $this->getItem();
        $result = [];
        for ($i = 0; $i < $item->items->count(); $i++) {
            $result[] = $this->productPart($user, $id, $i);
        }
        return $result;
    }

    /**
     * 获取详情-产品考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User   $user
     * @param  array $data
     * @return void
     */
    public function productStart(User $user, array $data): void
    {
        if (!DepartmentRole::checkProduct($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }
        $task = parent::find([
            'user_id' => $user->id,
            'id' => $data['id']
        ]);
        if (!$task->start_at || $task->task_status == WorkItem::STATUS_PENDING) {
            $now = Carbon::now();
            $sql = [
                'start_at' => $now,
                'valid_at' => $now->clone()->addHours($task->period),
                'status' => Task::STATUS_PROCESSING,
                'task_status' => Task::STATUS_PROCESSING
            ];
            $task->fill($sql);
            if ($task->save()) {
                $product = Product::where('number', $task->eb_number)->first();
                if ($product) {
                    $product->fill([
                        'qc_at' => $data['qc_at'],
                        'assembled_at' => $data['assembled_at']
                    ]);
                    $product->save();
                }
            }
            $this->clearCache();
        }
    }

    public function productPreview(User $user,string $id)
    {
        if (!DepartmentRole::checkProduct($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }
        $item = parent::findById($id);
        $fileName = [
            (new DictService())->getNameByCode('engine_type',$item->original_examine['engine'])
        ];
        $templateName = null;
        switch ($item->original_examine['type']) {
            case CommitProduct::TYPE_OVERHAUL:
                $fileName[] = 'Assembly';
                $templateName = 'ProductAssemblyReader';
                break;
            case CommitProduct::TYPE_ASSEMBLING:
                $fileName[] = 'Reassembly';
                $templateName = 'ProductReassemblyReader';
                break;
            case CommitProduct::TYPE_DYNAMIC:
                $fileName[] = 'Dynamic';
                $templateName = 'ProductDynamicReader';
                break;
        }
        $fileName[] = '.xlsx';
        $templateFile = implode('',$fileName);
        $file = resource_path('templates/'.$templateFile);
        if(!file_exists($file))
        {
            throw ValidationException::withMessages(['permission' => '请先上传记录模板']);
        }
        $templateData = (new ExcelReader($file));
        return $templateData->readData($item,$templateName);
    }

    /**
     * 提交在线考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User    $user
     * @param  string  $id
     * @param  array   $data
     * @return integer
     */
    public function inlineUpdate(User $user, string $id, array $data): int
    {
        $task = parent::find([
            'user_id' => $user->id,
            'id' => $id
        ]);
        $item = $task->items->where('id', $data['item_id'])->first();
        $content = $data['content'];
        if ($content == 1) {
            $item->fill([
                'content' => 1,
                'user_id' => $user->id,
                'remark' => $data['remark']
            ]);
            $item->save();
            if (array_key_exists('image', $data) && $data['image']) {
                foreach ($data['image'] as $image) {
                    $item->addMedia($image)->toMediaCollection(TaskItem::MEDIA_IMAGE);
                }
            }
            return $item->extra['process'];
        }
        $item->fill([
            'content' => 0,
            'user_id' => $user->id,
            'remark' => $data['remark']
        ]);
        $item->save();
        (new IssueService)->makeByInline($user, $task, $item, $data);
        return 0;
    }

    /**
     * 提交在线考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User    $user
     * @param  string  $id
     * @param  array   $data
     * @return integer
     */
    public function productUpdate(User $user, string $id, array $data): int
    {
        $task = parent::find([
            'user_id' => $user->id,
            'id' => $id
        ]);
        $item = $task->items->where('id', $data['item_id'])->first();
        $content = $data['content'];
        if ($content == 1) {
            $item->fill([
                'content' => 1,
                'user_id' => $user->id,
                'remark' => $data['remark']
            ]);
            $item->save();
            if (array_key_exists('image', $data) && $data['image']) {
                foreach ($data['image'] as $image) {
                    $item->addMedia($image)->toMediaCollection(TaskItem::MEDIA_IMAGE);
                }
            }
            return $item->extra['process'];
        }
        $item->fill([
            'content' => 0,
            'user_id' => $user->id,
            'remark' => $data['remark']
        ]);
        $item->save();
        (new IssueService)->makeByProduct($user, $task, $item, $data);
        return 0;
    }



    /**
     * 提交在线考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User    $user
     * @param  string  $id
     * @param  array   $data
     * @return integer
     */
    public function serviceUpdate(User $user, string $id, array $data): int
    {
        $task = parent::find([
            'user_id' => $user->id,
            'id' => $id
        ]);
        $item = $task->items->where('id', $data['item_id'])->first();
        $content = $data['content'];
        if ($content == 1) {
            $item->fill([
                'content' => 1,
                'user_id' => $user->id,
                'remark' => $data['remark']
            ]);
            $item->save();
            if (array_key_exists('image', $data) && $data['image']) {
                foreach ($data['image'] as $image) {
                    $item->addMedia($image)->toMediaCollection(TaskItem::MEDIA_IMAGE);
                }
            }
            return $item->extra['process'];
        }
        $item->fill([
            'content' => 0,
            'user_id' => $user->id,
            'remark' => $data['remark']
        ]);
        $item->save();
        (new IssueService)->makeByService($user, $task, $item, $data);
        return 0;
    }

    public function serviceAll(User $user, string $id, array $data): array
    {
        $result = [];

        return $result;
    }

    public function inlineAll(User $user, string $id, array $data): array
    {
        $task = parent::find([
            'user_id' => $user->id,
            'id' => $id
        ]);

        $canAll = $task->items->filter(fn($n) => optional($n->extra)['type'] == 2)->values();
        if ($canAll->count()) {
            throw ValidationException::withMessages(['can_all' => __('task.inline_all.cannot_all')]);
        }

        $result = [];

        return $result;
    }

    public function productAll(User $user, string $id, array $data): array
    {
        $result = [];

        return $result;
    }

    public function serviceReset(User $user, string $id, array $data): array
    {
        $result = [];

        return $result;
    }

    public function inlineReset(User $user, string $id, array $data): array
    {
        $result = [];

        return $result;
    }

    public function productReset(User $user, string $id, array $data): array
    {
        $result = [];

        return $result;
    }

    public function serviceItemDetail(User $user, string $id, array $data): array
    {
        $result = [];

        return $result;
    }

    public function inlineItemDetail(User $user, string $id, array $data): array
    {
        $result = [];

        return $result;
    }

    public function productItemDetail(User $user, string $id, array $data): array
    {
        $result = [];

        return $result;
    }

}