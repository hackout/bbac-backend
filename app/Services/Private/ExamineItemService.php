<?php
namespace App\Services\Private;

use App\Models\Commit;
use App\Models\Examine;
use App\Models\ExamineItem;
use App\Services\Service;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * 考核模板-考核项服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class ExamineItemService extends Service
{

    public ?string $className = ExamineItem::class;

    /**
     * 添加版本
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Commit $commit
     * @return void
     */
    public function updateByCommit(Examine $examine, Commit $commit)
    {
        $commitItems = $commit->items;
        $examineItems = $examine->items;
        $updated = [];
        $created = [];
        $deleted = [];
        if ($examineItems->count()) {
            $examineItems->each(function ($item) use ($commitItems, &$deleted) {
                if (!$commitItems->filter(fn($n) => $n == $item->content_zh)->first()) {
                    $deleted[] = $item->id;
                }
            });
        }
        if ($commitItems->count()) {
            $commitItems->each(function ($item) use ($examineItems, &$created) {
                if (!$examineItems->filter(fn($n) => $n == $item->content_zh)->first()) {
                    $created[] = [
                        'commit_item_id' => $item->id,
                        'station' => $item->station,
                        'sub_station' => $item->sub_station,
                        'name_zh' => $item->name_zh,
                        'name_en' => $item->name_en,
                        'content_zh' => $item->content_zh,
                        'content_en' => $item->content_en,
                        'standard_zh' => $item->standard_zh,
                        'standard_en' => $item->standard_en,
                        'eye_zh' => $item->eye_zh,
                        'eye_en' => $item->eye_en,
                        'number' => $item->number,
                        'special' => $item->special,
                        'gluing' => $item->gluing,
                        'bolt_number' => $item->bolt_number,
                        'bolt_model' => $item->bolt_model,
                        'bolt_type' => $item->bolt_type,
                        'bolt_status' => $item->bolt_status,
                        'bolt_close' => $item->bolt_close,
                        'lower_limit' => $item->lower_limit,
                        'upper_limit' => $item->upper_limit,
                        'unit' => $item->unit,
                        'is_scan' => $item->is_scan,
                        'is_camera' => $item->is_camera,
                        'part_number' => $item->part_number,
                        'process' => $item->process,
                        'type' => $item->type,
                        'sort_order' => $item->sort_order,
                        'thumbnails' => $item->thumbnails,
                        'options' => $item->options->map(function ($option) {
                            return [
                                'sort_order' => $option->sort_order,
                                'name_zh' => $option->name_zh,
                                'name_en' => $option->name_en,
                            ];
                        })->toArray()
                    ];
                }
            });
        }
        if ($commitItems->count() && $examineItems->count()) {
            $examineItems->each(function ($item) use ($commitItems, &$updated) {
                if (!$commit = $commitItems->filter(fn($n) => $n == $item->content_zh)->first()) {
                    $updated[$item->id] = [
                        'commit_item_id' => $commit->id,
                        'station' => $commit->station,
                        'sub_station' => $commit->sub_station,
                        'name_zh' => $commit->name_zh,
                        'name_en' => $commit->name_en,
                        'content_zh' => $commit->content_zh,
                        'content_en' => $commit->content_en,
                        'standard_zh' => $commit->standard_zh,
                        'standard_en' => $commit->standard_en,
                        'eye_zh' => $commit->eye_zh,
                        'eye_en' => $commit->eye_en,
                        'number' => $commit->number,
                        'special' => $commit->special,
                        'gluing' => $commit->gluing,
                        'bolt_number' => $commit->bolt_number,
                        'bolt_model' => $commit->bolt_model,
                        'bolt_type' => $commit->bolt_type,
                        'bolt_status' => $commit->bolt_status,
                        'blot_close' => $commit->blot_close,
                        'lower_limit' => $commit->lower_limit,
                        'upper_limit' => $commit->upper_limit,
                        'unit' => $commit->unit,
                        'is_scan' => $commit->is_scan,
                        'is_camera' => $commit->is_camera,
                        'part_number' => $commit->part_number,
                        'process' => $commit->process,
                        'type' => $commit->type,
                        'sort_order' => $commit->sort_order,
                        'thumbnails' => $item->thumbnails,
                        'options' => $commit->options->map(function ($option) {
                            return [
                                'sort_order' => $option->sort_order,
                                'name_zh' => $option->name_zh,
                                'name_en' => $option->name_en,
                            ];
                        })->toArray()
                    ];
                }
            });
        }

        if ($deleted) {
            $examine->items->filter(fn($item) => in_array($item->id, $delete))->each(fn($item) => $item->delete());
        }

        if ($created) {
            foreach ($created as $create) {
                $options = $create['options'];
                $thumbnails = $create['thumbnails'];
                unset($create['options'], $create['thumbnails']);
                if ($item = $examine->items()->create($create)) {
                    $options && $item->options()->createMany($options);
                    if ($thumbnails) {
                        foreach ($thumbnails as $rs) {
                            if ($media = Media::where('uuid', $rs['uuid'])->first()) {
                                $item->addMedia($media->getPath())->toMediaCollection(ExamineItem::MEDIA_FILE);
                            }
                        }
                    }
                }
            }
        }

        if ($updated) {
            foreach ($updated as $item_id => $update) {
                $options = $update['options'];
                unset($update['options']);
                if ($item = $examineItems->where('id', $item_id)->first()) {
                    $item->fill($update);
                    if ($item->save()) {
                        $item->options && $item->options->each(fn($n) => $n->delete);
                        $options && $item->options()->createMany($options);
                        if ($thumbnails) {
                            $item->media()->delete();
                            foreach ($thumbnails as $rs) {
                                if ($media = Media::where('uuid', $rs['uuid'])->first()) {
                                    $item->addMedia($media->getPath())->toMediaCollection(ExamineItem::MEDIA_FILE);
                                }
                            }
                        }
                    }
                }
            }
        }
        parent::clearCache();
    }

}