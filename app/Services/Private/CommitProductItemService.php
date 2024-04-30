<?php
namespace App\Services\Private;

use App\Services\Service;
use Illuminate\Support\Str;
use App\Models\CommitProduct;
use App\Models\CommitProductItem;

/**
 * 产品考核-考核项(历史)服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property CommitProductItem $item
 */
class CommitProductItemService extends Service
{
    public ?string $className = CommitProductItem::class;

    /**
     * 复制上一个版本的考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  CommitProduct $commitProduct
     * @return void
     */
    public function copyItem(CommitProduct $commitProduct)
    {
        if (optional($commitProduct->parent)->items->count()) {
            $commitProduct->parent->items->each(function (CommitProductItem $item) use ($commitProduct) {
                $newItem = $item->replicate();
                $newItem->commit_product_id = $commitProduct->id;
                if ($newItem->save()) {
                    if ($item->media->count()) {
                        try {
                            $item->media->each(fn($media) => $newItem->addMedia($media->getPath())->toMediaCollection(CommitProductItem::MEDIA_FILE));
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
                    $this->item->addMedia($media)->toMediaCollection(CommitProductItem::MEDIA_FILE);
                }
            }
        }
    }
}
