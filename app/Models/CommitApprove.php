<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 考核版本审核
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property string $id 主键
 * @property string $user_id 提交人ID
 * @property string $messenger_id 推送人ID
 * @property string $approver_id 审核员ID
 * @property ?string $commit_id 版本模型ID
 * @property ?string $commit_type 版本模型路径
 * @property ?string $content 变更内容
 * @property ?string $influence 影响范围
 * @property ?string $concerns 关注事项
 * @property ?array $extra 变更参数
 * @property ?string $description 备注回复
 * @property integer $status 审核状态
 * @property-read ?Carbon $created_at 创建时间
 * @property-read ?Carbon $updated_at 更新时间
 * @property-read null|CommitVehicle|CommitInline|CommitProduct $commit 版本
 * @property-read ?User $user 提交人
 * @property-read ?User $messenger 推送人
 * @property-read ?User $approver 审核员
 * @property-read ?Notice $notice 通知消息
 */
class CommitApprove extends Model
{
    use HasFactory, PrimaryKeyUuidTrait;

    /**
     * 待审核
     */
    const STATUS_DRAFT = 0;

    /**
     * 审核中
     */
    const STATUS_PENDING = 1;

    /**
     * 审核通过
     */
    const STATUS_SUCCESS = 2;

    /**
     * 审核拒绝
     */
    const STATUS_REJECT = 3;

    protected $fillable = [
        'id',
        'commit_id',
        'commit_type',
        'user_id',
        'messenger_id',
        'approver_id',
        'content',
        'influence',
        'concerns',
        'extra',
        'description',
        'status',
    ];

    public $casts = [
        'status' => 'integer',
        'extra' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * 考核模板(历史)
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|MorphTo|CommitVehicle|CommitInline|CommitProduct
     */
    public function commit()
    {
        return $this->morphTo();
    }

    /**
     * 提交用户
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo|User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 推送人
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo|User
     */
    public function messenger()
    {
        return $this->belongsTo(User::class, 'messenger_id');
    }

    /**
     * 审核员
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo|User
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * 相关消息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|MorphOne|Notice
     */
    public function notice()
    {
        return $this->morphOne(Notice::class, 'model');
    }
}
