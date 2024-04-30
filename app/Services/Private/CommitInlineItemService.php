<?php
namespace App\Services\Private;

use App\Models\CommitInline;
use App\Models\CommitInlineItem;
use App\Services\Service;
use Illuminate\Support\Str;

/**
 * 在线考核-考核项(历史)服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property CommitInlineItem $item
 */
class CommitInlineItemService extends Service
{
    public ?string $className = CommitInlineItem::class;

    /**
     * 复制上一个版本的考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  CommitInline $commitInline
     * @return void
     */
    public function copyItem(CommitInline $commitInline)
    {
        if (optional($commitInline->parent)->items->count()) {
            $commitInline->parent->items->each(function (CommitInlineItem $item) use ($commitInline) {
                $newItem = $item->replicate();
                $newItem->commit_inline_id = $commitInline->id;
                if ($newItem->save()) {
                    if ($item->media->count()) {
                        try {
                            $item->media->each(fn($media) => $newItem->addMedia($media->getPath())->toMediaCollection(CommitInlineItem::MEDIA_FILE));
                        } catch (\Exception $e) {
                            logger("复制附件失败:" . $e->getMessage());
                        }
                    }
                }
            });
        }
    }

    /**
     * 添加考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $item
     * @param  array $medias
     * @return void
     */
    public function addItemByCommit(array $item, array $medias = [])
    {
        $item['unique_id'] = Str::uuid();
        if (parent::create($item)) {

            if ($medias) {
                foreach ($medias as $media) {
                    $this->item->addMedia($media)->toMediaCollection(CommitInlineItem::MEDIA_FILE);
                }
            }
        }
    }
}
