<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\UserLogService;
use Illuminate\Http\Request;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * 后台访问记录控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class UserLogController extends Controller
{

    /**
     * 后台访问记录
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function index(Request $request): InertiaResponse
    {
        return Inertia::render('UserLog/Index');
    }

    /**
     * 查询后台记录
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request $request
     * @param  UserLogService $userLogService
     * @return JsonResponse
     */
    public function list(Request $request, UserLogService $userLogService): JsonResponse
    {
        $rules = [
            'keyword' => 'sometimes|nullable',
            'date' => 'sometimes|nullable|array'
        ];
        $data = $request->validate($rules);
        $result = $userLogService->getList($data);
        return $this->success($result);
    }

}
