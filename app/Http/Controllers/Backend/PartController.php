<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\AssemblyService;
use App\Services\Backend\DictService;
use App\Services\Backend\PartService;
use App\Services\Backend\PartItemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * 零件清单控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class PartController extends Controller
{

    /**
     * 零件清单
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function index(Request $request, DictService $dictService): InertiaResponse
    {
        return Inertia::render('Part/Index', [
            'engine_type' => $dictService->getOptionByCode('engine_type'),
            'assemblies' => (new AssemblyService)->getOptions()
        ]);
    }

    /**
     * 获取扭矩数据列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request           $request
     * @param  PartService $partService
     * @return JsonResponse
     */
    public function list(Request $request, PartService $partService): JsonResponse
    {
        $rules = [
            'keyword' => 'sometimes|nullable',
            'type' => 'sometimes|nullable|in:esd,one_time,traceability',
            'engine' => 'sometimes|nullable|integer',
            'assembly' => 'sometimes|nullable|exists:assemblies,id',
        ];
        $messages = [
            'type.in' => '零件类型不正确',
            'engine.integer' => '机型不正确',
            'assembly.exists' => '总成号不正确',
        ];
        $data = $request->validate($rules, $messages);
        $result = $partService->getList($request->user(), $data);
        return $this->success($result);
    }

    /**
     * 添加零件
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  PartService  $partService
     * @return JsonResponse
     */
    public function create(Request $request, PartService $partService): JsonResponse
    {
        $rules = [
            'name' => 'required|max:100',
            'name_en' => 'sometimes|nullable|max:200',
            'station' => 'sometimes|nullable',
            'number' => 'required|unique:parts,number',
            'is_esd' => 'sometimes|nullable|boolean',
            'is_traceability' => 'sometimes|nullable|boolean',
            'is_one_time' => 'sometimes|nullable|boolean',
            'assemblies' => 'sometimes|nullable|array',
            'assemblies.*.id' => 'required|exists:assemblies,id',
            'assemblies.*.num' => 'required|integer'
        ];

        $messages = [
            'number.required' => '零件号不能为空',
            'number.unique' => '零件号已存在',
            'name.required' => '中文名称不能为空',
            'name.max' => '中文名称最大支持100个字符',
            'name_en.max' => '英文名称最大支持200个字符',
            'is_esd.boolean' => 'ESD属性不正确',
            'is_traceability.boolean' => '追踪件属性不正确',
            'is_one_time.boolean' => '一次性零件属性不正确',
            'assemblies.array' => '应用总成不正确',
            'assemblies.*.id.required' => '总成号不能为空',
            'assemblies.*.num.required' => '数量不能为空',
            'assemblies.*.id.exists' => '总成号不正确',
            'assemblies.*.num.integer' => '数量不正确'
        ];

        $data = $request->validate($rules, $messages);
        $partService->createPart($request->user(), $data);
        return $this->success();
    }


    /**
     * 更新扭矩数据库
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string            $id
     * @param  Request           $request
     * @param  PartService $partService
     * @return JsonResponse
     */
    public function update(string $id, Request $request, PartService $partService): JsonResponse
    {
        $rules = [
            'id' => 'exists:parts,id',
            'name' => 'required|max:100',
            'name_en' => 'sometimes|nullable|max:200',
            'station' => 'sometimes|nullable',
            'number' => 'required|unique:parts,number,' . $id,
            'is_esd' => 'sometimes|nullable|boolean',
            'is_traceability' => 'sometimes|nullable|boolean',
            'is_one_time' => 'sometimes|nullable|boolean',
            'assemblies' => 'sometimes|nullable|array',
            'assemblies.*.id' => 'required|exists:assemblies,id',
            'assemblies.*.num' => 'required|integer'
        ];
        $messages = [
            'number.required' => '零件号不能为空',
            'number.unique' => '零件号已存在',
            'name.required' => '中文名称不能为空',
            'name.max' => '中文名称最大支持100个字符',
            'name_en.max' => '英文名称最大支持200个字符',
            'is_esd.boolean' => 'ESD属性不正确',
            'is_traceability.boolean' => '追踪件属性不正确',
            'is_one_time.boolean' => '一次性零件属性不正确',
            'assemblies.array' => '应用总成不正确',
            'assemblies.*.id.required' => '总成号不能为空',
            'assemblies.*.num.required' => '数量不能为空',
            'assemblies.*.id.exists' => '总成号不正确',
            'assemblies.*.num.integer' => '数量不正确',
            'id.exists' => '零件信息不正确',
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'name',
            'name_en',
            'station',
            'number',
            'is_esd',
            'is_traceability',
            'is_one_time',
            'assemblies'
        ]);
        $partService->updatePart($request->user(), $id, $data);
        return $this->success();
    }


    /**
     * 获取导入模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  PartService    $partService
     * @return BinaryFileResponse
     */
    public function template(PartService $partService): BinaryFileResponse
    {
        return $partService->downloadImportTemplate();
    }

    /**
     * 导入零件清单
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @param  PartService $partService
     * @return JsonResponse
     */
    public function import(Request $request, PartService $partService): JsonResponse
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
        $partService->importByUser($request->user(), $data['file']);
        return $this->success();
    }


    /**
     * 删除零件
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $id
     * @param  Request      $request
     * @param  PartService  $partService
     * @return JsonResponse
     */
    public function delete(string $id, Request $request, PartService $partService): JsonResponse
    {
        $rules = [
            'id' => 'exists:parts,id'
        ];
        $messages = [
            'id.exists' => '零件信息不存在',
        ];
        $validator = Validator::make([
            'id' => $id
        ], $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $partService->deletePart($id, $request->user());
        return $this->success();
    }

    /**
     * 零件列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $id
     * @param  Request      $request
     * @param  PartItemService  $partItemService
     * @return JsonResponse
     */
    public function item(string $id, Request $request, PartItemService $partItemService): JsonResponse
    {
        $rules = [
            'id' => 'exists:parts,id'
        ];
        $messages = [
            'id.exists' => '零件信息不存在',
        ];
        $validator = Validator::make([
            'id' => $id
        ], $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $partItemService->getListById($request->user(),$id);
        return $this->success();
    }
}
