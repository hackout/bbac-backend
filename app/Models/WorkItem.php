<?php

namespace App\Models;

use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkItem extends Model
{
    use HasFactory, PrimaryKeyUuidTrait;

    /**
     * 正常考核工时
     */
    const TYPE_NORMAL = 1;

    /**
     * 动态考核
     */
    const TYPE_DYNAMIC = 2;

    /**
     * 其他安排
     */
    const TYPE_OTHER = 3;

    /**
     * Campaign
     */
    const TYPE_CAMPAIGN = 4;

    /**
     * 培训
     */
    const TYPE_TRAINING = 5;

    /**
     * 加班
     */
    const TYPE_PTIME = 6;

    /**
     * 休假
     */
    const TYPE_HOLIDAY = 7;

    /**
     * 未开始
     */
    const STATUS_PENDING = 0;

    /**
     * 进行中
     */
    const STATUS_PROCESSING = 1;

    /**
     * 提前结束
     */
    const STATUS_ADVANCE = 2;

    /**
     * 已超时
     */
    const STATUS_TIMEOUT = 3;

    /**
     * 超时结束
     */
    const STATUS_ENDED = 4;

    protected $fillable = [
        'id',
        'user_id',
        'task_id',
        'work_id',
        'content',
        'type',
        'status',
        'period',
        'extra',
        'work_date',
    ];

    public $casts = [
        'type' => 'integer',
        'status' => 'integer',
        'period' => 'decimal:2',
        'extra' => 'array',
        'work_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function work()
    {
        return $this->belongsTo(Work::class);
    }
}
