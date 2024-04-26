<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\CommitService;
use App\Services\Backend\DictService;
use App\Services\Backend\ExamineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;

/**
 * 考核定义控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class ExamineController extends Controller
{

    /**
     * 已审核考核列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function index(Request $request, DictService $dictService, CommitService $commitService): InertiaResponse
    {
        return Inertia::render('Examine/Index', [
            'sub_type' => $dictService->getOptionByCode('sub_type'),
            'examine_type' => $dictService->getOptionByCode('examine_type'),
            'template_status' => $dictService->getOptionByCode('template_status'),
            'inline_type' => $commitService->getInlineOptions(),
            'product_type' => $commitService->getProductOptions(),
            'service_type' => $commitService->getServiceOptions(),
            'engine_type' => $dictService->getOptionByCode('engine_type')
        ]);
    }



    /**
     * 获取考核模板列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request        $request
     * @param  ExamineService $examineService
     * @return JsonResponse
     */
    public function list(Request $request, ExamineService $examineService): JsonResponse
    {
        $rules = [
            'sub_type' => 'sometimes|nullable|integer',
            'keyword' => 'sometimes|nullable',
            'type' => 'sometimes|nullable|integer',
            'status' => 'sometimes|nullable|integer',
        ];
        $messages = [
            'sub_type.integer' => '考核类型不正确',
            'type.integer' => '模板类型不正确',
            'status.integer' => '审核状态不正确',
        ];
        $data = $request->validate($rules, $messages);
        $result = $examineService->getList($data);
        return $this->success($result);
    }

    public  function option(Request $request,ExamineService $examineService):JsonResponse
    {
        $rules = [
            'type' => 'sometimes|nullable|integer'
        ];
        $messages = [
            'type.integer' => '模板类型不正确'
        ];
        $data = $request->validate($rules, $messages);
        $result = $examineService->getOption($data);
        return $this->success($result);
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


    /**
     * 删除考核模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string         $id
     * @param  ExamineService $examineService
     * @return JsonResponse
     */
    public function delete(string $id, ExamineService $examineService): JsonResponse
    {
        $examineService->delete($id);
        return $this->success();
    }
}
