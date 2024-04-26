<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\DictItemService;
use App\Services\Backend\DictService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;

/**
 * 问题追踪控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class IssueController extends Controller
{

    /**
     * 在线考核-问题追踪
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @param  DictService     $dictService
     * @return InertiaResponse
     */
    public function inline(Request $request, DictService $dictService): InertiaResponse
    {
        return Inertia::render('Issue/Inline', [
            'issue_status' => $dictService->getOptionByCode('issue_status'),
            'defect_categories' => $dictService->getOptionByCode('defect_category'),
        ]);
    }

    /**
     * 产品考核-问题追踪
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @param  DictService     $dictService
     * @return InertiaResponse
     */
    public function product(Request $request, DictService $dictService): InertiaResponse
    {
        return Inertia::render('Issue/Product', [
            'issue_status' => $dictService->getOptionByCode('issue_status'),
            'defect_categories' => $dictService->getOptionByCode('defect_category'),
        ]);
    }

    /**
     * 整车服务-待处理问题
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @param  DictService     $dictService
     * @return InertiaResponse
     */
    public function service(Request $request, DictService $dictService): InertiaResponse
    {
        return Inertia::render('Issue/Service', [
            'issue_status' => $dictService->getOptionByCode('issue_status'),
            'defect_categories' => $dictService->getOptionByCode('defect_category'),
        ]);
    }

    /**
     * 整车服务-已处理问题
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @param  DictService     $dictService
     * @return InertiaResponse
     */
    public function finish(Request $request, DictService $dictService): InertiaResponse
    {
        return Inertia::render('Issue/Finish', [
            'plant' => $dictService->getOptionByCode('plant'),
            'line' => $dictService->getOptionByCode('assembly_line'),
            'engine_type' => $dictService->getOptionByCode('engine_type'),
            'assemblies' => (new AssemblyService)->getOptions(),
            'months' => (new TorqueItemService)->getMonthByYear()
        ]);
    }

    /**
     * 查询字典项列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string          $code
     * @param  Request         $request
     * @param  DictItemService $dictItemService
     * @return JsonResponse
     */
    public function list(string $code,Request $request, DictItemService $dictItemService): JsonResponse
    {
        $rules = [
            'code' => 'exists:dicts,code',
        ];
        $messages = [
            'code.exists' => '字典标识不正确',
        ];
        $validator = Validator::make(array_merge([
            'code' => $code
        ], $request->input()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'code'
        ]);
        $result = $dictItemService->getList($data);
        return $this->success($result);
    }

    /**
     * 更新信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string          $code
     * @param  integer         $id
     * @param  Request         $request
     * @param  DictItemService $dictItemService
     * @return JsonResponse
     */
    public function update(string $code,int $id, Request $request, DictItemService $dictItemService): JsonResponse
    {
        $rules = [
            'code' => 'exists:dicts,code',
            'id' => 'exists:dict_items,id',
            'name' => 'required|max:100',
            'content' => 'required|integer',
            'sort_order' => 'required|integer'
        ];
        $messages = [
            'name.required' => '字典项键名不能为空',
            'name.max' => '字典项键名最大支持100个字符',
            'content.required' => '字典项键值不能为空',
            'content.integer' => '字典项键值不正确',
            'sort_order.required' => '字典项排序不能为空',
            'sort_order.integer' => '字典项排序不正确',
            'code.exists' => '字典标识不正确',
            'id.exists' => '字典标识不正确',
        ];
        $validator = Validator::make(array_merge([
            'code' => $code,
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'code',
            'name',
            'content',
            'sort_order'
        ]);
        $dictItemService->update($id, $data);
        return $this->success();
    }


    /**
     * 导出SPC
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @param  TorqueItemService $torqueItemService
     * @return JsonResponse
     */
    public function export(Request $request, TorqueItemService $torqueItemService): JsonResponse
    {
        $rules = [
            'keyword' => 'sometimes|nullable',
            'plant' => 'sometimes|nullable|integer',
            'line' => 'sometimes|nullable|integer',
        ];
        $messages = [
            'plant.integer' => '参数错误',
            'line.integer' => '参数错误',
        ];
        $data = $request->validate($rules, $messages);
        $result = $torqueItemService->export($data);
        return $this->success($result);
    }
    
    
}
