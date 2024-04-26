<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * 系统参数字典
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * 
 * @property string $code 字段主键
 * @property ?string $content 字典值
 * @property string $label 字典名
 * @property int $type 参数类型
 * @property-read null|array|int|bool|string $value 参数值
 */
class SystemConfig extends Model
{
    use HasFactory;

    /**
     * 单行文本
     */
    const TYPE_INPUT = 1;

    /**
     * Switch开关
     */
    const TYPE_SWITCH = 2;

    /**
     * 多行文本
     */
    const TYPE_TEXTAREA = 3;

    /**
     * 副文本
     */
    const TYPE_RICHTEXT = 4;

    /**
     * 单一图片
     */
    const TYPE_IMAGE = 5;

    /**
     * 多图
     */
    const TYPE_IMAGES = 6;

    /**
     * 数字
     */
    const TYPE_NUMERIC = 7;

    public $timestamps = false;

    protected $primaryKey = 'code';

    protected $fillable = [
        'code',
        'content',
        'label',
        'type'
    ];

    public $casts = [
        'type' => 'integer'
    ];

    public $appends = ['value'];

    public function getValueAttribute()
    {
        $result = $this->content;
        switch ($this->type) {
            case self::TYPE_INPUT:
            case self::TYPE_TEXTAREA:
            case self::TYPE_RICHTEXT:
                $result = $this->content;
                break;
            case self::TYPE_IMAGE:
                $result = Storage::url($this->content);
                break;
            case self::TYPE_IMAGES:
                $result = [];
                foreach (explode(',', $this->content) as $content) {
                    $result[] = Storage::url($content);
                }
                break;
            case self::TYPE_SWITCH:
                $result = $this->content == 'on';
                break;
            case self::TYPE_NUMERIC:
                $result = (int) $this->content;
                break;
        }
        return $result;
    }

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }
}
