<?php
namespace App\Services\Frontend;

use Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\IssueVehicle;
use App\Packages\Department\DepartmentRole;
use App\Services\Service;
use Illuminate\Validation\ValidationException;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * 整车服务-问题追踪
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class IssueVehicleService extends Service
{

    public ?string $className = IssueVehicle::class;

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
        if (!DepartmentRole::checkVehicle($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }
        $sql = [
            'author_id' => $user->id,
            'user_id' => $user->id,
            'shift' => $data['shift'],
            'plant' => $data['plant'],
            'eb_type' => $data['eb_type'],
            'product_number' => trim($data['product_number']),
            'sensor' => $data['sensor'],
            'eb_number' => trim($data['eb_number']),
            'car_line' => $data['car_line'],
            'car_type' => $data['car_type'],
            'is_block' => (bool) $data['is_block'],
            'description' => trim($data['description']),
            'initial_analysis' => trim($data['initial_analysis']),
            'status' => IssueVehicle::STATUS_VERIFY,
            'due_date' => Carbon::now()->addDays((int) (new SystemConfigService)->getValueByCode('vehicle_due'))
        ];
        if (parent::create($sql)) {
            if (array_key_exists('overview', $data) && $data['overview']) {
                $files = collect($data['overview'])->map(function ($file) {
                    return Str::uuid() . '.' . $file->getClientOriginalExtension();
                })->toArray();
                foreach ($data['overview'] as $key => $overview) {
                    $this->item->addMedia($overview)->usingFileName($files[$key])->toMediaCollection(IssueVehicle::MEDIA_OVERVIEW);
                }
            }
            if (array_key_exists('detail', $data) && $data['detail']) {
                $files = collect($data['detail'])->map(function ($file) {
                    return Str::uuid() . '.' . $file->getClientOriginalExtension();
                })->toArray();
                foreach ($data['detail'] as $key => $detail) {
                    $this->item->addMedia($detail)->usingFileName($files[$key])->toMediaCollection(IssueVehicle::MEDIA_DETAIL);
                }
            }
            if (array_key_exists('video', $data) && $data['video']) {
                $files = collect($data['video'])->map(function ($file) {
                    return Str::uuid() . '.' . $file->getClientOriginalExtension();
                })->toArray();
                foreach ($data['video'] as $key => $video) {
                    $this->item->addMedia($video)->usingFileName($files[$key])->toMediaCollection(IssueVehicle::MEDIA_VIDEO);
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
    public function getList(array $data): array
    {
        $condition = [
            'keyword' => ['search', ['description', 'eb_number', 'product_number', 'initial_analysis']],
            'date' => ['datetime_range', 'created_at']
        ];
        $data['date'] = [$data['start'], $data['end']];
        parent::listQuery($data, $condition);
        return parent::list([
            'id',
            'description',
            'status',
            'created_at',
            'is_block',
            'block_status',
            'block_content'
        ]);
    }

    /**
     * 获取整车问题详情
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $id
     * @return array
     */
    public function detail(string $id): array
    {
        $item = parent::findById($id);
        return [
            'id' => $item->id,
            'shift' => $item->shift,
            'plant' => $item->plant,
            'eb_type' => $item->eb_type,
            'product_number' => $item->product_number,
            'sensor' => $item->sensor,
            'eb_number' => $item->eb_number,
            'car_line' => $item->car_line,
            'car_type' => $item->car_type,
            'is_block' => $item->is_block,
            'description' => $item->description,
            'initial_analysis' => $item->initial_analysis,
            'status' => $item->status,
            'author' => optional(optional($item->author)->profile)->name ?? optional($item->author)->number,
            'created_at' => $item->created_at,
            'overview' => $item->overview_attaches,
            'detail' => $item->detail_attaches,
            'video' => $item->videos,
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
        if (!DepartmentRole::checkVehicle($user) || parent::find(['id' => $id, 'status' => IssueVehicle::STATUS_CLOSED])) {
            throw ValidationException::withMessages(['permission' => __('issue_vehicle.missing_permission')]);
        }

        $sql = [
            'user_id' => $user->id,
            'shift' => $data['shift'],
            'plant' => $data['plant'],
            'eb_type' => $data['eb_type'],
            'product_number' => trim($data['product_number']),
            'sensor' => $data['sensor'],
            'eb_number' => trim($data['eb_number']),
            'car_line' => $data['car_line'],
            'car_type' => $data['car_type'],
            'is_block' => (bool) $data['is_block'],
            'description' => trim($data['description']),
            'initial_analysis' => trim($data['initial_analysis']),
        ];

        if (parent::update($id, $sql)) {
            $overview_attaches = array_key_exists('overview', $data) ? (array) $data['overview'] : [];
            $detail_attaches = array_key_exists('detail', $data) ? (array) $data['detail'] : [];
            $videos = array_key_exists('video', $data) ? (array) $data['video'] : [];
            $media = array_key_exists('media', $data) ? (array) $data['media'] : [];
            if ($media) {

                Media::whereIn('uuid', $media)->get()->each(fn($n) => $n->delete());
            }
            if ($overview_attaches) {

                $files = collect($overview_attaches)->map(function (UploadedFile $file) {
                    return Str::uuid() . '.' . $file->getClientOriginalExtension();
                })->toArray();
                foreach ($overview_attaches as $key => $file) {
                    $this->item->addMedia($file)->usingFileName($files[$key])->toMediaCollection(IssueVehicle::MEDIA_OVERVIEW);
                }

            }
            if ($detail_attaches) {
                $files = collect($detail_attaches)->map(function (UploadedFile $file) {
                    return Str::uuid() . '.' . $file->getClientOriginalExtension();
                })->toArray();
                foreach ($detail_attaches as $key => $file) {
                    $this->item->addMedia($file)->usingFileName($files[$key])->toMediaCollection(IssueVehicle::MEDIA_DETAIL);
                }
            }
            if ($videos) {
                $files = collect($videos)->map(function (UploadedFile $file) {
                    return Str::uuid() . '.' . $file->getClientOriginalExtension();
                })->toArray();
                foreach ($videos as $key => $file) {
                    $this->item->addMedia($file)->usingFileName($files[$key])->toMediaCollection(IssueVehicle::MEDIA_VIDEO);
                }
            }
            $this->clearCache();
        }

    }

    /**
     * 整车服务-视频截图保存
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User   $user
     * @param  array  $data
     * @return void
     */
    public function poster(User $user, array $data): void
    {
        if (!DepartmentRole::checkVehicle($user)) {
            throw ValidationException::withMessages(['permission' => __('issue_vehicle.missing_permission')]);
        }
        $uuid = $data['uuid'];
        $poster = $data['poster'];
        if ($media = Media::where('uuid', $uuid)->first()) {
            $path = str_replace('.' . $media->extension, '.jpg', $media->getPath());
            @file_put_contents($path, base64_decode($poster));
            $this->clearCache();
        }
    }


    /**
     * 获取滞留车列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @return array
     */
    public function block(array $data): array
    {
        $condition = [
            'keyword' => ['search', ['description', 'eb_number', 'product_number', 'initial_analysis']],
            'date' => ['datetime_range', 'created_at']
        ];
        $data['date'] = [array_key_exists('start',$data) ? $data['start'] : null, array_key_exists('end',$data) ? $data['end'] : null];
        parent::listQuery($data, $condition, [['is_block', '=', true]]);
        return parent::list([
            'id',
            'description',
            'status',
            'created_at',
            'is_block',
            'block_status',
            'block_content'
        ]);
    }

    /**
     * 整车服务-滞留车变更状态
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return void
     */
    public function updateBlock(User $user, string $id, array $data): void
    {
        if (!DepartmentRole::checkVehicle($user) || parent::find(['id' => $id, 'status' => IssueVehicle::STATUS_CLOSED])) {
            throw ValidationException::withMessages(['permission' => __('issue_vehicle.missing_permission')]);
        }
        $sql = [
            'block_status' => $data['block_status'],
            'user_id' => $user->id
        ];
        if($data['block_status'] == IssueVehicle::BLOCK_STATUS_SUCCESS)
        {
            $sql['is_block'] = false;
        }
        if (array_key_exists('block_content', $data)) {
            $sql['block_content'] = $data['block_content'];
        }
        parent::update($id, $sql);
    }

}
