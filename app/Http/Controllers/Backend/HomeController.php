<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\AccountService;
use App\Services\Backend\AuthService;
use App\Services\Backend\DashboardService;
use App\Services\Backend\DepartmentService;
use App\Services\Backend\DictService;
use App\Services\Backend\ProfileService;
use App\Services\Backend\NoticeService;
use App\Services\Backend\UserLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\JsonResponse;

class HomeController extends Controller
{

    /**
     * 控制台首页
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return InertiaResponse
     */
    public function index(Request $request,DashboardService $service): InertiaResponse
    {
        return Inertia::render('Dashboard/Index',[
            'items' => $service->dashboard()
        ]);
    }


    /**
     * 获取个人信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  DictService       $dictService
     * @param  DepartmentService $departmentService
     * @return InertiaResponse
     */
    public function profile(Request $request,ProfileService $service, DictService $dictService, DepartmentService $departmentService)
    {
        $profile = $service->getProfileByUser(auth('users')->user());
        return Inertia::render('Profile/Index', [
            'tab' => 'profile',
            'item' => $profile['item'],
            'profile' => $profile['profile'],
            'options' => [
                'career_level' => $dictService->getOptionByCode('career_level'),
                'skill_level' => $dictService->getOptionByCode('skill_level'),
                'nation' => $dictService->getOptionByCode('nation'),
                'gender' => $dictService->getOptionByCode('gender')
            ]
        ]);
    }


    /**
     * 获取未读消息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request $request
     * @param  NoticeService       $service
     * @return JsonResponse
     */
    public function unread(Request $request, NoticeService $service): JsonResponse
    {
        return $this->success($service->getUnreadCountByUser($request->user()));
    }

    /**
     * 消息标记已读
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string        $id
     * @param  Request       $request
     * @param  NoticeService $service
     * @return JsonResponse
     */
    public function read(string $id, Request $request, NoticeService $service): JsonResponse
    {
        $service->markRead($request->user(), $id);
        return $this->success();
    }

    /**
     * 删除消息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $id
     * @param  Request       $request
     * @param  NoticeService $service
     * @return JsonResponse
     */
    public function delete(string $id, Request $request, NoticeService $service): JsonResponse
    {
        $service->deleteMessage($request->user(), $id);
        return $this->success();
    }

