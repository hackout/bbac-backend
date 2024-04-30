<?php

namespace App\Http\Controllers\Backend;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Backend\CacheService;
use Inertia\Response as InertiaResponse;
use App\Services\Backend\SystemConfigService;
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

    /**
     * 系统缓存管理
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function cache(Request $request,CacheService $cacheService):InertiaResponse
    {
        $result = $cacheService->getCacheSize();
        return Inertia::render('SystemConfig/Cache', [
            'cacheSize' => $result['size'],
            'cacheTotal' => $result['total']
        ]);
    }

    /**
     * 清空系统缓存
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  CacheService $cacheService
     * @return JsonResponse
     */
    public function cacheClear(Request $request,CacheService $cacheService):JsonResponse
    {
        $cacheService->clear();
        return $this->success();
    }
}
