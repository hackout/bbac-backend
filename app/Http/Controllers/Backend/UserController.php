<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\DepartmentService;
use App\Services\Backend\DictService;
use App\Services\Backend\RoleService;
use App\Services\Backend\UserService;
use App\Services\Backend\BirthdayCardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * 员工控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class UserController extends Controller
{

    /**
     * 员工管理
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @param  DictService     $dictService
     * @return InertiaResponse
     */
    public function index(Request $request, DictService $dictService): InertiaResponse
    {
        return Inertia::render('User/Index', [
            'departments' => (new DepartmentService)->getFullOptions(),
            'roles' => (new RoleService)->getOptions(),
            'default_password' => env('DEFAULT_PASSWORD', '123456'),
            'gender' => $dictService->getOptionByCode('gender'),
            'nation' => $dictService->getOptionByCode('nation'),
            'skill_level' => $dictService->getOptionByCode('skill_level'),
            'career_level' => $dictService->getOptionByCode('career_level'),
            'engine_type' => $dictService->getOptionByCode('engine_type'),
        ]);
    }

    /**
     * 员工详情
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string         $id
     * @param  Request        $request
     * @param  UserService $userService
     * @return JsonResponse
     */
    public function detail(string $id, Request $request, UserService $userService): JsonResponse
    {
        $rules = [
            'id' => 'exists:users,id'
        ];
        $messages = [
            'id.exists' => '用户不存在',
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $result = $userService->getDetailByLeader($id, $request->user());
        return $this->success($result);
    }

    /**
     * 获取员工列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request           $request
     * @param  DictService $dictService
     * @return JsonResponse
     */
    public function list(Request $request, UserService $userService): JsonResponse
    {
        $rules = [
            'keyword' => 'sometimes|nullable',
            'gender' => 'sometimes|nullable|integer',
            'nation' => 'sometimes|nullable|integer',
            'skill_level' => 'sometimes|nullable|integer',
            'career_level' => 'sometimes|nullable|integer',
            'department_id' => 'sometimes|nullable|exists_or_null:departments,id',
        ];
        $messages = [
            'gender.integer' => '参数错误',
            'nation.integer' => '参数错误',
            'skill_level' => '参数错误',
            'career_level' => '参数错误',
            'department_id.exists_or_null' => '部门信息错误'
        ];
        $data = $request->validate($rules, $messages);
        $result = $userService->getList($data);
        return $this->success($result);
    }

    /**
     * 添加员工信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request           $request
     * @param  UserService       $userService
     * @return JsonResponse
     */
    public function create(Request $request, UserService $userService): JsonResponse
    {
        $rules = [
            'username' => 'required_without_all:email,number,mobile|nullable|unique:accounts,account',
            'email' => 'required_without_all:username,number,mobile|nullable|unique:accounts,account',
            'number' => 'required_without_all:email,username,mobile|nullable|unique:accounts,account',
            'mobile' => 'required_without_all:email,number,username|nullable|unique:accounts,account',
            'is_valid' => 'sometimes|nullable|boolean',
            'department_id' => 'sometimes|nullable|exists_or_null:departments,id',
            'roles' => 'sometimes|nullable|array',
            'roles.*' => 'exists:roles,id'
        ];
        $messages = [
            'username.required_without_all' => '用户名/邮箱地址/员工号/手机号码不能同时为空',
            'email.required_without_all' => '用户名/邮箱地址/员工号/手机号码不能同时为空',
            'number.required_without_all' => '用户名/邮箱地址/员工号/手机号码不能同时为空',
            'mobile.required_without_all' => '用户名/邮箱地址/员工号/手机号码不能同时为空',
            'username.unique' => '用户名已存在',
            'email.unique' => '邮箱地址已存在',
            'number.unique' => '员工号已存在',
            'mobile.unique' => '手机号码已存在',
            'is_valid.boolean' => '账号状态错误',
            'department_id.exists_or_null' => '部门不存在',
            'roles.array' => '角色参数错误',
            'roles.*.exists' => '角色不存在',
        ];
        $data = $request->validate($rules, $messages);
        $userService->create($data);
        return $this->success();
    }

    /**
     * 更新员工信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string            $id
     * @param  Request        $request
     * @param  UserService    $userService
     * @return JsonResponse
     */
    public function update(string $id, Request $request, UserService $userService): JsonResponse
    {
        $rules = [
            'id' => 'exists:users,id',
            'is_valid' => 'sometimes|nullable|boolean',
            'department_id' => 'sometimes|nullable|exists_or_null:departments,id',
            'roles' => 'sometimes|nullable|array',
            'roles.*' => 'exists:roles,id'
        ];
        $messages = [
            'is_valid.boolean' => '账号状态错误',
            'department_id.exists_or_null' => '部门不存在',
            'roles.array' => '角色参数错误',
            'roles.*.exists' => '角色不存在',
            'id.exists' => '用户不存在',
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'is_valid',
            'department_id',
            'roles'
        ]);
        $userService->update($id, $data);
        return $this->success();
    }

    /**
     * 修改员工状态
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $slug
     * @param  Request      $request
     * @param  UserService  $userService
     * @return JsonResponse
     */
    public function patch(string $slug, Request $request, UserService $userService): JsonResponse
    {
        $rules = [
            'ids' => 'required|array',
            'ids.*' => 'required|exists_plus:users,id,0,is_super'
        ];
        $messages = [
            'ids.required' => '请选择一个角色',
            'ids.array' => '角色参数错误',
            'ids.*.required' => '角色参数不能为空',
            'ids.*.exists_plus' => '账号不允许删除',
        ];
        $data = $request->validate($rules, $messages);
        $userService->patch($slug, $data);
        return $this->success();
    }

    /**
     * 修改用户详细资料
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request          $request
     * @param  UserService      $service
     * @return JsonResponse
     */
    public function updateDetail(string $id, Request $request, UserService $service): JsonResponse
    {
        $rules = [
            'id' => 'exists:users,id',
            'username' => 'required|unique:accounts,account,' . $id . ',user_id',
            'number' => 'required|unique:accounts,account,' . $id . ',user_id',
            'mobile' => 'required|unique:accounts,account,' . $id . ',user_id',
            'email' => 'required|unique:accounts,account,' . $id . ',user_id',
            'name' => 'required',
            'pinyin' => 'required',
            'gender' => 'required|integer',
            'birth' => 'required|date',
            'nation' => 'required|integer',
            'birthplace' => 'required',
            'address' => 'required',
            'id_card' => 'required|unique:profiles,id_card,' . $id . ',user_id',
            'educational' => 'required',
            'science' => 'required',
            'emergency_contact' => 'required',
            'emergency_telephone' => 'required',
            'skill_level' => 'required|integer',
            'career_level' => 'required|integer',
            'attend_date' => 'required|date',
            'entry_date' => 'required|date',
            'department_id' => 'sometimes|nullable|exists_or_null:departments,id',
            'vocational_skills' => 'sometimes|nullable|array',
            'roles' => 'sometimes|nullable|array',
            'roles.*' => 'exists:roles,id'
        ];
        $messages = [
            'username.required' => '登录账号不能为空',
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
            'username.unique' => '登录账号已经存在',
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
            'department_id.exists_or_null' => '部门不存在',
            'vocational_skills.array' => '职业技能参数',
            'roles.array' => '角色参数错误',
            'roles.*.exists' => '角色不存在',
            'id.exists' => '用户不存在'
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'number',
            'mobile',
            'email',
            'name',
            'pinyin',
            'gender',
            'birth',
            'nation',
            'birthplace',
            'address',
            'id_card',
            'educational',
            'science',
            'emergency_contact',
            'emergency_telephone',
            'skill_level',
            'career_level',
            'attend_date',
            'entry_date',
            'department_id',
            'roles',
            'username',
            'vocational_skills'
        ]);
        $service->updateDetail($id, $request->user(), $data);
        return $this->success();
    }

    /**
     * 批量删除员工
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  UserService  $service
     * @return JsonResponse
     */
    public function batchDelete(Request $request, UserService $service): JsonResponse
    {
        $rules = [
            'ids' => 'required|array',
            'ids.*' => 'required|exists_plus:users,id,0,is_super'
        ];
        $messages = [
            'ids.required' => '请选择一个角色',
            'ids.array' => '角色参数错误',
            'ids.*.required' => '角色参数不能为空',
            'ids.*.exists_plus' => '账号不允许删除',
        ];
        $data = $request->validate($rules, $messages);
        $service->batch_delete((array) $data['ids']);
        return $this->success();
    }

    /**
     * 导出员工信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request           $request
     * @param  UserService       $userService
     * @return JsonResponse
     */
    public function export(Request $request, UserService $userService): JsonResponse
    {
        $rules = [
            'keyword' => 'sometimes|nullable',
            'gender' => 'sometimes|nullable|integer',
            'nation' => 'sometimes|nullable|integer',
            'skill_level' => 'sometimes|nullable|integer',
            'career_level' => 'sometimes|nullable|integer',
            'department_id' => 'sometimes|nullable|exists_or_null:departments,id',
        ];
        $messages = [
            'gender.integer' => '参数错误',
            'nation.integer' => '参数错误',
            'skill_level' => '参数错误',
            'career_level' => '参数错误',
            'department_id.exists_or_null' => '部门信息错误'
        ];
        $data = $request->validate($rules, $messages);
        $result = $userService->export($data);
        return $this->success($result);
    }

    /**
     * 下载员工导入模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  UserService        $userService
     * @return BinaryFileResponse
     */
    public function template(UserService $userService): BinaryFileResponse
    {
        return $userService->downloadImportTemplate();
    }

    /**
     * 导入员工信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  UserService  $userService
     * @return JsonResponse
     */
    public function import(Request $request, UserService $userService): JsonResponse
    {
        $rules = [
            'file' => 'required|file|mimes:xls,xlsx',
        ];
        $messages = [
            'file.required' => '上传文件不能为空',
            'file.file' => '上传文件错误',
            'file.mimes' => '上传文件格式错误'
        ];
        $data = $request->validate($rules, $messages);
        $userService->import($data['file']);
        return $this->success();
    }

    /**
     * 访问员工生日页面
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  BirthdayCardService $birthdayCardService
     * @return InertiaResponse
     */
    public function birthday(BirthdayCardService $birthdayCardService): InertiaResponse
    {
        return Inertia::render('User/Birthday', [
            'cards' => $birthdayCardService->getOptions()
        ]);
    }

    /**
     * 查询员工生日列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  UserService  $userService
     * @return JsonResponse
     */
    public function birthdayList(Request $request, UserService $userService): JsonResponse
    {
        $rules = [
            'keyword' => 'sometimes|nullable',
            'birth' => 'sometimes|nullable|date',
            'birthday_card_id' => 'sometimes|nullable|exists_or_null:birthday_cards,id'
        ];
        $messages = [
            'birth.date' => '生日信息不正确',
            'birthday_card_id.exists_or_null' => '祝福卡信息不正确',
        ];
        $data = $request->validate($rules, $messages);
        $result = $userService->getListOfBirthday($data);
        return $this->success($result);
    }

    public function birthdayUpdate(string $id, Request $request, UserService $userService): JsonResponse
    {
        $rules = [
            'id' => 'exists:birthday_cards,id',
            'ids' => 'required|array',
            'ids.*' => 'required|exists_plus:users,id,0,is_super'
        ];
        $messages = [
            'ids.required' => '请选择一个角色',
            'ids.array' => '角色参数错误',
            'ids.*.required' => '角色参数不能为空',
            'ids.*.exists_plus' => '账号不允许设置',
            'id.exists' => '卡片不存在',
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'ids'
        ]);
        $userService->saveBirthdayCard($id, $data['ids']);
        return $this->success();
    }

    /**
     * 根据部门查询员工
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $id
     * @param  UserService  $service
     * @return JsonResponse
     */
    public function department(string $id,UserService $service): JsonResponse
    {
        $rules = [
            'id' => 'exists:departments,id',
        ];
        $messages = [
            'id.exists' => '参数错误'
        ];
        $validator = Validator::make([
            'id' => $id
        ], $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $result = $service->getMemberByDepartment($id);
        return $this->success($result);
    }
}
