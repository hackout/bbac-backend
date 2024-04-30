<?php
namespace App\Services\Private;

use App\Services\Service;
use App\Models\CommitVehicle;
use App\Models\ExamineVehicle;
use App\Models\CommitVehicleItem;
use App\Models\ExamineVehicleItem;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * 考核模板-考核项服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class ExamineVehicleItemService extends Service
{

    public ?string $className = ExamineVehicleItem::class;

    /**
     * 添加考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  ExamineVehicle $examine
     * @param  CommitVehicle  $commit
     * @return void
     */
    public function createByCommit(ExamineVehicle $examine, CommitVehicle $commit)
    {
        if ($commit->items->count()) {
            $that = $this;
            $commit->items->each(function (CommitVehicleItem $item) use ($examine, $that) {
                $sql = [
                    'examine_vehicle_id' => $examine->id,
                    'commit_vehicle_item_id' => $item->id,
                    'unique_id' => $item->unique_id,
                    'content' => $item->content,
                    'content_en' => $item->content_en,
                    'standard' => $item->standard,
                    'standard_en' => $item->standard_en,
                    'other' => $item->other,
                    'other_en' => $item->other_en,
                    'type' => $item->type,
                    'sort_order' => $item->sort_order,
                    'files' => $item->getMedia(CommitVehicleItem::MEDIA_FILE)->map(fn($media) => $media->getPath())->toArray()
                ];
                $that->create($sql);
            });
        }
    }

    public function create(array $data)
    {
        $files = $data['files'];
        unset($data['files']);
        if (parent::create($data)) {
            if ($files) {
                foreach ($files as $file) {
                    $this->item->addMedia($file)->toMediaCollection(ExamineVehicleItem::MEDIA_FILE);
                }
            }
        }
    }

    public function update(string|int $id, array $data)
    {
        $files = $data['files'];
        unset($data['files']);
        if (parent::update($id, $data)) {
            $this->item->media->each(fn(Media $media) => $media->delete());
            if ($files) {
                foreach ($files as $file) {
                    $this->item->addMedia($file)->toMediaCollection(ExamineVehicleItem::MEDIA_FILE);
                }
            }
        }
    }

    /**
     * 更新考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  ExamineVehicle $examine
     * @param  CommitVehicle  $commit
     * @return void
     */
    public function updateByCommit(ExamineVehicle $examine, CommitVehicle $commit)
    {
        $commitItems = $commit->items;
        $examineItems = $examine->items;
        $updated = [];
        $created = [];
        $deleted = [];
        if ($examineItems->count()) {
            $examineItems->each(function (CommitVehicleItem $item) use ($commitItems, &$deleted) {
                if (!$commitItems->filter(fn($n) => $n->unique_id == $item->unique_id)->first()) {
                    $deleted[] = $item->id;
                }
            });
        }
        if ($commitItems->count()) {
            $commitItems->each(function (CommitVehicleItem $item) use ($examineItems, &$created, $examine) {
                if (!$examineItems->filter(fn($n) => $n->unique_id == $item->unique_id)->first()) {
                    $created[] = [
                        'examine_vehicle_id' => $examine->id,
                        'commit_vehicle_item_id' => $item->id,
                        'unique_id' => $item->unique_id,
                        'content' => $item->content,
                        'content_en' => $item->content_en,
                        'standard' => $item->standard,
                        'standard_en' => $item->standard_en,
                        'other' => $item->other,
                        'other_en' => $item->other_en,
                        'type' => $item->type,
                        'sort_order' => $item->sort_order,
                        'files' => $item->getMedia(CommitVehicleItem::MEDIA_FILE)->map(fn($media) => $media->getPath())->toArray()
                    ];
                }
            });
        }
        if ($commitItems->count() && $examineItems->count()) {
            $examineItems->each(function ($item) use ($commitItems, &$updated) {
                if (!$commit = $commitItems->filter(fn($n) => $n->unique_id == $item->unique_id)->first()) {
                    $updated[$item->id] = [
                        'content' => $commit->content,
                        'content_en' => $commit->content_en,
                        'standard' => $commit->standard,
                        'standard_en' => $commit->standard_en,
                        'other' => $commit->other,
                        'other_en' => $commit->other_en,
                        'type' => $commit->type,
                        'sort_order' => $commit->sort_order,
                        'files' => $commit->getMedia(CommitVehicleItem::MEDIA_FILE)->map(fn($media) => $media->getPath())->toArray()
                    ];
                }
            });
        }

        if ($deleted) {
            $examineItems->filter(fn(ExamineVehicleItem $item) => in_array($item->id, $deleted))->values()->each(fn($item) => $item->delete());
        }

        if ($created) {
            foreach ($created as $create) {
                $this->create($create);
            }
        }

        if ($updated) {
            foreach ($updated as $item_id => $update) {
                $this->update($item_id, $update);
            }
        }
        parent::clearCache();
    }

}