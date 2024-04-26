<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\UploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    /**
     * wangEditor上传图片
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request       $request
     * @param  UploadService $uploadService
     * @return JsonResponse
     */
    public function image(Request $request,UploadService $uploadService): JsonResponse
    {
        $data = $request->validate([
            'file' => 'required|image'
        ],[
            'file.required' => '文件不能为空',
            'file.image' => '图片文件不合规'
        ]);
        $result = $uploadService->image($data['file']);
        return response()->json([
            'errno' => 0,
            'data' => $result
        ]);
    }

    /**
     * wangEditor上传视频
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request       $request
     * @param  UploadService $uploadService
     * @return JsonResponse
     */
    public function video(Request $request,UploadService $uploadService): JsonResponse
    {
        $data = $request->validate([
            'file' => 'required|file|mimetypes:video/avi,video/mpeg,video/mp4,video/quicktime,video/h264'
        ],[
            'file.required' => '文件不能为空',
            'file.file' => '文件上传失败',
            'file.mimetypes' => '视频文件不合规'
        ]);
        $result = $uploadService->video($data['file']);
        return response()->json([
            'errno' => 0,
            'data' => $result
        ]);
    }

    /**
     * wangEditor上传附件
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request       $request
     * @param  UploadService $uploadService
     * @return JsonResponse
     */
    public function file(Request $request,UploadService $uploadService): JsonResponse
    {
        $data = $request->validate([
            'file' => 'required|file'
        ],[
            'file.required' => '文件不能为空',
            'file.file' => '文件上传失败'
        ]);
        $result = $uploadService->file($data['file']);
        return response()->json([
            'errno' => 0,
            'data' => $result
        ]);
    }
}
