<?php
namespace App\Services\Backend;

use App\Services\Service;
use Illuminate\Support\Str;
use App\Models\CommitVehicleItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * 整车服务-考核项服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class CommitVehicleItemService extends Service
{

    public ?string $className = CommitVehicleItem::class;

    /**
     * 获取考核项列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $id
     * @return Collection
     */
    public function getList(string $id): Collection
    {
        parent::setQuery([
            'commit_vehicle_id' => $id
        ]);
        $this->orderKey = 'sort_order';
        $this->orderType = 'desc';
        return parent::getAll()->map(function (CommitVehicleItem $item) {
            return [
                'id' => $item->id,
                'content' => $item->content,
                'content_en' => $item->content_en,
                'standard' => $item->standard,
                'standard_en' => $item->standard_en,
                'other' => $item->other,
                'other_en' => $item->other_en,
                'type' => $item->type ?? '',
                'sort_order' => $item->sort_order,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
                'thumbnails' => $item->thumbnails
            ];
        });
    }


    /**
     * 更新排序
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $list
     * @return array
     */
    public function updateOrder(array $list):array
    {
        $sql = [];
        foreach ($list as $key => $rs) {
            $sql[$rs] = count($list) - $key;
        }
        parent::quick('sort_order', $sql);
        return $sql;
    }

    /**
     * 考核项图示上传
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  UploadedFile $fileBag
     * @return array
     */
    public function upload(UploadedFile $fileBag): array
    {
        $file = Storage::putFile('public/images', $fileBag);
        $result = [
            'url' => Storage::url($file),
            'name' => $fileBag->getClientOriginalName(),
            'uuid' => Str::afterLast($file, '/')
        ];
        return $result;
    }

    /**
     * 删除考核项图示
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
     * 添加考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $id
     * @param  array  $data
     * @return void
     */
    public function createByCommitId(string $id, array $data)
    {
        $sql = [
            'commit_vehicle_id' => $id,
            'content' => array_key_exists('content', $data) ? trim($data['content']) : null,
            'content_en' => array_key_exists('content_en', $data) ? trim($data['content_en']) : null,
            'standard' => array_key_exists('standard', $data) ? trim($data['standard']) : null,
            'standard_en' => array_key_exists('standard_en', $data) ? trim($data['standard_en']) : null,
            'other' => array_key_exists('other', $data) ? trim($data['other']) : null,
            'other_en' => array_key_exists('other_en', $data) ? trim($data['other_en']) : null,
            'type' => array_key_exists('type', $data) ? (int) $data['type'] : 0,
            'sort_order' => array_key_exists('sort_order', $data) ? (int) $data['sort_order'] : 0,
        ];


        $thumbnail = array_key_exists('thumbnail', $data) && $data['thumbnail'] ? $data['thumbnail'] : [];

        if (parent::create($sql)) {
            foreach ($thumbnail as $thumb) {
                if ($this->item->addMedia(Storage::path('public/images/' . $thumb['uuid']))->toMediaCollection(CommitVehicleItem::MEDIA_FILE)) {
                    Storage::delete('public/images/' . $thumb['uuid']);
                }
            }
        }
    }

    /**
     * 编辑考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $id
     * @param  array  $data
     * @return void
     */
    public function updateByCommitId(string $id, array $data)
    {
        $sql = [
            'content' => array_key_exists('content', $data) ? trim($data['content']) : null,
            'content_en' => array_key_exists('content_en', $data) ? trim($data['content_en']) : null,
            'standard' => array_key_exists('standard', $data) ? trim($data['standard']) : null,
            'standard_en' => array_key_exists('standard_en', $data) ? trim($data['standard_en']) : null,
            'other' => array_key_exists('other', $data) ? trim($data['other']) : null,
            'other_en' => array_key_exists('other_en', $data) ? trim($data['other_en']) : null,
            'type' => array_key_exists('type', $data) ? (int) $data['type'] : 0,
            'sort_order' => array_key_exists('sort_order', $data) ? (int) $data['sort_order'] : 0,
        ];


        $thumbnail = array_key_exists('thumbnail', $data) && $data['thumbnail'] ? $data['thumbnail'] : [];

        if (parent::update($id, $sql)) {
            $thumbnails = $this->item->thumbnails;
            $uuidList = collect($thumbnail)->pluck('uuid')->toArray();
            $self = $this;
            collect($thumbnails)->each(function($thumb) use($self,$uuidList){
                if(!in_array($thumb['uuid'],$uuidList))
                {
                    $self->uploadDelete($thumb['uuid']);
                }
            });
            foreach ($thumbnail as $thumb) {
                if (!Str::isUuid($thumb['uuid'])) {
                    if ($this->item->addMedia(Storage::path('public/images/' . $thumb['uuid']))->toMediaCollection(CommitVehicleItem::MEDIA_FILE)) {
                        Storage::delete('public/images/' . $thumb['uuid']);
                    }
                }
            }
        }
    }
}