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
 */
class CommitVehicleItem extends Model implements HasMedia
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
}
