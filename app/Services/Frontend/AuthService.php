<?php
namespace App\Services\Frontend;

use App\Models\Account;
use App\Models\User;
use App\Services\Service;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * 认证服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class AuthService extends Service
{
    public ?string $className = Account::class;

    /**
     * 登录用户
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string  $username
     * @param  string  $password
     * @param  boolean $isRemember
     * @return bool
     * 
     * @throws ValidationException
     */
    public function login(string $username, string $password, bool $isRemember = false): array
    {
        $account = Account::find($username);
        if (!$account) {
            throw ValidationException::withMessages(['username.incorrect' => __('auth.login.messages.username.exists')]);
        }
        if (
            !auth('users')->attempt([
                'password' => $password,
                'id' => $account->user_id,
                'is_valid' => true,
                'is_lock' => false
            ], $isRemember)
        ) {
            $logService = new UserLogService;
            $logName = '登录系统';
            $systemConfigService = new SystemConfigService;
            $fail_times_for_lock = $systemConfigService->getValueByCode('fail_times_for_lock');
            if ($account->user->failed_count >= $fail_times_for_lock) {
                $account->user()->update(['is_lock' => true]);
                $logName = '登录系统,失败多次已停用';
            } else {
                $account->user()->increment('failed_count');
            }
            $logService->addLog($account->user, $logName, false);
            throw ValidationException::withMessages(['username.incorrect' => __('auth.login.messages.username.exists')]);
        }
        $user = $account->user;
        $token = $user->createToken('pad');
        $result = [
            'token' => $token->plainTextToken,
            'first' => empty($user->profile),
            'user' => [
                'name' => optional($user->profile)->name,
                'avatar' => optional($user->profile)->avatar,
            ]
        ];
        return $result;
    }


    /**
     * 更新用户登录时间
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $id
     * @return void
     */
    public function hasLogin(string $id): void
    {
        $this->setModel(User::class);
        $this->update($id, [
            'failed_count' => 0,
            'lasted_login' => Carbon::now(),
            'lasted_ip_address' => request()->getClientIp()
        ]);
    }

    public function finish(User $user, array $data)
    {
        $email = (string) $data['email'];
        $mobile = (string) $data['mobile'];
        $number = (string) $data['number'];
        $profile = [
            'name' => (string) $data['name'],
            'pinyin' => array_key_exists('pinyin', $data) ? (string) $data['pinyin'] : Str::ucfirst(pinyin((string) $data['name']))
        ];
        $password = (string) $data['password'];
        (new ProfileService)->updateOrCreate($user, $profile);
        $accountService = new AccountService;
        $accountService->createAccount($user, $email, Account::EMAIL);
        $accountService->createAccount($user, $mobile, Account::MOBILE);
        $accountService->createAccount($user, $number, Account::NUMBER);
        $user->update([
            'password' => Hash::make($password)
        ]);
    }
}