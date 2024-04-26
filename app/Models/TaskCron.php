<?php

namespace App\Models;

use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
