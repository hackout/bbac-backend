<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Carbon\Carbon;

/**
 * 扭矩数据库
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property string $id 主键
 * @property string $author_id 创建人
 * @property string $user_id 最后更新人
 * @property int $plant 工厂
 * @property int $line 产线
 * @property int $engine 机型
 * @property int $vehicle_type 车型
 * @property string $assembly_id 总成ID
 * @property string $number 螺栓编号
 * @property string $content_zh 中文说明
 * @property ?string $content_en 英文说明
 * @property int $quantity 螺栓数量
 * @property int $model 螺栓分类1
 * @property int $type 螺栓分类2
 * @property int $status 放行状态
 * @property int $stage 项目阶段
 * @property string $station 工位1
 * @property string $sub_station 工位2
 * @property int $special 特殊特性
 * @property ?string $param 参数
 * @property float $torque_target 目标扭矩
 * @property float $torque_lower 扭矩下限
 * @property float $torque_upper 扭矩上限
 * @property float $angle_target 角度标准
 * @property float $angle_lower 角度下限
 * @property float $angle_upper 角度上限
 * @property ?Carbon $lasted_at 最近放行时间
 * @property ?Carbon $expected_at 预计放行时间
 * @property ?Carbon $final_at 最终放行时间
 * @property string $start_torque 起始扭矩
 * @property string $residual_torque 转矩角
 * @property string $pfu_test PFU-测试值
 * @property string $pfu_lower PFU-考核下限
 * @property string $pfu_upper PFU-考核上限
 * @property string $pfu_early_lower PFU-预警上限
 * @property string $pfu_early_upper PFU-预警下限
 * @property string $l_pfu_test L-PFU-测试值
 * @property string $l_pfu_lower L-PFU-考核下限
 * @property string $l_pfu_upper L-PFU-考核上限
 * @property string $l_pfu_early_lower L-PFU-预警上限
 * @property string $l_pfu_early_upper L-PFU-预警下限
 * @property ?Carbon $created_at 添加时间
 * @property ?Carbon $updated_at 更新时间
 * @property-read int $actual_quantity 已录入螺栓数量
 * @property-read ?Assembly $assembly 总成
 * @property-read ?User $author 创建人
 * @property-read ?User $user 最后变更人
 * @property-read ?Collection $torque_items 螺栓信息
 * @property-read ?Collection $torque_change_records 变更记录
 * @property-read ?Collection $torque_item_details SPC信息
 * @property-read ?Collection $torque_item_fixtures 紧固参数
 * @property-read ?Collection $torque_item_monitors 监控参数
 * @property-read ?Collection<Media> $media 头像附件
 */
class Torque extends Model implements HasMedia
{
    use HasFactory, PrimaryKeyUuidTrait, InteractsWithMedia;

    const MEDIA_FILE  = "file";

    protected $fillable = [
        'id',
        'author_id',
        'user_id',
        'plant',
        'line',
        'engine',
        'vehicle_type',
        'assembly_id',
        'number',
        'content_zh',
        'content_en',
        'quantity',
        'model',
        'type',
        'status',
        'stage',
        'station',
        'sub_station',
        'special',
        'param',
        'torque_target',
        'torque_lower',
        'torque_upper',
        'angle_target',
        'angle_lower',
        'angle_upper',
        'lasted_at',
        'expected_at',
        'final_at',
        'start_torque',
        'residual_torque',
        'pfu_test',
        'pfu_lower',
        'pfu_upper',
        'pfu_early_lower',
        'pfu_early_upper',
        'l_pfu_test',
        'l_pfu_lower',
        'l_pfu_upper',
        'l_pfu_early_lower',
        'l_pfu_early_upper',
    ];

    public $casts = [
        'plant' => 'integer',
        'line' => 'integer',
        'engine' => 'integer',
        'vehicle_type' => 'integer',
        'quantity' => 'integer',
        'model' => 'integer',
        'type' => 'integer',
        'status' => 'integer',
        'stage' => 'integer',
        'special' => 'integer',
        'torque_target' => 'decimal:2',
        'torque_lower' => 'decimal:2',
        'torque_upper' => 'decimal:2',
        'angle_target' => 'decimal:2',
        'angle_lower' => 'decimal:2',
        'angle_upper' => 'decimal:2',
        'lasted_at' => 'datetime',
        'expected_at' => 'datetime',
        'final_at' => 'datetime',
        'start_torque' => 'decimal:2',
        'residual_torque' => 'decimal:2',
        'pfu_test' => 'decimal:2',
        'pfu_lower' => 'decimal:2',
        'pfu_upper' => 'decimal:2',
        'pfu_early_lower' => 'decimal:2',
        'pfu_early_upper' => 'decimal:2',
        'l_pfu_test' => 'decimal:2',
        'l_pfu_lower' => 'decimal:2',
        'l_pfu_upper' => 'decimal:2',
        'l_pfu_early_lower' => 'decimal:2',
        'l_pfu_early_upper' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'actual_quantity' => 'integer'
    ];

    public $appends = ['actual_quantity'];

    public $hidden = ['torque_items'];

    /**
     * 总成信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<Assembly>|Model
     */
    public function assembly()
    {
        return $this->belongsTo(Assembly::class);
    }

    /**
     * 创建人
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<User>|Model
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * 最后变更人
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<User>|Model
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 螺栓信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<TorqueItem>|HasMany
     */
    public function torque_items()
    {
        return $this->hasMany(TorqueItem::class);
    }

    /**
     * 扭矩SPC参数
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<TorqueItemDetail>|HasMany
     */
    public function torque_item_details()
    {
        return $this->hasMany(TorqueItemDetail::class);
    }

    /**
     * 紧固参数
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<TorqueItemFixture>|HasMany
     */
    public function torque_item_fixtures()
    {
        return $this->hasMany(TorqueItemFixture::class);
    }

    /**
     * 监控信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<TorqueItemMonitor>|HasMany
     */
    public function torque_item_monitors()
    {
        return $this->hasMany(TorqueItemMonitor::class);
    }

    /**
     * 变更记录
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<TorqueChangeRecord>|HasMany
     */
    public function torque_change_records()
    {
        return $this->hasMany(TorqueChangeRecord::class);
    }

    public function getActualQuantityAttribute()
    {
        return $this->torque_items->count();
    }
}
