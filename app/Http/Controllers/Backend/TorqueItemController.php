<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\AssemblyService;
use App\Services\Backend\DictService;
use App\Services\Backend\TorqueItemService;
use Illuminate\Http\Request;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * 扭矩数据控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class TorqueItemController extends Controller
{

    /**
     * SPC
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function index(Request $request, DictService $dictService): InertiaResponse
    {
        return Inertia::render('SPC/Index', [
            'plant' => $dictService->getOptionByCode('plant'),
            'line' => $dictService->getOptionByCode('assembly_line'),
            'engine_type' => $dictService->getOptionByCode('engine_type'),
            'assemblies' => (new AssemblyService)->getOptions(),
            'months' => (new TorqueItemService)->getMonthByYear()
        ]);
    }

    /**
     * SPC数据列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request           $request
     * @param  TorqueItemService $torqueItemService
     * @return JsonResponse
     */
    public function list(Request $request, TorqueItemService $torqueItemService): JsonResponse
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
        $result = $torqueItemService->getList($data);
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
    

}
