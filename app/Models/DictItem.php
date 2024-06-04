<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * 字典键值表
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * 
 * @property int $id 主键
 * @property int $dict_id 字典ID
 * @property string $name 标签
 * @property string $content 键值
 * @property int $sort_order 排序
 * @property ?Carbon $created_at 创建时间
 * @property ?Carbon $updated_at 更新时间
 * @property-read ?string $thumbnail 缩率图
 * @property-read BelongsTo<Dict> $dict 字典
 * @property-read Collection<Media> $media 附件
 * 
 */
class DictItem extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    const MEDIA_FILE = 'file';

    protected $fillable = [
        'dict_id',
        'name',
        'content',
        'sort_order',
    ];

    public $casts = [
        'dict_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public $appends = [
        'thumbnail'
    ];

    public $hidden = [
        'media'
    ];

    /**
     * 字典
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<Dict>|Dict|Model
     */
    public function dict()
    {
        return $this->belongsTo(Dict::class);
    }

    public function getThumbnailAttribute()
    {
        return $this->getFirstMediaUrl(self::MEDIA_FILE);
    }
}
