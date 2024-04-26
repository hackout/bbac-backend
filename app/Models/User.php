<?php

namespace App\Models;

use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 用户模型
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * 
 * @property string $id 用户主键
 * @property string $department_id 部门ID
 * @property string $lasted_login 最后登录时间
 * @property string $lasted_ip_address 最后登录IP
 * @property string $password 登录密码
 * @property string $is_valid 是否启用
 * @property string $is_super 是否超管
 * @property string $has_error 是否存在登录错误
 * @property string $is_lock 是否锁定
 * @property string $failed_count 登录错误次数
 * @property-read ?string $username 登录账号
 * @property-read ?string $mobile 手机号码
 * @property-read ?string $number 员工工号
 * @property-read ?string $email 邮箱地址
 * @property-read ?string $safe_username 登录账号(掩码)
 * @property-read ?string $safe_mobile 手机号码(掩码)
 * @property-read ?string $safe_number 员工工号(掩码)
 * @property-read ?string $safe_email 邮箱地址(掩码)
 * @property-read ?Profile $profile 个人资料
 * @property-read ?Collection<Account> $accounts 账号列表
 * @property-read ?Collection<TrainingUser> $trainings 参与的培训
 * @property-read ?Department $department 所在部门
 * @property-read ?Collection<Role> $roles 用户角色
 * @property-read ?Collection<UserLog> $logs 访问日志
 * @property-read ?Collection<Department> $departments 负责部门
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, PrimaryKeyUuidTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'department_id',
        'lasted_login',
        'lasted_ip_address',
        'password',
        'is_valid',
        'is_super',
        'has_error',
        'is_lock',
        'failed_count'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'accounts'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'lasted_login' => 'datetime',
        'password' => 'hashed',
        'is_valid' => 'boolean',
        'is_super' => 'boolean',
        'is_lock' => 'boolean',
        'has_error' => 'boolean',
        'failed_count' => 'integer'
    ];

    public $appends = [
        'username',
        'mobile',
        'number',
        'email',
        'safe_username',
        'safe_mobile',
        'safe_number',
        'safe_email'
    ];

    /**
     * 个人资料
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|HasOne|Profile|Model
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * 账号列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<Account>|HasMany|Model
     */
    public function accounts()
    {
        return $this->hasMany(Account::class,'user_id','id');
    }

    /**
     * 所在部门
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo|Department|Model
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * 用户角色
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<Role>|BelongsToMany|Model
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'users_roles', 'user_id', 'role_id');
    }

    /**
     * 访问日志
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<UserLog>|HasMany|Model
     */
    public function logs()
    {
        return $this->hasMany(UserLog::class, 'user_id', 'id');
    }

    /**
     * 负责部门
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<Department>|HasMany|Model
     */
    public function departments()
    {
        return $this->hasMany(Department::class,'leader_id');
    }

    /**
     * 参与的培训
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|Collection<TrainingUser>|HasMany
     */
    public function trainings()
    {
        return $this->hasMany(TrainingUser::class);
    }



    public function getUsernameAttribute()
    {
        return optional($this->accounts->where('type',Account::ACCOUNT)->first())->account ?: null;
    }

    public function getNumberAttribute()
    {
        return optional($this->accounts->where('type',Account::NUMBER)->first())->account ?: null;
    }

    public function getEmailAttribute()
    {
        return optional($this->accounts->where('type',Account::EMAIL)->first())->account ?: null;
    }

    public function getMobileAttribute()
    {
        return optional($this->accounts->where('type',Account::MOBILE)->first())->account ?: null;
    }
    public function getSafeUsernameAttribute()
    {
        $value = optional($this->accounts->where('type',Account::ACCOUNT)->first())->account ?: null;
        return $this->safeValue($value);
    }

    public function getSafeNumberAttribute()
    {
        $value = optional($this->accounts->where('type',Account::NUMBER)->first())->account ?: null;
        return $this->safeValue($value);
    }

    public function getSafeEmailAttribute()
    {
        $value = optional($this->accounts->where('type',Account::EMAIL)->first())->account ?: null;
        return $this->safeValue($value);
    }

    public function getSafeMobileAttribute()
    {
        $value = optional($this->accounts->where('type',Account::MOBILE)->first())->account ?: null;
        return $this->safeValue($value);
    }

    /**
     * 安全掩码
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string|null $string
     * @return ?string
     */
    private function safeValue(?string $string = null)
    {
        if(!$string) return null;
        if(mb_strlen($string) < 6) return mb_substr($string,0,3).'******';
        return mb_substr($string,0,3).'****'.mb_substr($string,-3);
    }

    public function checkPermission(string $str)
    {
        return true;
    }
}
