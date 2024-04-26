<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Frontend\UserService;
use App\Services\Frontend\NoticeService;
use App\Services\Frontend\DepartmentService;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{
    /**
     * 个人资料
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  UserService  $userService
     * @return JsonResponse
     */
    public function profile(Request $request, UserService $userService): JsonResponse
    {
        $result = $userService->getProfile($request->user());
        return $this->success($result);
    }

    /**
     * 个人中心
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  UserService  $userService
     * @return JsonResponse
     */
    public function index(Request $request, UserService $userService): JsonResponse
    {
        $result = $userService->getIndex($request->user());
        return $this->success($result);
    }

    /**
     * 首页技能数据
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  UserService  $userService
     * @return JsonResponse
     */
    public function statistic(Request $request, UserService $userService): JsonResponse
    {
        $result = $userService->getStatistic($request->user());
        return $this->success($result);
    }

    /**
     * 上传员工头像
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  UserService  $userService
     * @return JsonResponse
     */
    public function avatar(Request $request, UserService $userService): JsonResponse
    {
        $rules = [
            'file' => 'required|image'
        ];
        $messages = [
            'file.required' => __('user.avatar.file.required'),
            'file.image' => __('user.avatar.file.image')
        ];
        $data = $request->validate($rules, $messages);
        $result = $userService->updateAvatar($request->user(), $data['file']);
        return $this->success($result);
    }

    /**
     * 获取技能矩阵
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  UserService  $userService
     * @return JsonResponse
     */
    public function skill(Request $request, UserService $userService): JsonResponse
    {
        $result = $userService->getSkill($request->user());
        return $this->success($result);
    }

    /**
     * 获取部门列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  DepartmentService $departmentService
     * @return JsonResponse
     */
    public function department(DepartmentService $departmentService): JsonResponse
    {
        $result = $departmentService->getFullOptions();
        return $this->success($result);
    }

    /**
     * 保存个人信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  UserService  $userService
     * @return JsonResponse
     */
    public function setting(Request $request, UserService $userService): JsonResponse
    {

        $user_id = $request->user()->id;
        $rules = [
            'number' => 'required|unique:accounts,account,' . $user_id . ',user_id',
            'mobile' => 'required|unique:accounts,account,' . $user_id . ',user_id',
            'email' => 'required|unique:accounts,account,' . $user_id . ',user_id',
            'username' => 'required|unique:accounts,account,' . $user_id . ',user_id',
            'name' => 'required',
            'pinyin' => 'required',
            'gender' => 'required|integer',
            'birth' => 'required|date',
            'nation' => 'required|integer',
            'birthplace' => 'required',
            'address' => 'required',
            'id_card' => 'required|unique:profiles,id_card,' . $user_id . ',user_id',
            'educational' => 'required',
            'science' => 'required',
            'emergency_contact' => 'required',
            'emergency_telephone' => 'required',
            'skill_level' => 'required|integer',
            'career_level' => 'required|integer',
            'attend_date' => 'required|date',
            'entry_date' => 'required|date',
            'vocational_skills' => 'sometimes|nullable|array',
        ];
        $messages = [
            'number.required' => '员工工号不能为空',
            'mobile.required' => '手机号码不能为空',
            'email.required' => '邮箱地址不能为空',
            'username.required' => '登录账号不能为空',
            'number.unique' => '员工工号已经存在',
            'mobile.unique' => '手机号码已经存在',
            'email.unique' => '邮箱地址已经存在',
            'username.unique' => '登录账号已经存在',
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
            'vocational_skills.array' => '职业技能参数错误',
        ];
        $data = $request->validate($rules, $messages);
        $userService->setting($request->user(), $data);
        return $this->success();
    }

    /**
     * 获取生日卡片
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  UserService  $userService
     * @return JsonResponse
     */
    public function birth(Request $request, UserService $userService): JsonResponse
    {
        $result = $userService->birth($request->user());
        return $this->success($result);
    }


    public function notice(Request $request, NoticeService $noticeService): JsonResponse
    {
        $rules = [
            'type' => 'sometimes|nullable|integer',
        ];
        $messages = [
            'type.integer' => __('user.notice.type.integer')
        ];
        $data = $request->validate($rules, $messages);
        $result = $noticeService->getList($request->user(), $data);
        return $this->success($result);
    }
}
