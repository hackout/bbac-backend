<?php

namespace App\Models;

use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

/**
 * 版本考核项
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class CommitItem extends Model implements HasMedia
{
    use HasFactory, PrimaryKeyUuidTrait, InteractsWithMedia;

    /**
     * 附件Key
     */
    const MEDIA_FILE = 'file';

    /**
     * 扭矩监控
     */
    const TYPE_TORQUE = 1;

    /**
     * 尺寸测量
     */
    const TYPE_DIMENSIONAL = 2;

    /**
     * 外观检测
     */
    const TYPE_APPEARANCE = 3;

    /**
     * 过程监控
     */
    const TYPE_PROCESS = 4;

    /**
     * 墨水测试
     */
    const TYPE_INK = 5;

    /**
     * 撕胶测试
     */
    const TYPE_TEAR = 6;

    /**
     * 触发考核
     */
    const TYPE_TRIGGER = 7;

    /**
     * 试装支持
     */
    const TYPE_TRIAL = 8;

    /**
     * 项目支持
     */
    const TYPE_PROJECT = 9;


    /**
     * 测量检查
     */
    const TYPE_MEASUREMENT = 10;

    /**
     * 目视检查
     */
    const TYPE_VISUAL = 11;

    /**
     * 全部
     */
    const TYPE_ALL = 12;

    protected $fillable = [
        'id',
        'commit_id',
        'station',
        'sub_station',
        'name_zh',
        'name_en',
        'content_zh',
        'content_en',
        'standard_zh',
        'standard_en',
        'eye_zh',
        'eye_en',
        'number',
        'special',
        'gluing',
        'bolt_number',
        'bolt_model',
        'bolt_type',
        'bolt_status',
        'lower_limit',
        'upper_limit',
        'blot_close',
        'unit',
        'is_scan',
        'is_camera',
        'part_number',
        'process',
        'type',
        'sort_order',
    ];

    public $casts = [
        'sort_order' => 'integer',
        'type' => 'integer',
        'process' => 'integer',
        'is_scan' => 'boolean',
        'is_camera' => 'boolean',
        'lower_limit' => 'decimal:2',
        'upper_limit' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'bolt_status' => 'integer',
        'bolt_type' => 'integer',
        'bolt_model' => 'integer',
        'special' => 'integer'
    ];

    public $appends = ['thumbnails'];

    public $hidden = ['media'];

    public function commit()
    {
        return $this->belongsTo(Commit::class);
    }

    public function examine_item()
    {
        return $this->hasOne(ExamineItem::class);
    }

    public function options()
    {
        return $this->hasMany(CommitItemOption::class)->orderBy('sort_order', 'asc');
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
