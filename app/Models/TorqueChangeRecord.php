<?php

namespace App\Models;

use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

/**
 * 扭矩变更记录
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property string $id 主键
 * @property string $approver_id 审核员ID
 * @property string $user_id 用户ID
 * @property string $torque_id 扭矩ID
 * @property ?array $extra 变更项
 * @property boolean $is_io 是否IO
 * @property int $status 审核状态
 * @property ?Carbon $created_at 创建时间
 * @property ?Carbon $updated_at 最后更新时间
 * @property-read ?User $approver 审核员
 * @property-read ?User $user 提交人
 * @property-read ?Torque $torque 扭矩数据库
 */
class TorqueChangeRecord extends Model
{
    use HasFactory,PrimaryKeyUuidTrait;

    /**
     * 待审核
     */
    const STATUS_PENDING = 1;

    /**
     * 审核通过
     */
    const STATUS_SUCCESS = 2;

    /**
     * 审核失败
     */
    const STATUS_REJECT = 3;

    protected $fillable = [
        'id',
        'user_id',
        'approver_id',
        'torque_id',
        'extra',
        'is_io',
        'status',
    ];

    public $casts = [
        'status' => 'integer',
        'is_io' => 'boolean',
        'extra' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * 提交人
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<User>|User|Model
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 审核员
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<User>|User|Model
     */
    public function approver()
    {
        return $this->belongsTo(User::class,'approver_id');
    }

    /**
     * 扭矩数据库
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<Torque>|Torque|Model
     */
    public function torque()
    {
        return $this->belongsTo(Torque::class);
    }
}
