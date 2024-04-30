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
 * 整车服务-动态考核模板
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property string $id 主键 
 * @property string $author_id 发布者ID
 * @property string $user_id 用户ID
 * @property string $commit_vehicle_id 提交单ID
 * @property string $version 版本号
 * @property string $name 模板名称
 * @property string $description 描述
 * @property int $engine 机型
 * @property float $period 标准工时
 * @property bool $is_valid 是否有效 
 * @property int $status 状态
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property-read User $author 发布者
 * @property-read User $user 用户
 * @property-read CommitVehicle $commit 提交单
 * @property-read Collection $items 考核项
 */
class ExamineVehicle extends Model
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

    protected $fillable = [
        'id',
        'author_id',
        'user_id',
        'commit_vehicle_id',
        'version',
        'name',
        'description',
        'engine',
        'period',
        'is_valid',
        'status',
    ];

    public $casts = [
        'engine' => 'integer',
        'period' => 'decimal:2',
        'is_valid' => 'boolean',
        'status' => 'integer',
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
        return $this->belongsTo(CommitVehicle::class);
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
     * @return BelongsTo<CommitVehicle>
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return HasMany<ExamineVehicleItem>
     */
    public function items()
    {
        return $this->hasMany(ExamineVehicleItem::class);
    }
}