    /**
     * 审批变更记录
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string        $id
     * @param  Request       $request
     * @param  NoticeService $service
     * @return JsonResponse
     */
    public function approve(string $id, Request $request, NoticeService $service): JsonResponse
    {
        $rules = [
            'id' => 'exists:notices,id',
            'pass' => 'required|boolean',
            'description' => 'sometimes|nullable'
        ];
        $messages = [
            'id.exists' => '参数错误',
            'pass.required' => '参数错误',
            'pass.boolean' => '参数错误'
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->input()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'pass',
            'description'
        ]);
        $service->approveMessage($request->user(), $id, $data);
        return $this->success();
    }

    /**
     * 获取消息列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request $request
     * @param  NoticeService       $service
     * @return JsonResponse
     */
    public function message(Request $request, NoticeService $service): JsonResponse
    {
        $result = $service->getListByUser($request->user());
        return $this->success($result);
    }

    /**
     * 获取消息详情
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string        $id
     * @param  Request       $request
     * @param  NoticeService $service
     * @return JsonResponse
     */
    public function messageDetail(string $id,Request $request,NoticeService $service):JsonResponse
    {
        $rules = [
            'id' => 'exists:notices,id'
        ];
        $messages = [
            'id.exists' => '参数错误'
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->input()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $result = $service->getDetail($request->user(),$id);
        return $this->success($result);
    }

    /**
     * 修改个人资料
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request     $request
     * @param  ProfileService $service
     * @return RedirectResponse
     */
    public function save(Request $request, ProfileService $service): RedirectResponse
    {
        $rules = [
            'name' => 'required',
            'pinyin' => 'required',
            'gender' => 'required|integer',
            'birth' => 'required|date',
            'nation' => 'required|integer',
            'birthplace' => 'required',
            'address' => 'required',
            'id_card' => 'required|unique:profiles,id_card,' . auth('users')->user()->profile->id,
            'educational' => 'required',
            'science' => 'required',
            'emergency_contact' => 'required',
            'emergency_telephone' => 'required',
            'skill_level' => 'required|integer',
            'career_level' => 'required|integer',
            'attend_date' => 'required|date',
            'entry_date' => 'required|date',
            'vocational_skills' => 'sometimes|nullable|array'
        ];
        $messages = [
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
            'gender.integer' => '性别称谓不正确',
            'birth.date' => '出生日期不正确',
            'nation.integer' => '民族不正确',
            'id_card.unique' => '证件号码已存在',
            'skill_level.integer' => '综合技能等级不正确',
            'career_level.integer' => '职业等级不正确',
            'attend_date.date' => '工作时间不正确',
            'entry_date.date' => '入职时间不正确',
            'vocational_skills.array' => '职业技能参数错误'
        ];
        $data = $request->validate($rules, $messages);
        $service->updateOrCreate(auth('users')->user(), $data);
        return Redirect::back()->with([
            'success' => '修改个人资料成功',
            'extra' => [
                'tab' => 'profile'
            ]
        ]);
    }

    /**
     * 修改账号信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request        $request
     * @param  AccountService $service
     * @return JsonResponse
     */
    public function account(Request $request, AccountService $service): JsonResponse
    {

        $rules = [
            'account' => 'required|unique:accounts,account,' . auth('users')->user()->id . ',user_id',
            'type' => 'required|in:' . implode(',', $service->getTypeOptions())
        ];
        $messages = [
            'account.required' => '账号资料不能为空',
            'account.unique' => '账号资料已存在',
            'type.required' => '账号资料类型不能为空',
            'type.in' => '账号资料类型不正确'
        ];
        $data = $request->validate($rules, $messages);
        $service->updateAccount(auth('users')->user(), (string) $data['type'], (string) $data['account']);

        return $this->success();
    }

    /**
     * 更新已验证用户的密码
     *
     * @param  Request          $request
     * @param  AuthService  $service
     * @return RedirectResponse
     */
    public function password(Request $request, AuthService $service): RedirectResponse
    {
        $userId = auth('users')->user()->id;

        $rules = [
            'current_password' => 'required|current_password:users',
            'password' => 'required|between:6,32|confirmed',
            'password_confirmation' => 'required',
        ];

        $messages = [
            'current_password.required' => '旧密码不能为空',
            'current_password.current_password' => '旧密码不正确',
            'password.required' => '登录密码不能为空',
            'password.between' => '请输入6-32位的密码',
            'password.confirmed' => '两次输入不相同',
            'password_confirmation.required' => '请确认登录密码',
        ];
        if (auth('users')->user()->is_super) {
            $rules['password'] = 'required|between:15,32|confirmed';
            $messages['password.between'] = '请输入15-32位的密码';
        }

        $data = $request->validate($rules, $messages);
        $service->password($userId, (string) $data['password']);
        auth('users')->logout();

        return redirect()->route('login')->with('success', '修改密码成功，请重新登录');
    }

    /**
     * 校验登录密码
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request     $request
     * @param  AuthService $service
     * @return JsonResponse
     */
    public function checkPassword(Request $request, AuthService $service): JsonResponse
    {
        $rules = [
            'password' => 'required|current_password:users'
        ];
        $messages = [
            'password.required' => '登录密码不能为空',
            'password.current_password' => '登录密码不正确'
        ];
        $request->validate($rules, $messages);
        return $this->success();
    }

    /**
     * 获取已验证用户的操作日志。
     *
     * @param  UserLogService  $service
     * @return JsonResponse
     */
    public function log(UserLogService $service): JsonResponse
    {
        $user_id = auth('users')->user()->id;
        return $this->success($service->getList(['user_id' => $user_id]));
    }
}
