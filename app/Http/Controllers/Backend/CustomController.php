<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Backend\CustomExportService;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * 自定义导出控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class CustomController extends Controller
{

    /**
     * 自定义导出数据
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request             $request
     * @param  CustomExportService $customExportService
     * @return JsonResponse
     */
    public function export(Request $request, CustomExportService $customExportService): JsonResponse
    {
        $rules = [
            'data' => 'required|array'
        ];
        $messages = [
            'data.required' => '导出参数不能为空',
            'data.array' => '导出参数错误'
        ];
        $data = $request->validate($rules,$messages);
        $result = $customExportService->makeExcel($data['data']);
        return $this->success($result);
    }
}
