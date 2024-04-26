<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * 角色管理控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class RoleController extends Controller
{

    /**
     * 角色视图
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function index(Request $request): InertiaResponse
    {
        return Inertia::render('Role/Index', [
            'query' => [
                'page' => intval($request->input('page', 1)),
                'limit' => intval($request->input('limit', 20)),
                'keyword' => $request->input('keyword', null)
            ],
            'permissions' => config('permission')
        ]);
    }

    /**
     * 查询角色列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  RoleService  $roleService
     * @return JsonResponse
     */
    public function list(Request $request, RoleService $roleService): JsonResponse
    {
        $rules = [
            'keyword' => 'sometimes|nullable'
        ];
        $data = $request->validate($rules);
        $result = $roleService->getList($data);
        return $this->success($result);
    }


    /**
     * 新建角色
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  RoleService  $roleService
     * @return JsonResponse
     */
    public function create(Request $request, RoleService $roleService): JsonResponse
    {
        $rules = [
            'name' => 'required|max:100',
            'is_valid' => 'sometimes|nullable|boolean',
            'permissions' => 'sometimes|nullable|array',
            'permissions.*' => 'required|in:' . $roleService->getPermissions()
        ];
        $messages = [
            "name.required" => "角色名不能为空",
            "name.max" => "角色名最大支持100个字符",
            "is_valid.boolean" => "角色状态错误",
            "permissions.*.in" => "角色权限错误",
            "permissions.array" => "角色权限错误"
        ];
        $data = $request->validate($rules, $messages);
        $roleService->create($data);
        return $this->success();
    }

    /**
     * 更新角色
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $id
     * @param  Request      $request
     * @param  RoleService  $roleService
     * @return JsonResponse
     */
    public function update(string $id, Request $request, RoleService $roleService): JsonResponse
    {
        $rules = [
            'id' => 'exists:roles,id',
            'name' => 'required|max:100',
            'is_valid' => 'sometimes|nullable|boolean',
            'permissions' => 'sometimes|nullable|array',
            'permissions.*' => 'required|in:' . $roleService->getPermissions()
        ];
        $messages = [
            "name.required" => "角色名不能为空",
            "name.max" => "角色名最大支持100个字符",
            "is_valid.boolean" => "角色状态错误",
            "permissions.*.in" => "角色权限错误",
            "permissions.array" => "角色权限错误",
            "id.exists" => "角色不存在"
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'name',
            'is_valid',
            'permissions'
        ]);
        $roleService->update($id, $data);
        return $this->success();
    }

    /**
     * 批量删除角色
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  RoleService  $roleService
     * @return JsonResponse
     */
    public function batchDelete(Request $request, RoleService $roleService): JsonResponse
    {
        $rules = [
            'ids' => 'required|array',
            'ids.*' => 'required|exists:roles,id'
        ];
        $messages = [
            "ids.required" => "请选择一个角色",
            "ids.array" => "角色参数错误",
            "ids.*.required" => "角色参数不能为空",
            "ids.*.exists" => "角色参数错误"
        ];
        $data = $request->validate($rules, $messages);
        $roleService->batch_delete((array) $data['ids']);
        return $this->success();
    }

    /**
     * 设置角色有效性
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $id
     * @param  Request      $request
     * @param  RoleService  $roleService
     * @return JsonResponse
     */
    public function valid(string $id, Request $request, RoleService $roleService): JsonResponse
    {
        $rules = [
            'id' => 'exists:roles,id'
        ];
        $messages = [
            "id.exists" => "角色不存在"
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $roleService->valid($id);
        return $this->success();
    }

}
