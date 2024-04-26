<?php

namespace App\Models;

use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Examine extends Model
{
    use HasFactory,PrimaryKeyUuidTrait;

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
     * 在线考核
     */
    const TYPE_INLINE = 1;

    /**
     * 产品考核
     */
    const TYPE_PRODUCT = 2;

    /**
     * 整车考核
     */
    const TYPE_VEHICLE = 3;

    /**
     * 常规考核
     */
    const SUB_TYPE_STANDARD = 1;

    /**
     * 涂胶考核
     */
    const SUB_TYPE_GLUING = 2;

    /**
     * 动态考核
     */
    const SUB_TYPE_DYNAMIC = 3;

    /**
     * 拆检考核
     */
    const SUB_TYPE_OVERHAUL = 4;

    /**
     * 装配考核
     */
    const SUB_TYPE_ASSEMBLING = 5;


    protected $fillable = [
        'id',
        'author_id',
        'user_id',
        'commit_id',
        'version',
        'name',
        'description',
        'engine',
        'period',
        'is_valid',
        'status',
        'type',
        'sub_type'
    ];

    public $casts = [
        'engine' => 'integer',
        'period' => 'integer',
        'is_valid' => 'boolean',
        'status' => 'integer',
        'type' => 'integer',
        'sub_type' => 'integer',
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
     * 最后编辑用户
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<User>|User|Model
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 考核项列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<ExamineItem>|HasMany
     */
    public function items()
    {
        return $this->hasMany(ExamineItem::class)->orderBy('sort_order', 'asc');
    }

    /**
     * 历史版本
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<Commit>|HasMany
     */
    public function commits()
    {
        return $this->hasMany(Commit::class);
    }

    /**
     * 上一个版本
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<Commit>|Commit|Model
     */
    public function lasted()
    {
        return $this->belongsTo(Commit::class);
    }
}
