<?php

namespace App\Models;

use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 参与培训人员模块
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property string $id 主键
 * @property ?string $user_id 用户ID
 * @property string $training_id 培训ID
 * @property string $name 员工姓名 //以保证未录入后台的员工的数据保存
 * @property string $number 员工工号 //以保证未录入后台的员工的数据保存
 * @property int $status 培训状态
 * @property ?Carbon $trained_at 培训时间
 * @property ?Carbon $created_at 添加时间
 * @property ?Carbon $updated_at 更新时间
 * @property-read ?User $user 员工信息
 * @property-read ?Training $training 培训信息
 */
class TrainingUser extends Model
{
    use HasFactory, PrimaryKeyUuidTrait;

    /**
     * 未参与|0%
     */
    const STATUS_NONPARTICIPATION = 1;
    /**
     * 参与并通过考试|25%
     */
    const STATUS_PASS = 2;

    /**
     * 参与未通过考试|50%
     */
    const STATUS_FAILED = 3;

    /**
     * 不涉及
     */
    const STATUS_NOT_INVOLVED = 4;
    
    /**
     * 75%
     */
    const STATUS_THREE_QUARTER = 5;

    /**
     * 100%
     */
    const STATUS_HUNDRED  = 6;

    protected $fillable = [
        'id',
        'user_id',
        'training_id',
        'name',
        'number',
        'status',
        'trained_at',
    ];

    protected $casts = [
        'status' => 'integer',
        'trained_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * 培训员工信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<User>|User|Model
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 培训信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<Training>|Training|Model
     */
    public function training()
    {
        return $this->belongsTo(Training::class);
    }
}
