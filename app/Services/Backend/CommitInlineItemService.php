<?php
namespace App\Services\Backend;

use App\Services\Service;
use Illuminate\Support\Str;
use App\Models\CommitInlineItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * 整车服务-考核项服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class CommitInlineItemService extends Service
{

    public ?string $className = CommitInlineItem::class;

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
            'commit_inline_id' => $id
        ]);
        $this->orderKey = 'sort_order';
        $this->orderType = 'desc';
        return parent::getAll()->map(function (CommitInlineItem $item) {
            return [
                'id' => $item->id,
                'station' => $item->station,
                'name' => $item->name,
                'content' => $item->content,
                'content_en' => $item->content_en,
                'standard' => $item->standard,
                'standard_en' => $item->standard_en,
                'number' => $item->number,
                'special' => $item->special ?? '',
                'gluing' => $item->gluing,
                'bolt_number' => $item->bolt_number,
                'bolt_model' => $item->bolt_model ?? '',
                'bolt_type' => $item->bolt_type ?? '',
                'bolt_status' => $item->bolt_status ?? '',
                'lower_limit' => $item->lower_limit ?? '',
                'upper_limit' => $item->upper_limit ?? '',
                'unit' => $item->unit,
                'type' => $item->type ?? '',
                'sort_order' => $item->sort_order ?? '',
                'options' => $item->options,
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
    public function updateOrder(array $list): array
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
            'commit_inline_id' => $id,
            'name' => array_key_exists('name', $data) ? trim($data['name']) : null,
            'station' => array_key_exists('station', $data) ? trim($data['station']) : null,
            'content' => array_key_exists('content', $data) ? trim($data['content']) : null,
            'content_en' => array_key_exists('content_en', $data) ? trim($data['content_en']) : null,
            'standard' => array_key_exists('standard', $data) ? trim($data['standard']) : null,
            'standard_en' => array_key_exists('standard_en', $data) ? trim($data['standard_en']) : null,
            'number' => array_key_exists('number', $data) ? intval(trim($data['number'])) : 0,
            'gluing' => array_key_exists('gluing', $data) ? trim($data['gluing']) : null,
            'bolt_number' => array_key_exists('bolt_number', $data) ? trim($data['bolt_number']) : null,
            'unit' => array_key_exists('unit', $data) ? trim($data['unit']) : 'Nm',
            'special' => array_key_exists('special', $data) ? (int) $data['special'] : 0,
            'bolt_model' => array_key_exists('bolt_model', $data) ? (int) $data['bolt_model'] : 0,
            'bolt_type' => array_key_exists('bolt_type', $data) ? (int) $data['bolt_type'] : 0,
            'bolt_status' => array_key_exists('bolt_status', $data) ? (int) $data['bolt_status'] : 0,
            'lower_limit' => array_key_exists('lower_limit', $data) ? (float) $data['lower_limit'] : 0,
            'upper_limit' => array_key_exists('upper_limit', $data) ? (float) $data['upper_limit'] : 0,
            'sort_order' => array_key_exists('sort_order', $data) ? (int) $data['sort_order'] : 0,
            'type' => array_key_exists('type', $data) ? (int) $data['type'] : 0,
            'options' => [],
        ];
        if ($sql['number']) {
            for ($i = 0; $i < $sql['number']; $i++) {
                $sql['options'][] = [
                    'sort_order' => $i + 1,
                    'name' => $sql['name'] . $i + 1
                ];
            }
        }
        $thumbnail = array_key_exists('thumbnail', $data) && $data['thumbnail'] ? $data['thumbnail'] : [];

        if (parent::create($sql)) {
            foreach ($thumbnail as $thumb) {
                if ($this->item->addMedia(Storage::path('public/images/' . $thumb['uuid']))->toMediaCollection(CommitInlineItem::MEDIA_FILE)) {
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
            'name' => array_key_exists('name', $data) ? trim($data['name']) : null,
            'station' => array_key_exists('station', $data) ? trim($data['station']) : null,
            'content' => array_key_exists('content', $data) ? trim($data['content']) : null,
            'content_en' => array_key_exists('content_en', $data) ? trim($data['content_en']) : null,
            'standard' => array_key_exists('standard', $data) ? trim($data['standard']) : null,
            'standard_en' => array_key_exists('standard_en', $data) ? trim($data['standard_en']) : null,
            'number' => array_key_exists('number', $data) ? intval(trim($data['number'])) : 0,
            'gluing' => array_key_exists('gluing', $data) ? trim($data['gluing']) : null,
            'bolt_number' => array_key_exists('bolt_number', $data) ? trim($data['bolt_number']) : null,
            'unit' => array_key_exists('unit', $data) ? trim($data['unit']) : 'Nm',
            'special' => array_key_exists('special', $data) ? (int) $data['special'] : 0,
            'bolt_model' => array_key_exists('bolt_model', $data) ? (int) $data['bolt_model'] : 0,
            'bolt_type' => array_key_exists('bolt_type', $data) ? (int) $data['bolt_type'] : 0,
            'bolt_status' => array_key_exists('bolt_status', $data) ? (int) $data['bolt_status'] : 0,
            'lower_limit' => array_key_exists('lower_limit', $data) ? (float) $data['lower_limit'] : 0,
            'upper_limit' => array_key_exists('upper_limit', $data) ? (float) $data['upper_limit'] : 0,
            'sort_order' => array_key_exists('sort_order', $data) ? (int) $data['sort_order'] : 0,
            'type' => array_key_exists('type', $data) ? (int) $data['type'] : 0
        ];


        $thumbnail = array_key_exists('thumbnail', $data) && $data['thumbnail'] ? $data['thumbnail'] : [];

        if (parent::update($id, $sql)) {
            $thumbnails = $this->item->thumbnails;
            $uuidList = collect($thumbnail)->pluck('uuid')->toArray();
            $self = $this;
            collect($thumbnails)->each(function ($thumb) use ($self, $uuidList) {
                if (!in_array($thumb['uuid'], $uuidList)) {
                    $self->uploadDelete($thumb['uuid']);
                }
            });
            foreach ($thumbnail as $thumb) {
                if (!Str::isUuid($thumb['uuid'])) {
                    if ($this->item->addMedia(Storage::path('public/images/' . $thumb['uuid']))->toMediaCollection(CommitInlineItem::MEDIA_FILE)) {
                        Storage::delete('public/images/' . $thumb['uuid']);
                    }
                }
            }
        }
    }

    /**
     * 修改实际测量项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $id
     * @param  array  $data
     * @return void
     */
    public function updateOption(string $id,array $data)
    {
        parent::update($id,['options'=>$data]);
        parent::clearCache();
    }
}