<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * 账号模型
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * 
 * @property string $account 账号
 * @property string $user_id 用户主体ID
 * @property string $type 类型
 * @property ?array $extra 附加参数
 * @property Carbon $verified_at 验证时间
 * @property ?Carbon $created_at 创建时间
 * @property ?Carbon $updated_at 更新时间
 * @property-read ?User $user 用户主体
 */
class Account extends Model
{
    use HasFactory;
    protected $autoIncrement = false;

    public $primaryKey = 'account';

    public $keyType = 'string';

    /**
     * 账号
     */
    const ACCOUNT = 'account';

    /**
     * 电子邮箱
     */
    const EMAIL = 'email';

    /**
     * 手机号码
     */
    const MOBILE = 'mobile';

    /**
     * 工号
     */
    const NUMBER = "number";
    protected $fillable = [
        'account',
        'user_id',
        'type',
        'extra',
        'verified_at'
    ];

    protected $casts = [
        'extra' => 'array',
        'verified_at' => 'datetime'
    ];

    /**
     * 用户主体
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return null|BelongsTo|User|Model
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 获取账号类型选项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return array
     */
    public static function getTypeOptions(): array
    {
        return [
            self::ACCOUNT,
            self::EMAIL,
            self::MOBILE,
            self::NUMBER
        ];
    }
}
