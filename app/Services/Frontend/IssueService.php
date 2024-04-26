<?php
namespace App\Services\Frontend;

use App\Models\User;
use App\Models\Task;
use App\Models\Issue;
use App\Models\Product;
use App\Models\TaskItem;
use App\Models\IssueItem;
use App\Services\Service;
use Illuminate\Validation\ValidationException;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * 问题追踪服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class IssueService extends Service
{

    public ?string $className = Issue::class;

    /**
     * 创建问题-整车服务
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User  $user
     * @param  array $data
     * @return void
     */
    public function createVehicle(User $user, array $data)
    {
        $product = (new ProductService)->updateByIssue($data);
        if ($product->issues->where('type', Issue::TYPE_SERVICE)->first()) {
            throw ValidationException::withMessages(['issue.exists' => __('issue.vehicle.issue.exists')]);
        }
        $assembly = $product->assembly;
        $sql = [
            'user_id' => null,
            'author_id' => $user->id,
            'plant' => optional($assembly)->plant ?? 0,
            'line' => optional($assembly)->line ?? 0,
            'engine' => optional($assembly)->type ?? 0,
            'vehicle_type' => $data['motorcycle_type'],
            'assembly_id' => optional($assembly)->id,
            'description' => $data['description'],
            'soma' => null,
            'lama' => null,
            'note' => null,
            'eight_disciplines' => null,
            'status' => Issue::STATUS_VERIFY,
            'type' => Issue::TYPE_SERVICE,
            'model_id' => $product->id,
            'model_type' => get_class($product)
        ];
        if (parent::create($sql)) {
            $sql = [
                [
                    'code' => 'car_series',
                    'content' => $data['car_series'],
                ],
                [
                    'code' => 'sensor_point',
                    'content' => $data['sensor_point'],
                ],
                [
                    'code' => 'reason',
                    'content' => $data['reason'],
                ],
            ];
            $this->item->items()->createMany($sql);
            if (array_key_exists('picture', $data) && $data['picture']) {
                $files = collect($data['picture'])->map(function ($file) {
                    return $file->getClientOriginalName();
                });
                if ($item = $this->item->items()->create(['code' => 'picture', 'content' => $files->join(',')])) {
                    foreach ($data['picture'] as $picture) {
                        $item->addMedia($picture)->toMediaCollection(IssueItem::MEDIA_FILE);
                    }
                }
            }
            if (array_key_exists('image', $data) && $data['image']) {
                $files = collect($data['image'])->map(function ($file) {
                    return $file->getClientOriginalName();
                });
                if ($item = $this->item->items()->create(['code' => 'image', 'content' => $files->join(',')])) {
                    foreach ($data['image'] as $image) {
                        $item->addMedia($image)->toMediaCollection(IssueItem::MEDIA_FILE);
                    }
                }
            }
            if (array_key_exists('video', $data) && $data['video']) {
                $files = collect($data['video'])->map(function ($file) {
                    return $file->getClientOriginalName();
                });
                if ($item = $this->item->items()->create(['code' => 'video', 'content' => $files->join(',')])) {
                    foreach ($data['video'] as $video) {
                        $item->addMedia($video)->toMediaCollection(IssueItem::MEDIA_FILE);
                    }
                }
            }
        }
    }

    /**
     * 获取整车问题列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @return array
     */
    public function getListByVehicle(array $data): array
    {
        $condition = [
            'keyword' => ['search', ['description', 'soma', 'lama', 'note', 'eight_disciplines']],
            'date' => ['datetime_range', 'created_at']
        ];
        $data['date'] = [$data['start'], $data['end']];
        parent::listQuery($data, $condition, [['type', '=', Issue::TYPE_SERVICE]]);
        return parent::list([
            'id',
            'description',
            'status',
            'created_at',
            'model.status as product_status'
        ]);
    }

    /**
     * 获取滞留车列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @return array
     */
    public function getListByBlock(array $data): array
    {
        $condition = [
            'keyword' => ['search', ['description', 'soma', 'lama', 'note', 'eight_disciplines']],
            'date' => ['datetime_range', 'created_at']
        ];
        $data['date'] = [$data['start'], $data['end']];
        $this->setHas([
            'model' => function ($query) {
                $query->where('status', '<', Product::STATUS_OVERDUE);
            }
        ]);
        parent::listQuery($data, $condition, [['type', '=', Issue::TYPE_SERVICE]]);
        return parent::list([
            'id',
            'description',
            'status',
            'created_at',
            'model.status as product_status'
        ]);
    }

    /**
     * 获取整车问题详情
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $id
     * @return array
     */
    public function getDetailByVehicle(string $id): array
    {
        $item = parent::findById($id);
        return [
            'id' => $item->id,
            'motorcycle_type' => $item->vehicle_type,
            'description' => $item->description,
            'author' => optional(optional($item->author)->profile)->name ?? optional($item->author)->number,
            'car_series' => (int) $item->items->where('code', 'car_series')->value('content'),
            'sensor_point' => (int) $item->items->where('code', 'sensor_point')->value('content'),
            'reason' => $item->items->where('code', 'reason')->value('content'),
            'delay' => $item->model->status == Product::STATUS_VERIFY,
            'shift' => $item->model->shift,
            'plant' => $item->model->plant,
            'eb_type' => $item->model->eb_type,
            'pn_number' => $item->model->pn_number,
            'number' => $item->model->number,
            'created_at' => $item->created_at,
            'picture' => optional($item->items->where('code', 'picture')->first())->thumbnails ?? [],
            'image' => optional($item->items->where('code', 'image')->first())->thumbnails ?? [],
            'video' => optional($item->items->where('code', 'video')->first())->thumbnails ?? [],
        ];
    }

    /**
     * 更新车辆问题追踪
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User   $user
     * @param  string $id
     * @param  array  $data
     * @return void
     * 
     * @throws ValidationException
     */
    public function updateVehicle(User $user, string $id, array $data)
    {
        $issue = parent::find(['id' => $id, 'type' => Issue::TYPE_SERVICE]);
        if (!$issue) {
            throw ValidationException::withMessages(['id.exists_plus' => __('issue.vehicle.id.exists_plus')]);
        }
        $product = (new ProductService)->updateByIssue($data);
        $sql = [
            'vehicle_type' => $data['motorcycle_type'],
            'description' => $data['description'],
            'model_id' => $product->id,
            'user_id' => $user->id
        ];
        if (parent::update($id, $sql)) {
            $issue->items->where('code', 'car_series')->first()->update(['content' => $data['car_series']]);
            $issue->items->where('code', 'sensor_point')->first()->update(['content' => $data['sensor_point']]);
            $issue->items->where('code', 'reason')->first()->update(['content' => $data['reason']]);
            if (array_key_exists('picture', $data) && $data['picture']) {
                $files = collect($data['picture'])->map(function ($file) {
                    return $file->getClientOriginalName();
                });
                $item = $issue->items->where('code', 'picture')->first();
                if (!$item) {
                    $item = $issue->items()->create(['code' => 'picture', 'content' => $files->join(',')]);
                } else {
                    $item->update(['content' => $files->join(',')]);
                }
                foreach ($data['picture'] as $picture) {
                    $item->addMedia($picture)->toMediaCollection(IssueItem::MEDIA_FILE);
                }
            }
            if (array_key_exists('image', $data) && $data['image']) {
                $files = collect($data['image'])->map(function ($file) {
                    return $file->getClientOriginalName();
                });
                $item = $issue->items->where('code', 'image')->first();
                if (!$item) {
                    $item = $issue->items()->create(['code' => 'image', 'content' => $files->join(',')]);
                } else {
                    $item->update(['content' => $files->join(',')]);
                }
                foreach ($data['image'] as $image) {
                    $item->addMedia($image)->toMediaCollection(IssueItem::MEDIA_FILE);
                }
            }
            if (array_key_exists('video', $data) && $data['video']) {
                $files = collect($data['video'])->map(function ($file) {
                    return $file->getClientOriginalName();
                });
                $item = $issue->items->where('code', 'video')->first();
                if (!$item) {
                    $item = $issue->items()->create(['code' => 'video', 'content' => $files->join(',')]);
                } else {
                    $item->update(['content' => $files->join(',')]);
                }
                foreach ($data['video'] as $video) {
                    $item->addMedia($video)->toMediaCollection(IssueItem::MEDIA_FILE);
                }
            }
            if (array_key_exists('media', $data) && $data['media']) {
                Media::whereIn('uuid', $data['media'])->delete();
            }
        }
    }

    /**
     * 变更滞留车状态
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return void
     */
    public function updateVehicleBlock(User $user, string $id, array $data): void
    {
        $issue = parent::find(['id' => $id, 'type' => Issue::TYPE_SERVICE]);
        $product = $issue->model;
        $product->fill([
            'status' => (int) $data['block_status']
        ]);
        if ($product->save()) {
            if ($item = $issue->items->where('code', 'block_content')->first()) {
                $item->update(['content' => (int) $data['block_content']]);
            } else {
                $issue->items()->create([
                    'code' => 'block_content',
                    'content' => (int) $data['block_content']
                ]);
            }
            $types = [
                Product::STATUS_VERIFY => Issue::STATUS_VERIFY,
                Product::STATUS_PENDING => Issue::STATUS_ONGOING,
                Product::STATUS_OVERDUE => Issue::STATUS_CLOSED
            ];
            $issue->fill([
                'user_id' => $user->id,
                'status' => $types[(int) $data['block_status']]
            ]);
            $issue->save();
        }
    }

    /**
     * 通过字典标识及键值获取键名
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string  $code
     * @param  integer  $value
     * @return ?string
     */
    public function getNameByCode(string $code, int $value): ?string
    {
        if (!$item = parent::find(['code' => $code])) {
            return null;
        }
        return $item->items->where('content', $value)->value('name') ?? null;
    }

    public function makeByInline(User $user, Task $task, TaskItem $taskItem, array $data)
    {

        $product = (new ProductService)->find(['number' => $data['number']]);
        $sql = [
            'author_id' => $user->id,
            'user_id' => null,
            'plant' => $task->plant,
            'line' => $task->line,
            'engine' => $task->engine,
            'vehicle_type' => 0,
            'assembly_id' => $task->assembly_id,
            'description' => (new DictService)->getNameByCode('inline_item_type_' . $taskItem->extra->type, $data['description']),
            'soma' => null,
            'lama' => null,
            'note' => null,
            'eight_disciplines' => null,
            'status' => $task->status,
            'type' => Issue::TYPE_INLINE,
            'model_id' => $task->id,
            'model_type' => get_class($task)
        ];
        if (parent::create($sql)) {
            $sql = [
                [
                    'code' => 'remark',
                    'content' => $data['remark'] ?? '',
                ],
                [
                    'code' => 'product_id',
                    'content' => $product->id,
                ],
                [
                    'code' => 'scope',
                    'content' => $data['scope'] ?? '',
                ],
                [
                    'code' => 'ira',
                    'content' => $data['ira'] ?? '',
                ],
                [
                    'code' => 'options',
                    'content' => json_encode($data['options']) ?? '',
                ]
            ];
            $this->item->items()->createMany($sql);
            if (array_key_exists('picture', $data) && $data['picture']) {
                $files = collect($data['picture'])->map(function ($file) {
                    return $file->getClientOriginalName();
                });
                if ($item = $this->item->items()->create(['code' => 'picture', 'content' => $files->join(',')])) {
                    foreach ($data['picture'] as $picture) {
                        $item->addMedia($picture)->toMediaCollection(IssueItem::MEDIA_FILE);
                    }
                }
            }
            if (array_key_exists('image', $data) && $data['image']) {
                $files = collect($data['image'])->map(function ($file) {
                    return $file->getClientOriginalName();
                });
                if ($item = $this->item->items()->create(['code' => 'image', 'content' => $files->join(',')])) {
                    foreach ($data['image'] as $image) {
                        $item->addMedia($image)->toMediaCollection(IssueItem::MEDIA_FILE);
                    }
                }
            }
        }
    }

    public function makeByProduct(User $user, Task $task, TaskItem $taskItem, array $data)
    {

        $product = (new ProductService)->find(['number' => $data['number']]);
        $sql = [
            'author_id' => $user->id,
            'user_id' => null,
            'plant' => $task->plant,
            'line' => $task->line,
            'engine' => $task->engine,
            'vehicle_type' => 0,
            'assembly_id' => $task->assembly_id,
            'description' => $data['description'],
            'soma' => null,
            'lama' => null,
            'note' => null,
            'eight_disciplines' => null,
            'status' => $task->status,
            'type' => Issue::TYPE_PRODUCT,
            'model_id' => $task->id,
            'model_type' => get_class($task)
        ];
        if (parent::create($sql)) {
            $sql = [
                [
                    'code' => 'remark',
                    'content' => $data['remark'] ?? '',
                ],
                [
                    'code' => 'scope',
                    'content' => $data['scope'] ?? '',
                ],
                [
                    'code' => 'ira',
                    'content' => $data['ira'] ?? '',
                ]
            ];
            if ($product) {
                $sql[] = [
                    'code' => 'product_id',
                    'content' => $product->id,
                ];
            }
            if ($data['options']) {
                $sql[] = [
                    'code' => 'options',
                    'content' => json_encode($data['options']),
                ];
            }
            $this->item->items()->createMany($sql);
            if (array_key_exists('picture', $data) && $data['picture']) {
                $files = collect($data['picture'])->map(function ($file) {
                    return $file->getClientOriginalName();
                });
                if ($item = $this->item->items()->create(['code' => 'picture', 'content' => $files->join(',')])) {
                    foreach ($data['picture'] as $picture) {
                        $item->addMedia($picture)->toMediaCollection(IssueItem::MEDIA_FILE);
                    }
                }
            }
            if (array_key_exists('image', $data) && $data['image']) {
                $files = collect($data['image'])->map(function ($file) {
                    return $file->getClientOriginalName();
                });
                if ($item = $this->item->items()->create(['code' => 'image', 'content' => $files->join(',')])) {
                    foreach ($data['image'] as $image) {
                        $item->addMedia($image)->toMediaCollection(IssueItem::MEDIA_FILE);
                    }
                }
            }
        }
    }

    public function makeByService(User $user, Task $task, TaskItem $taskItem, array $data)
    {

        $product = (new ProductService)->find(['number' => $data['number']]);
        $sql = [
            'author_id' => $user->id,
            'user_id' => null,
            'plant' => $task->plant,
            'line' => $task->line,
            'engine' => $task->engine,
            'vehicle_type' => 0,
            'assembly_id' => $task->assembly_id,
            'description' => $data['description'],
            'soma' => null,
            'lama' => null,
            'note' => null,
            'eight_disciplines' => null,
            'status' => $task->status,
            'type' => Issue::TYPE_SERVICE,
            'model_id' => $task->id,
            'model_type' => get_class($task)
        ];
        if (parent::create($sql)) {
            $sql = [
                [
                    'code' => 'remark',
                    'content' => $data['remark'] ?? '',
                ],
                [
                    'code' => 'scope',
                    'content' => $data['scope'] ?? '',
                ],
                [
                    'code' => 'ira',
                    'content' => $data['ira'] ?? '',
                ]
            ];
            if ($product) {
                $sql[] = [
                    'code' => 'product_id',
                    'content' => $product->id,
                ];
            }
            if ($data['options']) {
                $sql[] = [
                    'code' => 'options',
                    'content' => json_encode($data['options']),
                ];
            }
            $this->item->items()->createMany($sql);
            if (array_key_exists('picture', $data) && $data['picture']) {
                $files = collect($data['picture'])->map(function ($file) {
                    return $file->getClientOriginalName();
                });
                if ($item = $this->item->items()->create(['code' => 'picture', 'content' => $files->join(',')])) {
                    foreach ($data['picture'] as $picture) {
                        $item->addMedia($picture)->toMediaCollection(IssueItem::MEDIA_FILE);
                    }
                }
            }
            if (array_key_exists('image', $data) && $data['image']) {
                $files = collect($data['image'])->map(function ($file) {
                    return $file->getClientOriginalName();
                });
                if ($item = $this->item->items()->create(['code' => 'image', 'content' => $files->join(',')])) {
                    foreach ($data['image'] as $image) {
                        $item->addMedia($image)->toMediaCollection(IssueItem::MEDIA_FILE);
                    }
                }
            }
        }
    }
}
