<?php

namespace App\Models;

use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
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
        'type',
        'plant',
        'line',
        'engine',
        'status',
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


    public $append = ['items_count'];

    public $hidden = ['items'];

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
     * 已分配员工
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<User>|User|Model
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 任务配置
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<TaskCron>|TaskCron|Model
     */
    public function task_cron()
    {
        return $this->belongsTo(TaskCron::class);
    }

    public function work_items()
    {
        return $this->hasMany(WorkItem::class);
    }

    public function items()
    {
        return $this->hasMany(TaskItem::class)->orderBy('sort_order','DESC');
    }

    public function getItemsCountAttribute()
    {
        return $this->items->count();
    }
}
