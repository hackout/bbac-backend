<?php
namespace App\Services\Private;

use App\Models\SystemConfig;
use App\Services\Service;

/**
 * 字典服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class SystemConfigService extends Service
{

    public ?string $className = SystemConfig::class;

    /**
     * 通过标识获取键值
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string                            $code
     * @return null|array|integer|boolean|string
     */
    public function getValueByCode(string $code): null|array|int|bool|string
    {
        if (!$item = parent::findById($code)) {
            return null;
        }
        return $item->value;
    }

}