<?php

namespace App\Models;

use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * 角色模型
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * 
 * @property string $id 角色主键
 * @property string $name 角色名称
 * @property ?array $permissions 权限说明
 * @property boolean $is_valid 是否启用
 * @property-read int $user_count 员工数
 * @property-read ?Collection<User> $users 用户列表
 */
class Role extends Model
{
    use HasFactory, PrimaryKeyUuidTrait;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
        'permissions',
        'is_valid',
    ];

    public $casts = [
        'permissions' => 'array',
        'is_valid' => 'boolean',
        'user_count' => 'integer'
    ];

    public $appends = ['user_count'];

    public $hidden = ['users'];

    /**
     * 获取角色用户
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<User>|BelongsToMany|Model
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'users_roles', 'role_id', 'user_id');
    }

    public function getUserCountAttribute()
    {
        return $this->users->count();
    }
}
