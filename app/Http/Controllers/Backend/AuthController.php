<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\AuthService;
use App\Services\Backend\DepartmentService;
use App\Services\Backend\DictService;
use App\Services\Backend\SystemConfigService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;
use Intervention\Image\ImageManager;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Redirect;

class AuthController extends Controller
{
    /**
     * 显示验证码
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return ImageManager
     */
    public function captcha(): ImageManager
    {
        return captcha();
    }

    public function resetPassword(Request $request, AuthService $service): RedirectResponse|InertiaResponse
    {
        $rules = [
            'username' => 'required|exists:accounts,account',
            'reset_token' => 'required|exists:password_reset_tokens,token',
            'password' => 'required|between:6,32|confirmed',
            'password_confirmation' => 'required',
            'code' => 'required|between:6,6'
        ];
        $messages = [
            'username.required' => '登录账号不能为空',
            'username.exists' => '两次输入的密码不相符',
            'password.between' => '登录密码仅支持6至32位字符串',
            'password.confirmed' => '两次输入的密码不相符',
            'code.required' => '验证码不能为空',
            'code.between' => '验证码错误',
            'reset_token.required' => '两次输入的密码不相符',
            'reset_token.exists' => '两次输入的密码不相符'
        ];
        $data = $this->validate($request, $rules, $messages);
        $service->resetPassword($data);
        return to_route('login')->with('success', '修改密码成功');
    }

    public function checkAccount(Request $request, AuthService $service): RedirectResponse
    {
        $rules = [
            'username' => 'required|exists:accounts,account',
            'code' => 'required|captcha'
        ];
        $messages = [
            'username.required' => '邮箱或手机号码不能为空',
            'username.exists' => '验证码不正确',
            'code.required' => '验证码不能为空',
            'code.captcha' => '验证码不正确'
        ];
        $data = $this->validate($request, $rules, $messages);
        return Redirect::back()->with('extra', [
            'step' => 1,
            'username' => $data['username'],
            'token' => $service->checkAccount($data)
        ]);
    }

    public function sendCode(Request $request, AuthService $service): RedirectResponse
    {
        $rules = [
            'username' => 'required|exists:accounts,account'
        ];
        $messages = [
            'username.required' => '邮箱或手机号码不能为空',
            'username.exists' => '验证码不正确'
        ];
        $data = $this->validate($request, $rules, $messages);
        $token = $service->sendCode((string) $data['username']);
        return Redirect::back()->with('extra', [
            'step' => 1,
            'username' => $data['username'],
            'token' => $token
        ]);
    }

    /**
     * 忘记密码页面
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  SystemConfigService $systemConfigService
     * @return InertiaResponse
     */
    public function forget(Request $request, SystemConfigService $systemConfigService): InertiaResponse
    {
        $captcha = $systemConfigService->getValueByCode('captcha_switch');
        return Inertia::render('Login/Forget', [
            'step' => $request->get('token') ? 1 : 0,
            'token' => $request->get('token', null),
            'captcha' => $captcha
        ]);
    }

    /**
     * 登录页面
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  SystemConfigService $systemConfigService
     * @return InertiaResponse
     */
    public function login(SystemConfigService $systemConfigService): InertiaResponse
    {
        $captcha = $systemConfigService->getValueByCode('captcha_switch');
        return Inertia::render('Login/Login', [
            'captcha' => $captcha
        ]);
    }

    /**
     * 首次登录-完善资料页面
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  DictService       $dictService
     * @param  DepartmentService $departmentService
     * @return InertiaResponse
     */
    public function first(DictService $dictService, DepartmentService $departmentService): InertiaResponse
    {
        return Inertia::render('Login/First', [
            'options' => [
                'career_level' => $dictService->getOptionByCode('career_level'),
                'skill_level' => $dictService->getOptionByCode('skill_level'),
                'nation' => $dictService->getOptionByCode('nation'),
                'gender' => $dictService->getOptionByCode('gender'),
                'engine_type' => $dictService->getOptionByCode('engine_type')
            ],
            'departments' => $departmentService->getFullOptions()
        ]);
    }

