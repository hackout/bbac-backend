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
 * 产品考核-考核项
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property string $id 主键
 * @property string $unique_id 唯一标识
 * @property string $commit_product_id 考核ID
 * @property ?string $part_id 零件清单ID
 * @property ?string $name 测量项
 * @property ?string $name_en 测量项(英)
 * @property ?string $eye 目视检查项
 * @property ?string $eye_en 目视检查项(英)
 * @property ?string $content 中文描述
 * @property ?string $content_en 英文描述
 * @property ?string $standard 检查标准
 * @property ?string $standard_en 检查标准(英)
 * @property ?string $number 检查数量
 * @property ?float $lower_limit 测量下限
 * @property ?float $upper_limit 测量上限
 * @property ?string $unit 测量单位
 * @property ?string $torque 拧紧扭矩要求
 * @property ?bool $is_ds 是否DS
 * @property ?bool $is_scan 是否扫码
 * @property ?bool $is_camera 是否拍照
 * @property ?string $scan 扫码提示
 * @property ?string $camera 拍照提示
 * @property ?string $record 记录提示
 * @property ?float $process 扫码进度
 * @property int $type 考核类型
 * @property int $sort_order 工序号
 * @property ?array<array<string,string|int>> $options 附加选项
 * @property-read ?Part $part 零件清单
 * @property-read ?Carbon $created_at 创建时间
 * @property-read ?Carbon $updated_at 更新时间
 * @property-read ?CommitProduct $commit 考核模板
 * @property-read ?Collection<Media> $media 附件
 * @property-read ?array<array<string,string>> $thumbnails 图示
 */
class CommitProductItem extends Model implements HasMedia
{
    use HasFactory, PrimaryKeyUuidTrait, InteractsWithMedia;

    /**
     * 附件Key
     */
    const MEDIA_FILE = 'file';

    protected $fillable = [
        'id',
        'unique_id',
        'commit_product_id',
        'part_id',
        'name',
        'name_en',
        'content',
        'content_en',
        'standard',
        'standard_en',
        'eye',
        'eye_en',
        'number',
        'lower_limit',
        'upper_limit',
        'unit',
        'torque',
        'is_ds',
        'is_scan',
        'is_camera',
        'scan',
        'camera',
        'record',
        'process',
        'type',
        'sort_order',
        'options',
    ];

    public $casts = [
        'lower_limit' => 'decimal:2',
        'upper_limit' => 'decimal:2',
        'number' => 'integer',
        'is_scan' => 'boolean',
        'is_camera' => 'boolean',
        'is_ds' => 'boolean',
        'process' => 'decimal:2',
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
     * @return null|BelongsTo<CommitProduct>|CommitProduct
     */
    public function commit()
    {
        return $this->belongsTo(CommitProduct::class);
    }

    /**
     * 零件清单
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<Part>|Part
     */
    public function part()
    {
        return $this->belongsTo(Part::class);
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
