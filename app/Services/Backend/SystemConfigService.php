<?php
namespace App\Services\Backend;

use App\Models\SystemConfig;
use App\Services\Service;
use Illuminate\Support\Collection;

/**
 * 系统参数服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class SystemConfigService extends Service
{

    public ?string $className = SystemConfig::class;

    /**
     * 查询数据列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return Collection
     */
    public function getList(): Collection
    {
        return parent::getAll([
            'code',
            'name',
            'value',
            'type'
        ]);
    }

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

    /**
     * 保存设置内容
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @return bool
     */
    public function setData(array $data)
    {
        return parent::quick('content', $data);
    }

    public function getValidation(): array
    {
        $settings = parent::getAll([
            'code',
            'label',
            'type'
        ])->toArray();
        $rules = [];
        $messages = [];
        $emptyText = '不能为空';
        $incorrectText = '参数不正确';
        foreach ($settings as $rs) {
            switch ($rs['type']) {
                case SystemConfig::TYPE_INPUT:
                case SystemConfig::TYPE_TEXTAREA:
                case SystemConfig::TYPE_RICHTEXT:
                case SystemConfig::TYPE_IMAGE:
                case SystemConfig::TYPE_IMAGES:
                    $rules[$rs['code']] = "required";
                    $messages[$rs['code'] . '.required'] = $rs['label'] . $emptyText;
                    break;
                case SystemConfig::TYPE_SWITCH:
                    $rules[$rs['code']] = "required|in:on,off";
                    $messages[$rs['code'] . '.required'] = $rs['label'] . $emptyText;
                    $messages[$rs['code'] . '.in'] = $rs['label'] . $incorrectText;
                    break;
                case SystemConfig::TYPE_NUMERIC:
                    $rules[$rs['code']] = "required|integer";
                    $messages[$rs['code'] . '.required'] = $rs['label'] . $emptyText;
                    $messages[$rs['code'] . '.integer'] = $rs['label'] . $incorrectText;
                    break;
            }
        }
        return [
            'rules' => $rules,
            'messages' => $messages
        ];
    }
}