<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;
use Illuminate\Http\JsonResponse;
use App\Services\Backend\UserLogService;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;


    /**
     * 返回正常数据
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param array|string|null|bool|Collection $data 数据
     * @param string $message 说明消息
     * @return JsonResponse
     */
    public function success(array|string|null|bool|Collection $data = null, $message = 'success'): JsonResponse
    {
        return response()->json([
            'code' => 200,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * 返回失败数据
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param string $message
     * @param integer $code
     * @param array|string|null|bool|Collection $data
     * @return JsonResponse
     */
    public function error(string $message = 'error', $code = 500, array|string|null|bool|Collection $data = null): JsonResponse
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data
        ]);
    }
}
