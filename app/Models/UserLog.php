<?php

namespace App\Models;

use App\Packages\LogWrite\LogWrite;
use App\Traits\AutoCreatedTimeTrait;
use App\Traits\PrimaryKeyUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

/**
 * 用户操作日志
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * 
 * @property string $id 记录主键
 * @property string $user_id 用户ID
 * @property string $name 操作说明
 * @property string $route 访问地址
 * @property ?array $extra 附加参数
 * @property string $ip_address 访问IP
 * @property string $method 请求方式
 * @property string $os 客户端
 * @property Carbon $created_at 发生时间
 * @property-read ?User $user 用户主体
 */
class UserLog extends Model
{
    use HasFactory, PrimaryKeyUuidTrait, AutoCreatedTimeTrait;

    /**
     * 后台
     */
    const OS_BACKEND = 'backend';

    /**
     * PAD端
     */
    const OS_PAD = 'pad';

    protected $fillable = [
        'user_id',
        'name',
        'route',
        'status',
        'extra',
        'method',
        'ip_address',
        'os',
    ];

    public $casts = [
        'status' => 'boolean',
        'extra' => 'array',
        'created_at' => 'datetime'
    ];

    public $appends = ['description'];

    /**
     * 用户
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return mixed|User|Model|BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
