<?php

namespace App\Http\Controllers\Backend;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Backend\DictService;
use App\Services\Backend\ExamineService;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

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
    public function index(Request $request, DictService $dictService): InertiaResponse
    {
        return Inertia::render('Examine/Index', [
            'examine_inline_type' => $dictService->getOptionByCode('examine_inline_type'),
            'examine_product_type' => $dictService->getOptionByCode('examine_product_type'),
            'engine_type' => $dictService->getOptionByCode('engine_type'),
            'template_status' => $dictService->getOptionByCode('template_status'),
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
            'keyword' => 'sometimes|nullable',
            'type' => 'sometimes|nullable|in:inline,product,vehicle',
            'status' => 'sometimes|nullable|integer',
            'page' => 'sometimes|nullable',
            'limit' => 'sometimes|nullable'
        ];
        $messages = [
            'type.in' => '模板类型不正确',
            'status.integer' => '审核状态不正确',
        ];
        $data = $request->validate($rules, $messages);
        $result = $examineService->getList($request->user(), $data);
        return $this->success($result);
    }

    /**
     * 获取考核模板选项列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request        $request
     * @param  ExamineService $examineService
     * @return JsonResponse
     */
    public function option(string $type,Request $request, ExamineService $examineService): JsonResponse
    {
        $result = $examineService->getOption($request->user(), $type);
        return $this->success($result);
    }

    /**
     * 删除考核模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string         $id
     * @param  string         $type
     * @param  Request        $request
     * @param  ExamineService $examineService
     * @return JsonResponse
     */
    public function delete(string $id, string $type, Request $request, ExamineService $examineService): JsonResponse
    {
        $examineService->delete($request->user(), $type, $id);
        return $this->success();
    }
}
