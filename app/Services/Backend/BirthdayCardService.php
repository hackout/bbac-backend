<?php
namespace App\Services\Backend;

use App\Models\BirthdayCard;
use App\Packages\ImagePlus\ImagePlus;
use App\Services\Service;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * 生日祝福卡模板服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class BirthdayCardService extends Service
{

    public ?string $className = BirthdayCard::class;

    /**
     * 获取祝福卡选项列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return Collection
     */
    public function getOptions():Collection
    {
        return parent::getAll([
            'id',
            'name',
            'example'
        ]);
    }

    /**
     * 查询祝福卡列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @return array
     */
    public function getList(array $data):array
    {
        $conditions = [
            'keyword' => ['search',['name']]
        ];
        parent::listQuery($data,$conditions);
        return parent::list();
    }

    public function create(array $data): bool
    {
        $sql = [
            'name' => $data['name'],
            'design' => $data['design']
        ];
        if ($result = parent::create($sql)) {
            $media = $this->item->addMediaFromBase64($data['file'])->toMediaCollection(BirthdayCard::MEDIA_FILE);
            $imagePlus = new ImagePlus($media->getPath());
            $examplePath = $imagePlus->makeBirthdayCard($data['user'], $data['design']);
            if ($examplePath) {
                $this->item->addMedia($examplePath)->toMediaCollection(BirthdayCard::MEDIA_EXAMPLE);
                @unlink($examplePath);
            }
        }
        return $result;
    }

    public function update(int|string $id, array $data): bool
    {
        $sql = [
            'name' => $data['name'],
            'design' => $data['design']
        ];
        if ($result = parent::update($id, $sql)) {
            if (Str::startsWith($data['file'], ['data:image'])) {
                $this->item->media && $this->item->media()->delete();
                $media = $this->item->addMediaFromBase64($data['file'])->toMediaCollection(BirthdayCard::MEDIA_FILE);
                $imagePlus = new ImagePlus($media->getPath());
                $examplePath = $imagePlus->makeBirthdayCard($data['user'], $data['design']);
                if ($examplePath) {
                    $this->item->addMedia($examplePath)->toMediaCollection(BirthdayCard::MEDIA_EXAMPLE);
                    @unlink($examplePath);
                }
            }

        }
        return $result;
    }
}