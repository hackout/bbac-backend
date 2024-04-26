<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\DepartmentService;
use App\Services\Backend\DictService;
use App\Services\Backend\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Http\JsonResponse;

/**
 * 组织机构控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class DepartmentController extends Controller
{

    /**
     * 组织机构视图
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @param  DepartmentService $departmentService
     * @return InertiaResponse
     */
    public function index(Request $request, DepartmentService $departmentService): InertiaResponse
    {
        return Inertia::render('Department/Index', [
            'departments' => $departmentService->getFullOptions($request->user()),
            'users' => (new UserService)->getOptions(),
            'department_role' => (new DictService)->getOptionByCode('department_role')
        ]);
    }

    /**
     * 获取组织结构树型列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request           $request
     * @param  DepartmentService $departmentService
     * @return JsonResponse
     */
    public function list(Request $request, DepartmentService $departmentService): JsonResponse
    {
        $rules = [
            'parent_id' => 'sometimes|nullable|exists_or_null:departments,id'
        ];
        $messages = [
            'parent_id.exists_or_null' => '组织机构参数不正确'
        ];
        $data = $request->validate($rules, $messages);
        $result = $departmentService->getList($request->user(),$data);
        return $this->success($result);
    }

    /**
     * 创建组织机构
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request           $request
     * @param  DepartmentService $departmentService
     * @return JsonResponse
     */
    public function create(Request $request, DepartmentService $departmentService): JsonResponse
    {
        $rules = [
            'name' => 'required|max:100',
            'contact' => 'sometimes|nullable',
            'mobile' => 'sometimes|nullable',
            'email' => 'sometimes|nullable|email',
            'leader_id' => 'sometimes|nullable|exists_or_null:users,id',
            'parent_id' => 'sometimes|nullable|exists_or_null:departments,id',
            'role' => 'sometimes|nullable|integer'
        ];
        $messages = [
            'name.required' => '部门名称不能为空',
            'name.max' => '部门名称最大支持100个字符',
            'email.email' => '联系邮箱不正确',
            'role.integer' => '部门权限不正确',
            'parent_id.exists_or_null' => '上级部门错误',
            'leader_id.exists_or_null' => '负责人用户信息错误',
        ];
        $data = $request->validate($rules, $messages);
        $departmentService->create($data);
        return $this->success();
    }

    /**
     * 更新组织机构
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string            $id
     * @param  Request           $request
     * @param  DepartmentService $departmentService
     * @return JsonResponse
     */
    public function update(string $id, Request $request, DepartmentService $departmentService): JsonResponse
    {
        $rules = [
            'id' => 'exists:departments,id',
            'name' => 'required|max:100',
            'contact' => 'sometimes|nullable',
            'mobile' => 'sometimes|nullable',
            'email' => 'sometimes|nullable|email',
            'leader_id' => 'sometimes|nullable|exists_or_null:users,id',
            'parent_id' => 'sometimes|nullable|exists_or_null:departments,id|not_in:' . $id,
            'role' => 'sometimes|nullable|integer'
        ];
        $messages = [
            'name.required' => '部门名称不能为空',
            'name.max' => '部门名称最大支持100个字符',
            'email.email' => '联系邮箱不正确',
            'role.integer' => '部门权限不正确',
            'parent_id.exists_or_null' => '上级部门错误',
            'leader_id.exists_or_null' => '负责人用户信息错误',
            'parent_id.not_in' => '上级部门不能是自身',
            'id.exists' => '部门信息错误',
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'name',
            'contact',
            'mobile',
            'email',
            'leader_id',
            'parent_id',
            'role'
        ]);
        $departmentService->update($id, $data);
        return $this->success();
    }

    /**
     * 批量删除组织机构
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request           $request
     * @param  DepartmentService $departmentService
     * @return JsonResponse
     */
    public function batchDelete(Request $request, DepartmentService $departmentService): JsonResponse
    {
        $rules = [
            'ids' => 'required|array',
            'ids.*' => 'required|exists:departments,id'
        ];
        $messages = [
            'ids.required' => '请选择一个部门',
            'ids.array' => '部门参数错误',
            'ids.*.required' => '部门参数不能为空',
            'ids.*.exists' => '部门参数错误',
        ];
        $data = $request->validate($rules, $messages);
        $departmentService->batch_delete((array) $data['ids']);
        return $this->success();
    }

    /**
     * 下载组织机构导入模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  DepartmentService  $departmentService
     * @return BinaryFileResponse
     */
    public function template(DepartmentService $departmentService): BinaryFileResponse
    {
        return $departmentService->downloadImportTemplate();
    }

    /**
     * 导入组织机构信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request           $request
     * @param  DepartmentService $departmentService
     * @return JsonResponse
     */
    public function import(Request $request, DepartmentService $departmentService): JsonResponse
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
        $departmentService->import($data['file']);
        return $this->success();
    }
}
