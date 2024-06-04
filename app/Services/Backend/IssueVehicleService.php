<?php
namespace App\Services\Backend;

use Str;
use Storage;
use FFMpeg\FFMpeg;
use App\Models\User;
use App\Services\Service;
use App\Models\IssueVehicle;
use FFMpeg\Coordinate\TimeCode;
use App\Packages\Department\DepartmentRole;
use Illuminate\Validation\ValidationException;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * 整车服务-问题服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class IssueVehicleService extends Service
{
    public ?string $className = IssueVehicle::class;

    /**
     * 获取问题列表
     */
    public function getList(User $user, array $data): array
    {
        if (!DepartmentRole::checkVehicle($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }
        $condition = [
            'keyword' => ['search', ['product_number', 'eb_number', 'description']],
            'plant' => 'eq',
            'type' => 'eq',
            'eb_type' => 'eq',
            'date' => ['datetime_range', 'created_at']
        ];
        $status = trim($data['status']) == 'a';
        parent::listQuery($data, $condition, [['status', $status ? '=' : '!=', IssueVehicle::STATUS_CLOSED]]);
        $result = parent::list();
        $result['items'] = $result['items']->map(function (IssueVehicle $item) {
            return [
                'id' => $item->id,
                'type' => $item->type,
                'plant' => $item->plant,
                'eb_type' => $item->eb_type,
                'description' => $item->description,
                'author' => optional(optional($item->author)->profile)->name ?? optional($item->author)->number,
                'created_at' => $item->created_at,
                'status' => $item->status,
                'due_end' => $item->due_end,
            ];
        });
        return $result;
    }


    /**
     * 问题追踪图示上传
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  UploadedFile $fileBag
     * @return array
     */
    public function upload(UploadedFile $fileBag): array
    {
        $videos = ['mp4', 'avi', 'mov', 'wma', 'mkv', 'mpg', 'rm'];
        $pictures = ['jpg', 'jpeg', 'png', 'bmp', 'tif', 'tiff'];
        $ext = $fileBag->guessExtension();
        $result = [];
        if (in_array($ext, $videos)) {
            $file = Storage::putFile('public/video', $fileBag);
            $ffmpegConfig = [
                'ffmpeg.binaries' => env('FFMPEG_PATH'),
                'ffprobe.binaries' => env('FFPROBE_PATH')
            ];
            $ffmpeg = FFMpeg::create($ffmpegConfig);
            $video = $ffmpeg->open(storage_path('app/' . $file));
            $path = storage_path('app/public/screenshot');
            $posterName = head(explode(".", basename($file))) . '.jpg';
            $poster = $path . '/' . $posterName;
            if (!is_dir($path)) {
                @mkdir($path);
            }
            $frame = $video->frame(TimeCode::fromSeconds(1));
            $frame->save($poster, true);
            $result = [
                'url' => Storage::url($file),
                'name' => $fileBag->getClientOriginalName(),
                'uuid' => Str::afterLast($file, '/'),
                'poster' => Storage::url('public/screenshot/' . $posterName)
            ];
        }
        if (in_array($ext, $pictures)) {
            $file = Storage::putFile('public/images', $fileBag);
            $result = [
                'url' => Storage::url($file),
                'name' => $fileBag->getClientOriginalName(),
                'uuid' => Str::afterLast($file, '/')
            ];

        }
        return $result;
    }

    /**
     * 删除问题追踪图示
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $uuid
     * @return void
     */
    public function uploadDelete(string $uuid)
    {
        $isMedia = Str::isUuid($uuid);
        if ($isMedia) {
            if ($media = Media::where('uuid', $uuid)->first()) {
                $media->delete();
            }
        } else {
            $filePath = Storage::path($uuid);
            if (file_exists($filePath)) {
                Storage::delete($filePath);
            }
        }
    }

    /**
     * 关闭问题
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User   $user
     * @param  string $id
     * @return void
     */
    public function close(User $user, string $id)
    {
        if (!DepartmentRole::checkVehicle($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }
        $this->canClose($id);
        $sql = [
            'user_id' => $user->id,
            'status' => IssueVehicle::STATUS_CLOSED
        ];
        parent::update($id, $sql);
    }

    public function updateVehicle(User $user, string $id, array $data)
    {
        if (!DepartmentRole::checkVehicle($user)) {
            throw ValidationException::withMessages(['permission' => '暂无该操作权限']);
        }
        $sql = [
            'initial_analysis' => array_key_exists('initial_analysis', $data) ? $data['initial_analysis'] : null,
            'initial_action' => array_key_exists('initial_action', $data) ? $data['initial_action'] : null,
            'status' => array_key_exists('status', $data) ? $data['status'] : null,
            'type' => array_key_exists('type', $data) ? $data['type'] : null,
            'defect_level' => array_key_exists('defect_level', $data) ? $data['defect_level'] : null,
            'soma' => array_key_exists('soma', $data) ? $data['soma'] : null,
            'lama' => array_key_exists('lama', $data) ? $data['lama'] : null,
            'eight_disciplines' => array_key_exists('eight_disciplines', $data) ? $data['eight_disciplines'] : null,
            'ira' => array_key_exists('ira', $data) ? $data['ira'] : null,
            'issue_type' => array_key_exists('issue_type', $data) ? intval($data['issue_type']) : 0,
            'is_ppm' => array_key_exists('is_ppm', $data) ? $data['is_ppm'] : false,
            'is_pre_highlight' => array_key_exists('is_pre_highlight', $data) ? $data['is_pre_highlight'] : false,
            'detect_area' => array_key_exists('detect_area', $data) ? $data['detect_area'] : null,
            'quantity' => array_key_exists('quantity', $data) ? $data['quantity'] : null,
            'cause' => array_key_exists('cause', $data) ? $data['cause'] : null,
            'relate_parts' => array_key_exists('relate_parts', $data) ? $data['relate_parts'] : null,
            'cause_type' => array_key_exists('cause_type', $data) ? $data['cause_type'] : null,
            'due_date' => array_key_exists('due_date', $data) ? $data['due_date'] : null,
            'delivery_confirm' => array_key_exists('delivery_confirm', $data) ? $data['delivery_confirm'] : false,
            'user_id' => $user->id,
            'is_confirm' => true,
        ];
        if ($sql['status'] == IssueVehicle::STATUS_CLOSED) {
            $this->canClose($id);
        }
        if (parent::update($id, $sql)) {
            $overview_attaches = array_key_exists('overview_attaches', $data) ? (array) $data['overview_attaches'] : [];
            $master_overview_attaches = array_key_exists('master_overview_attaches', $data) ? (array) $data['master_overview_attaches'] : [];
            $detail_attaches = array_key_exists('detail_attaches', $data) ? (array) $data['detail_attaches'] : [];
            $master_detail_attaches = array_key_exists('master_detail_attaches', $data) ? (array) $data['master_detail_attaches'] : [];
            $videos = array_key_exists('videos', $data) ? (array) $data['videos'] : [];
            $media = array_key_exists('media', $data) ? (array) $data['media'] : [];
            if ($media) {

                Media::whereIn('uuid', $media)->get()->each(fn($n) => $n->delete());
            }
            if ($overview_attaches) {
                foreach ($overview_attaches as $file) {
                    if (!Str::isUuid($file['uuid'])) {
                        if ($this->item->addMedia(Storage::path('public/images/' . $file['uuid']))->toMediaCollection(IssueVehicle::MEDIA_OVERVIEW)) {
                            Storage::delete('public/images/' . $file['uuid']);
                        }
                    }
                }
            }
            if ($master_overview_attaches) {
                foreach ($master_overview_attaches as $file) {
                    if (!Str::isUuid($file['uuid'])) {
                        if ($this->item->addMedia(Storage::path('public/images/' . $file['uuid']))->toMediaCollection(IssueVehicle::MEDIA_MASTER_OVERVIEW)) {
                            Storage::delete('public/images/' . $file['uuid']);
                        }
                    }
                }
            }
            if ($detail_attaches) {
                foreach ($detail_attaches as $file) {
                    if (!Str::isUuid($file['uuid'])) {
                        if ($this->item->addMedia(Storage::path('public/images/' . $file['uuid']))->toMediaCollection(IssueVehicle::MEDIA_DETAIL)) {
                            Storage::delete('public/images/' . $file['uuid']);
                        }
                    }
                }
            }
            if ($master_detail_attaches) {
                foreach ($master_detail_attaches as $file) {
                    if (!Str::isUuid($file['uuid'])) {
                        if ($this->item->addMedia(Storage::path('public/images/' . $file['uuid']))->toMediaCollection(IssueVehicle::MEDIA_MASTER_DETAIL)) {
                            Storage::delete('public/images/' . $file['uuid']);
                        }
                    }
                }
            }
            if ($videos) {
                foreach ($videos as $file) {
                    if (!Str::isUuid($file['uuid'])) {
                        if ($item = $this->item->addMedia(Storage::path('public/video/' . $file['uuid']))->toMediaCollection(IssueVehicle::MEDIA_VIDEO)) {
                            Storage::delete('public/video/' . $file['uuid']);
                            $poster = array_key_exists('poster', $file) ? Storage::path('public/screenshot/' . Str::afterLast($file['poster'], '/')) : null;
                            if (file_exists($poster)) {
                                @rename($poster, str_replace('.' . $item->extension, '.jpg', $item->getPath()));
                            }
                        }
                    }
                }
            }
        }

    }

    /**
     * 检查是否可关闭问题
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string  $id
     * @return void
     * 
     * @throws ValidationException
     */
    private function canClose(string $id)
    {
        if (!IssueVehicle::where(['id' => $id, 'is_block' => false,'block_status'=>IssueVehicle::BLOCK_STATUS_SUCCESS])->count()) {
            throw ValidationException::withMessages(['status.incorrect' => '车辆未放行,无法关闭问题']);
        }
    }
}