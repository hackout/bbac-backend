<?php

namespace App\Models;

use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;


/**
 * 部门模型
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * 
 * @property string $id 部门主键
 * @property ?string $parent_id 上级ID
 * @property ?string $leader_id Leader ID
 * @property string $name 名称
 * @property ?string $contact 负责人
 * @property ?string $mobile 联系手机
 * @property ?string $email 联系邮箱
 * @property int $role 权限组
 * @property-read ?self $parent 上级列表
 * @property-read int $user_count 成员数
 * @property-read bool $has_top 是否调用全局
 * @property-read ?User $leader 负责人数据
 * @property-read ?Collection<self> $children 下级列表
 * @property-read ?Collection<User> $users 用户列表
 */
class Department extends Model
{
    use HasFactory, PrimaryKeyUuidTrait;

    public $timestamps = false;

    /**
     * 零权组
     */
    const ROLE_UNDER = 0;

    /**
     * 管理组
     */
    const ROLE_MANAGER = 1;

    /**
     * 在线考核
     */
    const ROLE_INLINE = 2;

    /**
     * 产品考核
     */
    const ROLE_PRODUCT = 3;

    /**
     * 整车服务
     */
    const ROLE_VEHICLE = 4;

    protected $fillable = [
        'id',
        'parent_id',
        'leader_id',
        'name',
        'contact',
        'mobile',
        'email',
        'role'
    ];

    public $casts = [
        'user_count' => 'integer',
        'role' => 'integer'
    ];

    public $appends = [
        'user_count',
        'has_top'
    ];

    public $hidden = [
        'users'
    ];

    /**
     * 上级部门
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|self|BelongsTo|Model
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * 下级部门
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<self>|HasMany|Model
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * 下属员工-非多级
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<User>|HasMany|Model
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * 负责人ID
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo<User>|User|Model
     */
    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function getUserCountAttribute()
    {
        return $this->users->count();
    }

    public function getHasTopAttribute()
    {
        return $this->role == self::ROLE_MANAGER;
    }
}