    /**
     * 完善用户资料
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request          $request
     * @param  AuthService      $service
     * @return RedirectResponse
     */
    public function finish(Request $request, AuthService $service): RedirectResponse
    {
        $rules = [
            'number' => 'required|unique:accounts,account',
            'mobile' => 'required|unique:accounts,account',
            'email' => 'required|unique:accounts,account',
            'name' => 'required',
            'pinyin' => 'required',
            'gender' => 'required|integer',
            'birth' => 'required|date',
            'nation' => 'required|integer',
            'birthplace' => 'required',
            'address' => 'required',
            'id_card' => 'required|unique:profiles,id_card',
            'educational' => 'required',
            'science' => 'required',
            'emergency_contact' => 'required',
            'emergency_telephone' => 'required',
            'skill_level' => 'required|integer',
            'career_level' => 'required|integer',
            'attend_date' => 'required|date',
            'entry_date' => 'required|date',
            'vocational_skills' => 'sometimes|nullable|array',
            'password' => 'required|between:6,32|confirmed',
            'password_confirmation' => 'required'
        ];
        $messages = [
            'number.required' => '员工工号不能为空',
            'mobile.required' => '手机号码不能为空',
            'email.required' => '邮箱地址不能为空',
            'name.required' => '员工姓名不能为空',
            'pinyin.required' => '姓名拼音不能为空',
            'gender.required' => '性别称谓不能为空',
            'birth.required' => '出生日期不能为空',
            'nation.required' => '民族不能为空',
            'birthplace.required' => '户籍籍贯不能为空',
            'address.required' => '家庭地址不能为空',
            'id_card.required' => '证件号码不能为空',
            'educational.required' => '学历不能为空',
            'science.required' => '学位不能为空',
            'emergency_contact.required' => '紧急联系人不能为空',
            'emergency_telephone.required' => '紧急联系电话不能为空',
            'skill_level.required' => '综合技能等级不能为空',
            'career_level.required' => '职业等级不能为空',
            'attend_date.required' => '工作时间不能为空',
            'entry_date.required' => '入职时间不能为空',
            'password.required' => '登录密码不能为空',
            'password_confirmation.required' => '重复登录密码不能为空',
            'number.unique' => '员工工号已经存在',
            'mobile.unique' => '手机号码已经存在',
            'email.unique' => '邮箱地址已经存在',
            'gender.integer' => '性别称谓不正确',
            'birth.date' => '出生日期不正确',
            'nation.integer' => '民族不正确',
            'id_card.unique' => '证件号码已存在',
            'skill_level.integer' => '综合技能等级不正确',
            'career_level.integer' => '职业等级不正确',
            'attend_date.date' => '工作时间不正确',
            'entry_date.date' => '入职时间不正确',
            'vocational_skills.array' => '职业技能参数错误',
            'password.between' => '登录密码仅支持6至32位字符串',
            'password.confirmed' => '两次输入的密码不相符',
        ];
        $data = $request->validate($rules, $messages);
        if ($request->user()->profile) {
            return to_route('dashboard')->with('success', '欢迎回来【' . $data['name'] . '】');
        }
        $service->finish($request->user(), $data);
        return to_route('dashboard')->with('success', '欢迎回来【' . $data['name'] . '】');
    }

    /**
     * 会员登录
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request                          $request
     * @param  AuthService                      $service
     * @return RedirectResponse|InertiaResponse
     */
    public function auth(Request $request, AuthService $service): RedirectResponse|InertiaResponse
    {
        $data = $request->validate([
            'username' => 'required|exists:accounts,account',
            'password' => 'required',
            'remember' => 'sometimes|boolean|nullable',
            'code' => 'sometimes|nullable|captcha'
        ], [
            'username.required' => '用户名不能为空',
            'username.exists' => '登录名或密码错误',
            'password.required' => '登录名或密码错误',
            'remember.boolean' => '登录名或密码错误',
            'code.captcha' => '验证码错误'
        ]);
        $result = $service->login((string) $data['username'], (string) $data['password'], (bool) $data['remember']);
        if ($result === true) {
            $request->session()->regenerate();
            $service->hasLogin(auth('users')->user()->id);
            if (auth('users')->user()->profile) {
                return to_route('dashboard')->with('success', '欢迎回来【' . $data['username'] . '】');
            }
            return to_route('login.first')->with('success', '欢迎回来【' . $data['username'] . '】,首次登录需要您完善个人信息');
        }
        return $result;
    }

    /**
     * 退出登录
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return RedirectResponse
     */
    public function logout(): RedirectResponse
    {
        auth('users')->logout();
        return redirect()->route('login')->with('success', '已退出登录，请重新登录账号');
    }
}
