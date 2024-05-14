<?php

namespace App\Models;

use Carbon\Carbon;
use Spatie\MediaLibrary\HasMedia;
use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 任务单模型
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property string $id 主键
 * @property string $name 任务名称
 * @property ?string $user_id 用户ID
 * @property ?string $issue_id 问题追踪ID
 * @property ?string $examine_id 考核ID
 * @property ?string $task_cron_id 任务生产ID
 * @property ?array $extra 扩展信息
 * @property int $type 考核类型
 * @property int $plant 工厂
 * @property int $line 产线
 * @property int $engine 机型
 * @property int $status 项目阶段
 * @property ?string $eb_number 发动机号
 * @property ?string $remark 备注信息
 * @property ?string $assembly_id 总成
 * @property int $task_status 任务状态
 * @property string $number 任务单号
 * @property ?float $period 工时
 * @property ?Carbon $start_at 开始时间
 * @property ?Carbon $end_at 结束时间
 * @property ?Carbon $valid_at 过期时间
 * @property ?array $original_examine 原始信息
 * @property-read int $items_count 考核项数量
 * @property-read ?Carbon $created_at 创建时间
 * @property-read ?Carbon $updated_at 更新时间
 * @property-read ?Assembly $assembly 总成
 * @property-read null|ExamineInline|ExamineProduct|ExamineVehicle $examine 考核模板
 * @property-read ?User $user 考核项数量
 * @property-read ?TaskCron $task_cron 考核项数量
 * @property-read ?Collection<Media> $media 附件
 * @property-read ?Collection<WorkItem> $work_items 任务记录
 * @property-read ?Collection<TaskItem> $items 考核项数量
 */
class Task extends Model implements HasMedia
{
    use HasFactory, PrimaryKeyUuidTrait, InteractsWithMedia;

    /**
     * 附件Key 同考核项附件Key
     */
    const MEDIA_FILE = "file";

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
    const TYPE_VEHICLE = 3;

    /**
     * 未分配
     */
    const STATUS_UNDER = 0;

    /**
     * 已排班
     */
    const STATUS_TYPESET = 1;

    /**
     * 进行中
     */
    const STATUS_PROCESSING = 2;

    /**
     * 已完成
     */
    const STATUS_COMPLETED = 3;

    protected $fillable = [
        'id',
        'name',
        'user_id',
        'examine_id',
        'task_cron_id',
        'extra',
        'remark',
        'type',
        'plant',
        'line',
        'engine',
        'status',
        'eb_number',
        'assembly_id',
        'task_status',
        'number',
        'period',
        'start_at',
        'end_at',
        'valid_at',
        'original_examine',
    ];

    public $casts = [
        'defect_level' => 'integer',
        'defect_category' => 'integer',
        'extra' => 'array',
        'type' => 'integer',
        'plant' => 'integer',
        'line' => 'integer',
        'engine' => 'integer',
        'status' => 'integer',
        'task_status' => 'integer',
        'period' => 'decimal:2',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'valid_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'original_examine' => 'array',
        'items_count' => 'integer'
    ];


    public $append = ['items_count','thumbnails'];

    public $hidden = ['items','media'];

    /**
     * 总成号
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<Assembly>|Assembly
     */
    public function assembly()
    {
        return $this->belongsTo(Assembly::class);
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

    /**
     * 已分配员工
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<User>|User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 任务配置
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<TaskCron>|TaskCron
     */
    public function task_cron()
    {
        return $this->belongsTo(TaskCron::class);
    }

    /**
     * 员工工作项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return HasMany|Collection<WorkItem>
     */
    public function work_items()
    {
        return $this->hasMany(WorkItem::class);
    }

    /**
     * 考核项数量
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<TaskItem>|HasMany
     */
    public function items()
    {
        return $this->hasMany(TaskItem::class)->orderBy('sort_order', 'DESC');
    }

    public function getItemsCountAttribute()
    {
        return $this->items->count();
    }

    
    public function getThumbnailsAttribute()
    {
        if (!$medias = $this->getMedia(self::MEDIA_FILE))
            return [];
        return $medias->map(function ($item) {
            return [
                'name' => $item->file_name,
                'url' => url($item->getUrl()),
                'uuid' => $item->uuid
            ];
        });
    }
}
