<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class FileController extends Controller
{
    /**
     * 知识库列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  FileService  $service
     * @return JsonResponse
     */
    public function index(Request $request, FileService $service): JsonResponse
    {
        $rules = [
            'path' => 'required',
            'hidden' => 'sometimes|nullable|boolean'
        ];
        $messages = [
            'path.required' => '目录参数错误',
            'hidden.boolean' => '参数错误'
        ];
        $data = $request->validate($rules, $messages);
        return $this->success($service->getList($data));
    }


    /**
     * 预览文件
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string      $id
     * @param  Request     $request
     * @param  FileService $service
     * @return JsonResponse
     */
    public function view(string $id, Request $request, FileService $service): JsonResponse
    {
        $rules = [
            'path' => 'required',
        ];
        $messages = [
            'path.required' => '文件路径不能为空',
        ];
        $data = $request->validate($rules, $messages);
        $result = $service->viewer($id, $data['path']);
        return $this->success($result);
    }

}
