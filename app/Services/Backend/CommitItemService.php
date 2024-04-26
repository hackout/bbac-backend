<?php
namespace App\Services\Backend;

use App\Models\User;
use App\Models\CommitItem;
use App\Services\Service;
use App\Traits\ExportTemplateTrait;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * 版本服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class CommitItemService extends Service
{
    use ExportTemplateTrait;

    public ?string $className = CommitItem::class;

    /**
     * 常规考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return array
     */
    public function getStandardOption(): array
    {
        return [
            CommitItem::TYPE_DIMENSIONAL,
            CommitItem::TYPE_TORQUE,
            CommitItem::TYPE_APPEARANCE,
            CommitItem::TYPE_PROCESS
        ];
    }

    /**
     * 涂胶考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return array
     */
    public function getGluingOption(): array
    {
        return [
            CommitItem::TYPE_INK,
            CommitItem::TYPE_TEAR
        ];
    }

    /**
     * 动态考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return array
     */
    public function getDynamicOption(): array
    {
        return [
            CommitItem::TYPE_PROJECT,
            CommitItem::TYPE_TRIAL,
            CommitItem::TYPE_TRIGGER
        ];
    }

    /**
     * 产品、整车考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return array
     */
    public function getOtherOption(): array
    {
        return [
            CommitItem::TYPE_VISUAL,
            CommitItem::TYPE_MEASUREMENT,
            CommitItem::TYPE_ALL
        ];
    }


    /**
     * 查询历史版本
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array      $data
     * @return Collection
     */
    public function getOption(array $data): Collection
    {
        $conditions = [
            'engine' => 'eq',
            'type' => 'eq',
            'sub_type' => 'eq'
        ];
        parent::listQuery($data, $conditions, [['is_valid', '=', false]]);
        return parent::getAll([
            'id as value',
            'name',
            'version'
        ]);
    }

    /**
     * 获取考核项列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $id
     * @return Collection
     */
    public function getList(string $id): Collection
    {
        $condition = [
            'commit_id' => 'eq',
        ];
        parent::listQuery(['commit_id' => $id], $condition);
        $this->orderKey = 'sort_order';
        $this->orderType = 'desc';
        return parent::getAll();
    }


    /**
     * 更新排序
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $list
     * @return void
     */
    public function updateOrder(array $list)
    {
        $sql = [];
        foreach ($list as $key => $rs) {
            $sql[$rs] = count($list) - $key - 1;
        }
        parent::quick('sort_order', $sql);
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
            'commit_id' => $id,
            'station' => array_key_exists('station', $data) ? trim($data['station']) : null,
            'sub_station' => array_key_exists('sub_station', $data) ? trim($data['sub_station']) : null,
            'name_zh' => array_key_exists('name_zh', $data) ? trim($data['name_zh']) : null,
            'name_en' => array_key_exists('name_en', $data) ? trim($data['name_en']) : null,
            'content_zh' => array_key_exists('content_zh', $data) ? trim($data['content_zh']) : null,
            'content_en' => array_key_exists('content_en', $data) ? trim($data['content_en']) : null,
            'standard_zh' => array_key_exists('standard_zh', $data) ? trim($data['standard_zh']) : null,
            'standard_en' => array_key_exists('standard_en', $data) ? trim($data['standard_en']) : null,
            'eye_zh' => array_key_exists('eye_zh', $data) ? trim($data['eye_zh']) : null,
            'eye_en' => array_key_exists('eye_en', $data) ? trim($data['eye_en']) : null,
            'number' => array_key_exists('number', $data) ? (int) $data['number'] : 1,
            'special' => array_key_exists('special', $data) ? (int) $data['special'] : 0,
            'gluing' => array_key_exists('gluing', $data) ? trim($data['gluing']) : null,
            'blot_close' => array_key_exists('blot_close', $data) ? trim($data['blot_close']) : null,
            'bolt_number' => array_key_exists('bolt_number', $data) ? trim($data['bolt_number']) : null,
            'bolt_model' => array_key_exists('bolt_model', $data) ? (int) $data['bolt_model'] : 0,
            'bolt_type' => array_key_exists('bolt_type', $data) ? (int) $data['bolt_type'] : 0,
            'bolt_status' => array_key_exists('bolt_status', $data) ? (int) $data['bolt_status'] : 0,
            'lower_limit' => array_key_exists('lower_limit', $data) ? (float) $data['lower_limit'] : 0,
            'upper_limit' => array_key_exists('upper_limit', $data) ? (float) $data['upper_limit'] : 0,
            'unit' => array_key_exists('unit', $data) ? trim($data['unit']) : null,
            'is_scan' => array_key_exists('is_scan', $data) ? (bool) $data['is_scan'] : false,
            'is_camera' => array_key_exists('is_camera', $data) ? (bool) $data['is_camera'] : false,
            'part_number' => array_key_exists('part_number', $data) ? trim($data['part_number']) : null,
            'process' => array_key_exists('process', $data) ? (float) $data['process'] : 0,
            'type' => array_key_exists('type', $data) ? (int) $data['type'] : 0,
            'sort_order' => array_key_exists('sort_order', $data) ? (int) $data['sort_order'] : 0,
        ];


        $thumbnail = array_key_exists('thumbnail', $data) && $data['thumbnail'] ? $data['thumbnail'] : [];

        if (parent::create($sql)) {
            foreach ($thumbnail as $thumb) {
                if ($this->item->addMedia(Storage::path('public/images/' . $thumb['uuid']))->toMediaCollection(CommitItem::MEDIA_FILE)) {
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
            'station' => array_key_exists('station', $data) ? trim($data['station']) : null,
            'sub_station' => array_key_exists('sub_station', $data) ? trim($data['sub_station']) : null,
            'name_zh' => array_key_exists('name_zh', $data) ? trim($data['name_zh']) : null,
            'name_en' => array_key_exists('name_en', $data) ? trim($data['name_en']) : null,
            'content_zh' => array_key_exists('content_zh', $data) ? trim($data['content_zh']) : null,
            'content_en' => array_key_exists('content_en', $data) ? trim($data['content_en']) : null,
            'standard_zh' => array_key_exists('standard_zh', $data) ? trim($data['standard_zh']) : null,
            'standard_en' => array_key_exists('standard_en', $data) ? trim($data['standard_en']) : null,
            'eye_zh' => array_key_exists('eye_zh', $data) ? trim($data['eye_zh']) : null,
            'eye_en' => array_key_exists('eye_en', $data) ? trim($data['eye_en']) : null,
            'number' => array_key_exists('number', $data) ? (int) $data['number'] : 1,
            'special' => array_key_exists('special', $data) ? (int) $data['special'] : 0,
            'gluing' => array_key_exists('gluing', $data) ? trim($data['gluing']) : null,
            'blot_close' => array_key_exists('blot_close', $data) ? trim($data['blot_close']) : null,
            'bolt_number' => array_key_exists('bolt_number', $data) ? trim($data['bolt_number']) : null,
            'bolt_model' => array_key_exists('bolt_model', $data) ? (int) $data['bolt_model'] : 0,
            'bolt_type' => array_key_exists('bolt_type', $data) ? (int) $data['bolt_type'] : 0,
            'bolt_status' => array_key_exists('bolt_status', $data) ? (int) $data['bolt_status'] : 0,
            'lower_limit' => array_key_exists('lower_limit', $data) ? (float) $data['lower_limit'] : 0,
            'upper_limit' => array_key_exists('upper_limit', $data) ? (float) $data['upper_limit'] : 0,
            'unit' => array_key_exists('unit', $data) ? trim($data['unit']) : null,
            'is_scan' => array_key_exists('is_scan', $data) ? (bool) $data['is_scan'] : false,
            'is_camera' => array_key_exists('is_camera', $data) ? (bool) $data['is_camera'] : false,
            'part_number' => array_key_exists('part_number', $data) ? trim($data['part_number']) : null,
            'process' => array_key_exists('process', $data) ? (float) $data['process'] : 0,
            'type' => array_key_exists('type', $data) ? (int) $data['type'] : 0,
            'sort_order' => array_key_exists('sort_order', $data) ? (int) $data['sort_order'] : 0,
        ];


        $thumbnail = array_key_exists('thumbnail', $data) && $data['thumbnail'] ? $data['thumbnail'] : [];

        if (parent::update($id, $sql)) {
            foreach ($thumbnail as $thumb) {
                if (!Str::isUuid($thumb['uuid'])) {
                    if ($this->item->addMedia(Storage::path('public/images/' . $thumb['uuid']))->toMediaCollection(CommitItem::MEDIA_FILE)) {
                        Storage::delete('public/images/' . $thumb['uuid']);
                    }
                }
            }
        }
    }
}