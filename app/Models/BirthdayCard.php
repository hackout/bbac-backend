<?php

namespace App\Models;

use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * 生日卡模板
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * 
 * @property string $id 主键
 * @property string $name 模板名称
 * @property array $design 设计参数
 * @property-read ?string $thumbnail 背景图
 * @property-read ?string $example 背景图
 * @property-read ?Collection<Media> $media 附件
 * @property-read ?Collection<Profile> $profiles 档案资料
 */
class BirthdayCard extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, PrimaryKeyUuidTrait;

    const MEDIA_FILE = "file";

    const MEDIA_EXAMPLE = "example";

    protected $fillable = [
        'id',
        'name',
        'design',
    ];

    public $casts = [
        'design' => 'array'
    ];

    public $appends = ['thumbnail','example'];

    public $hidden = ['media'];

    public function getThumbnailAttribute()
    {
        return $this->getFirstMediaUrl(self::MEDIA_FILE);
    }

    public function getExampleAttribute()
    {
        return $this->getFirstMediaUrl(self::MEDIA_EXAMPLE);
    }

    /**
     * 获取下级档案
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return HasMany|null|Collection|Model
     */
    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }
}
