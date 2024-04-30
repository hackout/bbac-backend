<?php
namespace App\Services\Private;

use App\Models\CommitVehicle;
use App\Models\CommitVehicleItem;
use App\Services\Service;
use Illuminate\Support\Str;

/**
 * 整车服务-考核项(历史)服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property CommitVehicleItem $item
 */
class CommitVehicleItemService extends Service
{
    public ?string $className = CommitVehicleItem::class;

    /**
     * 复制上一个版本的考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  CommitVehicle $commitVehicle
     * @return void
     */
    public function copyItem(CommitVehicle $commitVehicle)
    {
        if (optional($commitVehicle->parent)->items->count()) {
            $commitVehicle->parent->items->each(function (CommitVehicleItem $item) use ($commitVehicle) {
                $newItem = $item->replicate();
                $newItem->commit_vehicle_id = $commitVehicle->id;
                if ($newItem->save()) {
                    if ($item->media->count()) {
                        try {
                            $item->media->each(fn($media) => $newItem->addMedia($media->getPath())->toMediaCollection(CommitVehicleItem::MEDIA_FILE));
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
                    $this->item->addMedia($media)->toMediaCollection(CommitVehicleItem::MEDIA_FILE);
                }
            }
        }
    }
}
