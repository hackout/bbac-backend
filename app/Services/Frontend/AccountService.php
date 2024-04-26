<?php
namespace App\Services\Frontend;

use App\Models\User;
use App\Models\Account;
use App\Services\Service;
use Illuminate\Database\Eloquent\Model;

/**
 * 账号服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class AccountService extends Service
{
    public ?string $className = Account::class;

    /**
     * 获取账号参数选项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return array
     */
    public function getTypeOptions(): array
    {
        return [
            Account::EMAIL,
            Account::MOBILE,
            Account::NUMBER
        ];
    }

    /**
     * 添加账号
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User|Model $user 用户
     * @param  string $account 账号
     * @param  string $type 类型
     * @return bool
     */
    public function createAccount(User|Model $user, string $account, string $type)
    {
        return parent::create([
            'account' => $account,
            'user_id' => $user->id,
            'type' => $type
        ]);
    }

    /**
     * 更新或添加
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User|Model $user
     * @param  string     $type
     * @param  string     $account
     * @return void
     */
    public function updateOrCreate(User|Model $user, string $type,string $account)
    {
        if(!parent::find(['type'=>$type,'user_id'=>$user->id]))
        {
            $this->createAccount($user,$account,$type);
        }else{
            $this->updateAccount($user,$type,$account);
        }
    }

    /**
     * 更新账号内容
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  mixed|User   $user 用户模型
     * @param  string $type 类型
     * @param  string $value 账号内容
     * @return void
     */
    public function updateAccount($user, string $type, string $value)
    {
        $user->accounts()->where('type', $type)->update(['account' => $value]);
    }

}