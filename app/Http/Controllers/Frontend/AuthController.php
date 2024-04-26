<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\AuthService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthController extends Controller
{
    /**
     * PAD登录
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  AuthService  $authService
     * @return JsonResponse
     */
    public function login(Request $request, AuthService $authService): JsonResponse
    {
        $rules = [
            'username' => 'required|exists:accounts,account',
            'password' => 'required',
            'remember' => 'sometimes|boolean|nullable',
            'code' => 'sometimes|nullable|captcha'
        ];
        $messages = [
            "username.required" => __("auth.login.messages.username.required"),
            "username.exists" => __("auth.login.messages.username.exists"),
            "password.required" => __("auth.login.messages.password.required"),
            "remember.boolean" => __("auth.login.messages.remember.boolean"),
            "code.captcha" => __("auth.login.messages.code.captcha")
        ];
        $data = $request->validate($rules, $messages);
        $result = $authService->login((string) $data['username'], (string) $data['password'], (bool) $data['remember']);
        if ($result === true) {
            $authService->hasLogin(auth('users')->user()->id);
        }
        return $this->success($result);
    }

    /**
     * 完善个人资料
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  AuthService  $service
     * @return JsonResponse
     */
    public function first(Request $request, AuthService $service): JsonResponse
    {
        $user_id = $request->user()->id;
        $rules = [
            'number' => 'required|unique:accounts,account,'.$user_id.',user_id',
            'mobile' => 'required|unique:accounts,account,'.$user_id.',user_id',
            'email' => 'required|unique:accounts,account,'.$user_id.',user_id',
            'name' => 'required',
            'pinyin' => 'sometimes|nullable',
            'password' => 'required|between:6,32|confirmed',
            'password_confirmation' => 'required'
        ];
        $messages = [
            'number.required' => '员工工号不能为空',
            'mobile.required' => '手机号码不能为空',
            'email.required' => '邮箱地址不能为空',
            'name.required' => '员工姓名不能为空',
            'password.required' => '登录密码不能为空',
            'password_confirmation.required' => '重复登录密码不能为空',
            'number.unique' => '员工工号已经存在',
            'mobile.unique' => '手机号码已经存在',
            'email.unique' => '邮箱地址已经存在',
            'password.between' => '登录密码仅支持6至32位字符串',
            'password.confirmed' => '两次输入的密码不相符',
        ];
        $data = $request->validate($rules, $messages);
        if ($request->user()->profile) {
            return $this->success();
        }
        $service->finish($request->user(), $data);
        return $this->success();
    }
}
