<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\SystemConfigService;
use Illuminate\Http\Request;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * 系统设置控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class SystemConfigController extends Controller
{

    /**
     * 字典视图
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse|JsonResponse
     */
    public function index(Request $request, SystemConfigService $systemConfigService): InertiaResponse|JsonResponse
    {
        if ($request->post()) {
            $rules = $systemConfigService->getValidation();
            $data = $request->validate($rules['rules'], $rules['messages']);
            $systemConfigService->setData($data);
            return $this->success();
        }
        return Inertia::render('SystemConfig/Index', [
            'config' => $systemConfigService->getList()
        ]);
    }
}
