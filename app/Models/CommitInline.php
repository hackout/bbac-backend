<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 在线考核-考核模板
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property string $id 主键
 * @property ?string $author_id 发布者ID
 * @property ?string $user_id 用户ID
 * @property ?string $examine_inline_id 考核模板ID
 * @property ?string $parent_id 上一个版本ID
 * @property string $version 版本号
 * @property string $name 模板名称
 * @property string $description 备注信息
 * @property int $engine 机型
 * @property float $period 标准工时
 * @property bool $is_valid 是否有效
 * @property int $status 状态
 * @property int $type 考核类型
 * @property-read ?Carbon $created_at 创建时间
 * @property-read ?Carbon $updated_at 更新时间
 * @property-read ?User $author 发布者
 * @property-read ?User $user 用户
 * @property-read ?self $parent 上一个版本
 * @property-read ?self $child 下一个版本
 * @property-read ?ExamineInline $examine 考核模板
 * @property-read ?Collection<CommitInlineItem> $items 考核项
 * @property-read ?Collection<CommitApprove> $approves 审核记录
 */
class CommitInline extends Model
{
    use HasFactory, PrimaryKeyUuidTrait;

    /**
     * 待提交
     */
    const STATUS_DRAFT = 0;

    /**
     * 待审核
     */
    const STATUS_PENDING = 1;

    /**
     * 审核通过
     */
    const STATUS_SUCCESS = 2;

    /**
     * 拒绝
     */
    const STATUS_REJECT = 3;

    /**
     * 常规考核
     */
    const TYPE_STANDARD = 1;

    /**
     * 涂胶考核
     */
    const TYPE_GLUING = 2;

    /**
     * 动态考核
     */
    const TYPE_DYNAMIC = 3;

    protected $fillable = [
        'id',
        'author_id',
        'user_id',
        'examine_inline_id',
        'parent_id',
        'version',
        'name',
        'description',
        'engine',
        'period',
        'is_valid',
        'status',
        'type'
    ];

    public $casts = [
        'engine' => 'integer',
        'period' => 'decimal:2',
        'is_valid' => 'boolean',
        'status' => 'integer',
        'type' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * 发布者
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<User>|User|Model
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * 用户
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<User>|User|Model
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    /**
     * 考核模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<ExamineInline>|ExamineInline|Model
     */
    public function examine()
    {
        return $this->belongsTo(ExamineInline::class);
    }


    /**
     * 上一个版本
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<self>|self|Model
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }


    /**
     * 下一个版本
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|HasOne<self>|self|Model
     */
    public function child()
    {
        return $this->hasOne(self::class, 'parent_id');
    }

    /**
     * 考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<CommitInlineItem>|HasMany
     */
    public function items()
    {
        return $this->hasMany(CommitInlineItem::class);
    }

    /**
     * 审核记录
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<CommitApprove>|MorphMany
     */
    public function approves()
    {
        return $this->morphMany(CommitApprove::class, 'commit');
    }
}
