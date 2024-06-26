<?php

namespace App\Models;

use Carbon\Carbon;
use Spatie\MediaLibrary\HasMedia;
use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 整车服务-考核项
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property string $id 主键
 * @property string $unique_id 唯一标识
 * @property string $commit_vehicle_id 考核模板ID
 * @property string $content 检查详情
 * @property string $content_en 检查详情(英)
 * @property string $standard 检查标准
 * @property string $standard_en 检查标准(英)
 * @property string $other 其他要求
 * @property string $other_en 其他要求(英)
 * @property int $type 检查类型
 * @property int $sort_order 工序号
 * @property-read ?Carbon $created_at 创建时间
 * @property-read ?Carbon $updated_at 更新时间
 * @property-read ?CommitVehicle $commit 考核模板
 * @property-read ?Collection<Media> $media 附件
 * @property-read ?array<array<string,string>> $thumbnails 图示
 */
class CommitVehicleItem extends Model implements HasMedia
{
    use HasFactory, PrimaryKeyUuidTrait, InteractsWithMedia;

    /**
     * 附件Key
     */
    const MEDIA_FILE = 'file';

    protected $fillable = [
        'id',
        'unique_id',
        'commit_vehicle_id',
        'content',
        'content_en',
        'standard',
        'standard_en',
        'other',
        'other_en',
        'type',
        'sort_order',
    ];

    public $casts = [
        'type' => 'integer',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public $appends = ['thumbnails'];

    public $hidden = ['media'];


    /**
     * 考核模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<CommitVehicle>|CommitVehicle
     */
    public function commit()
    {
        return $this->belongsTo(CommitVehicle::class);
    }

    public function getThumbnailsAttribute()
    {
        if (!$medias = $this->getMedia(self::MEDIA_FILE))
            return [];
        return $medias->map(function ($item) {
            return [
                'name' => $item->file_name,
                'url' => $item->original_url,
                'uuid' => $item->uuid
            ];
        });
    }
}
