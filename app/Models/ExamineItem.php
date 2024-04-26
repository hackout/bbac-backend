<?php

namespace App\Models;

use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\InteractsWithMedia;
use Carbon\Carbon;

class ExamineItem extends Model implements HasMedia
{
    use HasFactory,PrimaryKeyUuidTrait,InteractsWithMedia;

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
        'examine_id',
        'commit_item_id',
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
        'bolt_close',
        'lower_limit',
        'upper_limit',
        'unit',
        'is_scan',
        'is_camera',
        'part_number',
        'process',
        'type',
        'sort_order',
    ];

    public $casts = [
        'type' => 'integer',
        'is_scan' => 'boolean',
        'process' => 'integer',
        'sort_order' => 'integer',
        'bolt_type' => 'integer',
        'bolt_status' => 'integer',
        'bolt_model' => 'integer',
        'special' => 'integer',
        'lower_limit' => 'decimal:2',
        'upper_limit' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * 考核模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<Examine>|Examine|Model
     */
    public function examine()
    {
        return $this->belongsTo(Examine::class);
    }

    /**
     * 实际测量项列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<ExamineItemOption>|HasMany
     */
    public function options()
    {
        return $this->hasMany(ExamineItemOption::class)->orderBy('sort_order','asc');
    }

    /**
     * 考核项历史项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<CommitItem>|CommitItem|Model
     */
    public function commit_item()
    {
        return $this->belongsTo(CommitItem::class);
    }
}