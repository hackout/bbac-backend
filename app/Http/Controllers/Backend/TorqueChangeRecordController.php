<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\DictService;
use App\Services\Backend\TorqueChangeRecordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * 扭矩数据库变更记录控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class TorqueChangeRecordController extends Controller
{

    /**
     * 扭矩数据库变更记录
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string  $id
     * @param  Request           $request
     * @param  TorqueChangeRecordService $torqueChangeRecordService
     * @return JsonResponse
     */
    public function list(string $id,Request $request, TorqueChangeRecordService $torqueChangeRecordService): JsonResponse
    {
        $rules = [
            'id' => 'exists:torques,id',
        ];
        $messages = [
            'id.exists' => '扭矩数据库不正确',
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->input()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $result = $torqueChangeRecordService->getListById($id);
        return $this->success($result);
    }

}
