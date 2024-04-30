<?php
namespace App\Services\Private;

use App\Services\Service;
use App\Models\CommitInline;
use App\Models\ExamineInline;
use App\Models\CommitInlineItem;
use App\Models\ExamineInlineItem;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * 考核模板-考核项服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class ExamineInlineItemService extends Service
{

    public ?string $className = ExamineInlineItem::class;

    /**
     * 添加考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  ExamineInline $examine
     * @param  CommitInline  $commit
     * @return void
     */
    public function createByCommit(ExamineInline $examine, CommitInline $commit)
    {
        if ($commit->items->count()) {
            $that = $this;
            $commit->items->each(function (CommitInlineItem $item) use ($examine, $that) {
                $sql = [
                    'examine_inline_id' => $examine->id,
                    'commit_inline_item_id' => $item->id,
                    'unique_id' => $item->unique_id,
                    'station' => $item->station,
                    'name' => $item->name,
                    'content' => $item->content,
                    'content_en' => $item->content_en,
                    'standard' => $item->standard,
                    'standard_en' => $item->standard_en,
                    'number' => $item->number,
                    'special' => $item->special,
                    'gluing' => $item->gluing,
                    'bolt_number' => $item->bolt_number,
                    'bolt_model' => $item->bolt_model,
                    'bolt_type' => $item->bolt_type,
                    'bolt_status' => $item->bolt_status,
                    'lower_limit' => $item->lower_limit,
                    'upper_limit' => $item->upper_limit,
                    'unit' => $item->unit,
                    'type' => $item->type,
                    'sort_order' => $item->sort_order,
                    'options' => $item->options,
                    'files' => $item->getMedia(CommitInlineItem::MEDIA_FILE)->map(fn($media) => $media->getPath())->toArray()
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
                    $this->item->addMedia($file)->toMediaCollection(ExamineInlineItem::MEDIA_FILE);
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
                    $this->item->addMedia($file)->toMediaCollection(ExamineInlineItem::MEDIA_FILE);
                }
            }
        }
    }

    /**
     * 更新考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  ExamineInline $examine
     * @param  CommitInline  $commit
     * @return void
     */
    public function updateByCommit(ExamineInline $examine, CommitInline $commit)
    {
        $commitItems = $commit->items;
        $examineItems = $examine->items;
        $updated = [];
        $created = [];
        $deleted = [];
        if ($examineItems->count()) {
            $examineItems->each(function (CommitInlineItem $item) use ($commitItems, &$deleted) {
                if (!$commitItems->filter(fn($n) => $n->unique_id == $item->unique_id)->first()) {
                    $deleted[] = $item->id;
                }
            });
        }
        if ($commitItems->count()) {
            $commitItems->each(function (CommitInlineItem $item) use ($examineItems, &$created, $examine) {
                if (!$examineItems->filter(fn($n) => $n->unique_id == $item->unique_id)->first()) {
                    $created[] = [
                        'examine_inline_id' => $examine->id,
                        'commit_inline_item_id' => $item->id,
                        'unique_id' => $item->unique_id,
                        'station' => $item->station,
                        'name' => $item->name,
                        'content' => $item->content,
                        'content_en' => $item->content_en,
                        'standard' => $item->standard,
                        'standard_en' => $item->standard_en,
                        'number' => $item->number,
                        'special' => $item->special,
                        'gluing' => $item->gluing,
                        'bolt_number' => $item->bolt_number,
                        'bolt_model' => $item->bolt_model,
                        'bolt_type' => $item->bolt_type,
                        'bolt_status' => $item->bolt_status,
                        'lower_limit' => $item->lower_limit,
                        'upper_limit' => $item->upper_limit,
                        'unit' => $item->unit,
                        'type' => $item->type,
                        'sort_order' => $item->sort_order,
                        'options' => $item->options,
                        'files' => $item->getMedia(CommitInlineItem::MEDIA_FILE)->map(fn($media) => $media->getPath())->toArray()
                    ];
                }
            });
        }
        if ($commitItems->count() && $examineItems->count()) {
            $examineItems->each(function ($item) use ($commitItems, &$updated) {
                if ($commit = $commitItems->filter(fn($n) => $n->unique_id == $item->unique_id)->first()) {
                    $updated[$item->id] = [
                        'station' => $commit->station,
                        'name' => $commit->name,
                        'content' => $commit->content,
                        'content_en' => $commit->content_en,
                        'standard' => $commit->standard,
                        'standard_en' => $commit->standard_en,
                        'number' => $commit->number,
                        'special' => $commit->special,
                        'gluing' => $commit->gluing,
                        'bolt_number' => $commit->bolt_number,
                        'bolt_model' => $commit->bolt_model,
                        'bolt_type' => $commit->bolt_type,
                        'bolt_status' => $commit->bolt_status,
                        'lower_limit' => $commit->lower_limit,
                        'upper_limit' => $commit->upper_limit,
                        'unit' => $commit->unit,
                        'type' => $commit->type,
                        'sort_order' => $commit->sort_order,
                        'options' => $commit->options,
                        'files' => $commit->getMedia(CommitInlineItem::MEDIA_FILE)->map(fn($media) => $media->getPath())->toArray()
                    ];
                }
            });
        }

        if ($deleted) {
            $examineItems->filter(fn(ExamineInlineItem $item) => in_array($item->id, $deleted))->values()->each(fn($item) => $item->delete());
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