<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\DictService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * 字典控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class DictController extends Controller
{

    /**
     * 字典视图
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function index(Request $request): InertiaResponse
    {
        return Inertia::render('Dict/Index');
    }

    /**
     * 获取字典列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request           $request
     * @param  DictService $dictService
     * @return JsonResponse
     */
    public function list(Request $request, DictService $dictService): JsonResponse
    {
        $rules = [
            'keyword' => 'sometimes|nullable',
        ];
        $data = $request->validate($rules);
        $result = $dictService->getList($data);
        return $this->success($result);
    }
    
    public function option(string $code,DictService $dictService):JsonResponse
    {
        return $this->success(
            $dictService->getOptionByCode($code)
        );
    }

    /**
     * 创建字典
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request           $request
     * @param  DictService $dictService
     * @return JsonResponse
     */
    public function create(Request $request, DictService $dictService): JsonResponse
    {
        $rules = [
            'name' => 'required|max:100',
            'code' => 'required|unique:dicts,code',
            'description' => 'sometimes|nullable'
        ];
        $messages = [
            'name.required' => '字典名称不能为空',
            'name.max' => '字典名称最大支持100个字符',
            'code.required' => '字典标识不能为空',
            'code.unique' => '字典标识已存在',
        ];
        $data = $request->validate($rules, $messages);
        $dictService->create($data);
        return $this->success();
    }

    /**
     * 更新字典
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  int            $id
     * @param  Request           $request
     * @param  DictService $dictService
     * @return JsonResponse
     */
    public function update(int $id, Request $request, DictService $dictService): JsonResponse
    {
        $rules = [
            'id' => 'exists:dicts,id',
            'name' => 'required|max:100',
            'code' => 'required|unique:dicts,code,' . $id,
            'description' => 'sometimes|nullable'
        ];
        $messages = [
            'name.required' => '字典名称不能为空',
            'name.max' => '字典名称最大支持100个字符',
            'code.required' => '字典标识不能为空',
            'code.unique' => '字典标识已存在',
            'id.exists' => '字典信息错误',
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'name',
            'code',
            'description'
        ]);
        $dictService->update($id, $data);
        return $this->success();
    }

    /**
     * 导出字典数据
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request           $request
     * @param  DictService       $dictService
     * @return JsonResponse
     */
    public function export(Request $request, DictService $dictService): JsonResponse
    {
        $rules = [
            'keyword' => 'sometimes|nullable',
        ];
        $data = $request->validate($rules);
        $result = $dictService->export($data);
        return $this->success($result);
    }
}
