<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 计划任务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property string $id 主键
 * @property string $name 任务单名称
 * @property int $type 考核类型
 * @property ?string $examine_id 考核ID
 * @property int $plant 工厂
 * @property int $line 产线
 * @property int $engine 状态
 * @property int $status 项目阶段
 * @property ?string $assembly_id 总成ID
 * @property ?array $days 按日期生产
 * @property int $yield 排产台数
 * @property int $yield_unit 排产次数
 * @property bool $is_valid 是否有效
 * @property int $period 工时
 * @property-read ?Carbon $created_at 创建时间
 * @property-read ?Carbon $updated_at 更新时间
 * @property-read null|ExamineInline|ExamineProduct|ExamineVehicle $examine 考核模板
 * @property-read ?Assembly $assembly 总成
 * @property-read ?Collection<Task> $tasks 已生产任务单
 */
class TaskCron extends Model
{
    use HasFactory, PrimaryKeyUuidTrait;

    /**
     * 在线考核
     */
    const TYPE_INLINE = 1;

    /**
     * 产品考核
     */
    const TYPE_PRODUCT = 2;

    /**
     * 整车服务
     */
    const TYPE_SERVICE = 3;

    protected $fillable = [
        'name',
        'plant',
        'line',
        'type',
        'engine',
        'status',
        'examine_id',
        'assembly_id',
        'days',
        'yield',
        'yield_unit',
        'is_valid',
        'period',
    ];

    public $casts = [
        'plant' => 'integer',
        'line' => 'integer',
        'type' => 'integer',
        'engine' => 'integer',
        'status' => 'integer',
        'days' => 'array',
        'yield' => 'integer',
        'period' => 'decimal:2',
        'is_valid' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];


    /**
     * 总成号
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<Assembly>|Assembly|Model
     */
    public function assembly()
    {
        return $this->belongsTo(Assembly::class);
    }

    /**
     * 任务考核单
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<Task>|HasMany
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * 考核模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<ExamineInline|ExamineProduct|ExamineVehicle>|ExamineInline|ExamineProduct|ExamineVehicle
     */
    public function examine()
    {
        if ($this->type == self::TYPE_INLINE) {
            return $this->belongsTo(ExamineInline::class, 'examine_id');
        }
        if ($this->type == self::TYPE_PRODUCT) {
            return $this->belongsTo(ExamineProduct::class, 'examine_id');
        }
        return $this->belongsTo(ExamineVehicle::class, 'examine_id');
    }
}
