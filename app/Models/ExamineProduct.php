<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 产品考核-考核模板
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property string $id 主键 
 * @property string $author_id 发布者ID
 * @property string $user_id 用户ID
 * @property string $commit_product_id 提交单ID
 * @property string $version 版本号
 * @property string $name 模板名称
 * @property string $description 描述
 * @property int $engine 机型
 * @property float $period 标准工时
 * @property bool $is_valid 是否有效 
 * @property int $status 状态
 * @property int $type 考核类型
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property-read User $author 发布者
 * @property-read User $user 用户
 * @property-read CommitProduct $commit 提交单
 * @property-read Collection $items 考核项
 */
class ExamineProduct extends Model
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
     * 拆检考核
     */
    const TYPE_OVERHAUL = 2;

    /**
     * 装配考核
     */
    const TYPE_ASSEMBLING = 3;

    /**
     * 动态考核
     */
    const TYPE_DYNAMIC = 1;

    protected $fillable = [
        'id',
        'author_id',
        'user_id',
        'commit_product_id',
        'version',
        'name',
        'description',
        'engine',
        'period',
        'is_valid',
        'status',
        'type',
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
     * @return BelongsTo<User>
     */
    public function commit()
    {
        return $this->belongsTo(CommitProduct::class);
    }

    /**
     * 用户
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return BelongsTo<User>
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * 提交单
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return BelongsTo<CommitProduct>
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return HasMany<ExamineProductItem>
     */
    public function items()
    {
        return $this->hasMany(ExamineProductItem::class);
    }
}
