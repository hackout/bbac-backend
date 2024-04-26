<?php
namespace App\Services\Backend;

use App\Mail\UserForgetPassword;
use App\Models\Account;
use App\Models\User;
use App\Packages\AliApi\AliApi;
use App\Services\Service;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * 认证服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class AuthService extends Service
{
    public ?string $className = Account::class;

    /**
     * 检查账号并发送验证码
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array  $data
     * @return string
     * 
     * @throws ValidationException
     */
    public function checkAccount(array $data): string
    {
        $account = $this->findById((string) $data['username']);
        if (!$account || !in_array($account->type, [Account::EMAIL, Account::MOBILE])) {
            throw ValidationException::withMessages(['username.incorrect' => '发送验证码失败']);
        }
        return $this->sendCode($account);
    }

    /**
     * 发送验证码
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Account|string $account
     * @return string
     * 
     * @throws ValidationException
     */
    public function sendCode(Account|string $account): string
    {
        if (is_string($account)) {
            $account = $this->findById((string) $account);
            if (!$account || !in_array($account->type, [Account::EMAIL, Account::MOBILE])) {
                throw ValidationException::withMessages(['username.incorrect' => '发送验证码失败']);
            }
        }
        if (!$account || !in_array($account->type, [Account::EMAIL, Account::MOBILE])) {
            return false;
        }
        $lastItem = DB::table('password_reset_tokens')->where([
            'account' => $account->account
        ])->orderBy('created_at', 'DESC')->first();
        if ($lastItem) {
            $lastAt = Carbon::parse($lastItem->created_at);
            if ($lastAt->gt(Carbon::now()->subMinute()))
                throw ValidationException::withMessages(['username.incorrect' => '发送验证码失败']);
        }
        $code = rand(0, 999999) + 1000000;
        $code = (string) mb_substr($code, 1);
        $token = Str::uuid();
        $now = Carbon::now();
        $sql = [
            'user_id' => $account->user_id,
            'token' => $token,
            'account' => $account->account,
            'type' => $account->type,
            'created_at' => $now,
            'valid_at' => $now->clone()->addMinutes(15)
        ];
        $item = DB::table('password_reset_tokens')->insert($sql);
        if (!$item) {
            throw ValidationException::withMessages(['username.incorrect' => '发送验证码失败']);
        }
        if ($account->type == Account::EMAIL) {
            Mail::to($account->account)->send(new UserForgetPassword($code, $token));
        }
        if ($account->type == Account::MOBILE) {
            $aliApi = new AliApi;
            $aliApi->sentSms($account->account, $code);
        }
        Cache::put($token, $code, 300);
        return $token;
    }


    /**
     * 找回密码
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @return void
     * 
     * @throws ValidationException
     */
    public function resetPassword(array $data)
    {
        $account = $this->findById((string) $data['username']);
        if (!$account) {
            throw ValidationException::withMessages(['password.incorrect' => '两次输入的密码不相符']);
        }
        $token = (string) $data['reset_token'];
        if (!$lastItem = DB::table("password_reset_tokens")->where('token', $token)->first()) {
            throw ValidationException::withMessages(['password.incorrect' => '两次输入的密码不相符']);
        }
        $validAt = Carbon::parse($lastItem->valid_at);
        if ($validAt->lt(Carbon::now())) {
            throw ValidationException::withMessages(['password.incorrect' => '两次输入的密码不相符']);
        }
        if (Cache::pull($token) != (string) $data['code']) {
            throw ValidationException::withMessages(['code.incorrect' => '验证码错误']);
        }
        $account->user()->update([
            'password' => Hash::make((string) $data['password'])
        ]);
        DB::table("password_reset_tokens")->where('token', $token)->delete();
    }

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
    public function login(string $username, string $password, bool $isRemember = false): bool|RedirectResponse
    {
        $account = Account::find($username);
        if (!$account) {
            return back()->withErrors('登录名或密码错误');
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
            return back()->withErrors('登录名或密码错误');
        }
        return true;
    }

    /**
     * 完善个人信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  User  $user 用户
     * @param  array $data 参数内容
     * @return void
     */
    public function finish(User $user, array $data)
    {
        $email = (string) $data['email'];
        $mobile = (string) $data['mobile'];
        $number = (string) $data['number'];
        $profile = [
            'name' => (string) $data['name'],
            'pinyin' => (string) $data['pinyin'],
            'gender' => (int) $data['gender'],
            'birth' => Carbon::parse($data['birth']),
            'nation' => (string) $data['nation'],
            'birthplace' => (string) $data['birthplace'],
            'address' => (string) $data['address'],
            'id_card' => (string) $data['id_card'],
            'educational' => (string) $data['educational'],
            'science' => (string) $data['science'],
            'emergency_contact' => (string) $data['emergency_contact'],
            'emergency_telephone' => (string) $data['emergency_telephone'],
            'skill_level' => (int) $data['skill_level'],
            'career_level' => (int) $data['career_level'],
            'attend_date' => Carbon::parse($data['attend_date']),
            'entry_date' => Carbon::parse($data['entry_date']),
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


    /**
     * 修改登录密码
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $user_id 用户ID
     * @param  string   $password 新密码
     * @return void
     * 
     */
    public function password(string $user_id, string $password): void
    {
        $this->setModel(User::class);
        parent::setValue($user_id,'password',Hash::make($password));
    }
}