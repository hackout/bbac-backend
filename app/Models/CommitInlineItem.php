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
 * 在线考核-考核项
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property string $id 主键
 * @property string $unique_id 唯一标识
 * @property string $commit_inline_id 考核ID
 * @property ?string $station 工位
 * @property ?string $name 检测项
 * @property ?string $content 中文描述
 * @property ?string $content_en 英文描述
 * @property ?string $standard 检查标准
 * @property ?string $standard_en 检查标准(英)
 * @property ?string $number 检查数量
 * @property int $special 特殊特性
 * @property ?string $gluing 墨水型号
 * @property ?string $bolt_number 螺栓编号
 * @property int $bolt_model 种类1
 * @property int $bolt_type 种类2
 * @property int $bolt_status 放行状态
 * @property ?float $lower_limit 测量下限
 * @property ?float $upper_limit 测量上限
 * @property ?string $unit 测量单位
 * @property int $type 考核类型
 * @property int $sort_order 工序号
 * @property ?array<array<string,string|int>> $options 附加选项
 * @property-read ?Carbon $created_at 创建时间
 * @property-read ?Carbon $updated_at 更新时间
 * @property-read ?CommitInline $commit 考核模板
 * @property-read ?Collection<Media> $media 附件
 * @property-read ?array<array<string,string>> $thumbnails 图示
 */
class CommitInlineItem extends Model implements HasMedia
{
    use HasFactory, PrimaryKeyUuidTrait, InteractsWithMedia;

    /**
     * 附件Key
     */
    const MEDIA_FILE = 'file';

    protected $fillable = [
        'id',
        'unique_id',
        'commit_inline_id',
        'station',
        'name',
        'content',
        'content_en',
        'standard',
        'standard_en',
        'number',
        'special',
        'gluing',
        'bolt_number',
        'bolt_model',
        'bolt_type',
        'bolt_status',
        'lower_limit',
        'upper_limit',
        'unit',
        'type',
        'sort_order',
        'options',
    ];

    public $casts = [
        'lower_limit' => 'decimal:2',
        'upper_limit' => 'decimal:2',
        'number' => 'integer',
        'special' => 'integer',
        'bolt_model' => 'integer',
        'bolt_type' => 'integer',
        'bolt_status' => 'integer',
        'type' => 'integer',
        'sort_order' => 'integer',
        'options' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public $appends = ['thumbnails'];

    public $hidden = ['media'];


    /**
     * 考核模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<CommitInline>|CommitInline
     */
    public function commit()
    {
        return $this->belongsTo(CommitInline::class);
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
