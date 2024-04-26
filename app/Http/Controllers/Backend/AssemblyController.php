<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\AssemblyService;
use App\Services\Backend\DictService;
use App\Services\Backend\RoleService;
use App\Services\Backend\BirthdayCardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * 基础信息控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class AssemblyController extends Controller
{

    /**
     * 基础信息管理
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @param  DictService     $dictService
     * @return InertiaResponse
     */
    public function index(Request $request, DictService $dictService): InertiaResponse
    {
        return Inertia::render('Assembly/Index', [
            'assembly_status' => $dictService->getOptionByCode('assembly_status'),
            'assembly_line' => $dictService->getOptionByCode('assembly_line'),
            'engine_type' => $dictService->getOptionByCode('engine_type'),
            'plant' => $dictService->getOptionByCode('plant'),
        ]);
    }

    /**
     * 获取总成列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @param  AssemblyService $assemblyService
     * @return JsonResponse
     */
    public function list(Request $request, AssemblyService $assemblyService): JsonResponse
    {
        $rules = [
            'keyword' => 'sometimes|nullable',
            'type' => 'sometimes|nullable|integer',
            'status' => 'sometimes|nullable|integer',
        ];
        $messages = [
            'type.integer' => '参数错误',
            'status.integer' => '参数错误',
        ];
        $data = $request->validate($rules, $messages);
        $result = $assemblyService->getList($data);
        return $this->success($result);
    }

    /**
     * 添加总成信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request           $request
     * @param  AssemblyService       $assemblyService
     * @return JsonResponse
     */
    public function create(Request $request, AssemblyService $assemblyService): JsonResponse
    {
        $rules = [
            'type' => 'required|integer',
            'plant' => 'required|integer',
            'line' => 'required|integer',
            'status' => 'required|integer',
            'number' => 'required|max:250|unique:assemblies,number',
            'remark' => 'sometimes|nullable|max:250'
        ];
        $messages = [
            'type.required' => '机型不能为空',
            'plant.required' => '工厂不能为空',
            'line.required' => '产线不能为空',
            'status.required' => '阶段状态不能为空',
            'type.integer' => '机型不正确',
            'plant.integer' => '工厂不正确',
            'line.integer' => '产线不正确',
            'status.integer' => '阶段状态不正确',
            'number.required' => '总成号不能为空',
            'number.unique' => '总成号已存在',
            'number.max' => '总成号不能超过250个字符',
            'remark.max' => '备注信息不能超过250个字符',
        ];
        $data = $request->validate($rules, $messages);
        $assemblyService->create($data);
        return $this->success();
    }

    /**
     * 编辑总成信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string            $id
     * @param  Request        $request
     * @param  AssemblyService       $assemblyService
     * @return JsonResponse
     */
    public function update(string $id, Request $request, AssemblyService $assemblyService): JsonResponse
    {
        $rules = [
            'id' => 'exists:assemblies,id',
            'type' => 'required|integer',
            'plant' => 'required|integer',
            'line' => 'required|integer',
            'status' => 'required|integer',
            'number' => 'required|max:250|unique:assemblies,number,' . $id . ',id',
            'remark' => 'sometimes|nullable|max:250'
        ];
        $messages = [
            'type.required' => '机型不能为空',
            'plant.required' => '工厂不能为空',
            'line.required' => '产线不能为空',
            'status.required' => '阶段状态不能为空',
            'type.integer' => '机型不正确',
            'plant.integer' => '工厂不正确',
            'line.integer' => '产线不正确',
            'status.integer' => '阶段状态不正确',
            'number.required' => '总成号不能为空',
            'number.unique' => '总成号已存在',
            'number.max' => '总成号不能超过250个字符',
            'remark.max' => '备注信息不能超过250个字符',
            'id.exists' => '总成不存在',
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'type',
            'plant',
            'line',
            'status',
            'number',
            'remark',
        ]);
        $assemblyService->update($id, $data);
        return $this->success();
    }

    /**
     * 删除总成信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $id
     * @param  Request      $request
     * @param  AssemblyService  $assemblyService
     * @return JsonResponse
     */
    public function patch(string $id,AssemblyService $assemblyService): JsonResponse
    {
        $assemblyService->delete($id);
        return $this->success();
    }


    /**
     * 导出总成信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request           $request
     * @param  AssemblyService       $assemblyService
     * @return JsonResponse
     */
    public function export(Request $request, AssemblyService $assemblyService): JsonResponse
    {
        $rules = [
            'keyword' => 'sometimes|nullable',
            'type' => 'sometimes|nullable|integer',
            'status' => 'sometimes|nullable|integer',
        ];
        $messages = [
            'type.integer' => '参数错误',
            'status.integer' => '参数错误',
        ];
        $data = $request->validate($rules, $messages);
        $result = $assemblyService->export($data);
        return $this->success($result);
    }

    /**
     * 下载总成导入模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  AssemblyService        $assemblyService
     * @return BinaryFileResponse
     */
    public function template(AssemblyService $assemblyService): BinaryFileResponse
    {
        return $assemblyService->downloadImportTemplate();
    }

    /**
     * 导入总成基础信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  AssemblyService  $assemblyService
     * @return JsonResponse
     */
    public function import(Request $request, AssemblyService $assemblyService): JsonResponse
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
        $assemblyService->import($data['file']);
        return $this->success();
    }

    /**
     * 获取总成选项列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @param  AssemblyService $assemblyService
     * @return JsonResponse
     */
    public function option(Request $request, AssemblyService $assemblyService): JsonResponse
    {
        $rules = [
            'plant' => 'sometimes|nullable|integer',
            'line' => 'sometimes|nullable|integer',
            'type' => 'sometimes|nullable|integer',
        ];
        $messages = [
            'plant.integer' => '参数错误',
            'line.integer' => '参数错误',
            'type.integer' => '参数错误',
        ];
        $data = $request->validate($rules, $messages);
        $result = $assemblyService->getOptions($data);
        return $this->success($result);
    }

}
