<?php
namespace App\Services\Frontend;

use App\Models\BirthdayCard;
use App\Services\Service;

/**
 * 生日祝福卡模板服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class BirthdayCardService extends Service
{

    public ?string $className = BirthdayCard::class;

    /**
     * 获取生日卡参数
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $id
     * @return array
     */
    public function getTemplateData(string $id):array
    {
        $item = parent::findById($id);
        return [
            'template' => $item->getFirstMediaPath(BirthdayCard::MEDIA_FILE),
            'data' => $item->design
        ];
    }
}