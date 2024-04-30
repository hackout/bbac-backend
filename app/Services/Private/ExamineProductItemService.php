<?php
namespace App\Services\Private;

use App\Services\Service;
use App\Models\CommitProduct;
use App\Models\ExamineProduct;
use App\Models\CommitProductItem;
use App\Models\ExamineProductItem;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * 产品考核-考核模板-考核项服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class ExamineProductItemService extends Service
{

    public ?string $className = ExamineProductItem::class;

    /**
     * 添加考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  ExamineProduct $examine
     * @param  CommitProduct  $commit
     * @return void
     */
    public function createByCommit(ExamineProduct $examine, CommitProduct $commit)
    {
        if ($commit->items->count()) {
            $that = $this;
            $commit->items->each(function (CommitProductItem $item) use ($examine, $that) {
                $sql = [
                    'examine_product_id' => $examine->id,
                    'commit_product_item_id' => $item->id,
                    'unique_id' => $item->unique_id,
                    'part_id' => $item->part_id,
                    'name' => $item->name,
                    'name_en' => $item->name_en,
                    'content' => $item->content,
                    'content_en' => $item->content_en,
                    'standard' => $item->standard,
                    'standard_en' => $item->standard_en,
                    'eye' => $item->eye,
                    'eye_en' => $item->eye_en,
                    'number' => $item->number,
                    'lower_limit' => $item->lower_limit,
                    'upper_limit' => $item->upper_limit,
                    'unit' => $item->unit,
                    'torque' => $item->torque,
                    'is_scan' => $item->is_scan,
                    'is_camera' => $item->is_camera,
                    'scan' => $item->scan,
                    'camera' => $item->camera,
                    'record' => $item->record,
                    'process' => $item->process,
                    'type' => $item->type,
                    'sort_order' => $item->sort_order,
                    'options' => $item->options,
                    'files' => $item->getMedia(CommitProductItem::MEDIA_FILE)->map(fn($media) => $media->getPath())->toArray()
                ];
                $that->create($sql);
            });
        }
    }

    /**
     * 添加数据
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @return void
     */
    public function create(array $data)
    {
        $files = $data['files'];
        unset($data['files']);
        if (parent::create($data)) {
            if ($files) {
                foreach ($files as $file) {
                    $this->item->addMedia($file)->toMediaCollection(ExamineProductItem::MEDIA_FILE);
                }
            }
        }
    }

    /**
     * 更新数据
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string|integer $id
     * @param  array          $data
     * @return void
     */
    public function update(string|int $id, array $data)
    {
        $files = $data['files'];
        unset($data['files']);
        if (parent::update($id, $data)) {
            $this->item->media->each(fn(Media $media) => $media->delete());
            if ($files) {
                foreach ($files as $file) {
                    $this->item->addMedia($file)->toMediaCollection(ExamineProductItem::MEDIA_FILE);
                }
            }
        }
    }

    /**
     * 更新考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  ExamineProduct $examine
     * @param  CommitProduct  $commit
     * @return void
     */
    public function updateByCommit(ExamineProduct $examine, CommitProduct $commit)
    {
        $commitItems = $commit->items;
        $examineItems = $examine->items;
        $updated = [];
        $created = [];
        $deleted = [];
        if ($examineItems->count()) {
            $examineItems->each(function (CommitProductItem $item) use ($commitItems, &$deleted) {
                if (!$commitItems->filter(fn($n) => $n->unique_id == $item->unique_id)->first()) {
                    $deleted[] = $item->id;
                }
            });
        }
        if ($commitItems->count()) {
            $commitItems->each(function (CommitProductItem $item) use ($examineItems, &$created, $examine) {
                if (!$examineItems->filter(fn($n) => $n->unique_id == $item->unique_id)->first()) {
                    $created[] = [
                        'examine_product_id' => $examine->id,
                        'commit_product_item_id' => $item->id,
                        'unique_id' => $item->unique_id,
                        'part_id' => $item->part_id,
                        'name' => $item->name,
                        'name_en' => $item->name_en,
                        'content' => $item->content,
                        'content_en' => $item->content_en,
                        'standard' => $item->standard,
                        'standard_en' => $item->standard_en,
                        'eye' => $item->eye,
                        'eye_en' => $item->eye_en,
                        'number' => $item->number,
                        'lower_limit' => $item->lower_limit,
                        'upper_limit' => $item->upper_limit,
                        'unit' => $item->unit,
                        'torque' => $item->torque,
                        'is_scan' => $item->is_scan,
                        'is_camera' => $item->is_camera,
                        'scan' => $item->scan,
                        'camera' => $item->camera,
                        'record' => $item->record,
                        'process' => $item->process,
                        'type' => $item->type,
                        'sort_order' => $item->sort_order,
                        'options' => $item->options,
                        'files' => $item->getMedia(CommitProductItem::MEDIA_FILE)->map(fn($media) => $media->getPath())->toArray()
                    ];
                }
            });
        }
        if ($commitItems->count() && $examineItems->count()) {
            $examineItems->each(function ($item) use ($commitItems, &$updated) {
                if ($commit = $commitItems->filter(fn($n) => $n->unique_id == $item->unique_id)->first()) {
                    $updated[$item->id] = [
                        'part_id' => $commit->part_id,
                        'name' => $commit->name,
                        'name_en' => $commit->name_en,
                        'content' => $commit->content,
                        'content_en' => $commit->content_en,
                        'standard' => $commit->standard,
                        'standard_en' => $commit->standard_en,
                        'eye' => $commit->eye,
                        'eye_en' => $commit->eye_en,
                        'number' => $commit->number,
                        'lower_limit' => $commit->lower_limit,
                        'upper_limit' => $commit->upper_limit,
                        'unit' => $commit->unit,
                        'torque' => $commit->torque,
                        'is_scan' => $commit->is_scan,
                        'is_camera' => $commit->is_camera,
                        'scan' => $commit->scan,
                        'camera' => $commit->camera,
                        'record' => $commit->record,
                        'process' => $commit->process,
                        'type' => $commit->type,
                        'sort_order' => $commit->sort_order,
                        'options' => $commit->options,
                        'files' => $commit->getMedia(CommitProductItem::MEDIA_FILE)->map(fn($media) => $media->getPath())->toArray()
                    ];
                }
            });
        }

        if ($deleted) {
            $examineItems->filter(fn(ExamineProductItem $item) => in_array($item->id, $deleted))->values()->each(fn($item) => $item->delete());
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