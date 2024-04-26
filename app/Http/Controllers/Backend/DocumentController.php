<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\AssemblyService;
use App\Services\Backend\DictService;
use App\Services\Backend\DocumentLogService;
use App\Services\Backend\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * 指导书控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class DocumentController extends Controller
{

    /**
     * 拆检指导书
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function overhaul(Request $request, DictService $dictService): InertiaResponse
    {
        return Inertia::render('Document/Overhaul', [
            'engine_type' => $dictService->getOptionByCode('engine_type'),
        ]);
    }

    /**
     * 装配指导书
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function assembling(Request $request, DictService $dictService): InertiaResponse
    {
        return Inertia::render('Document/Assembling', [
            'engine_type' => $dictService->getOptionByCode('engine_type'),
        ]);
    }

    /**
     * 扭矩清单
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function torque(Request $request, DictService $dictService): InertiaResponse
    {
        return Inertia::render('Document/Torque', [
            'engine_type' => $dictService->getOptionByCode('engine_type'),
        ]);
    }

    /**
     * 获取指导书列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  integer         $type
     * @param  Request         $request
     * @param  DocumentService $documentService
     * @return JsonResponse
     */
    public function list(int $type, Request $request, DocumentService $documentService): JsonResponse
    {
        $result = $documentService->getList($type, $request->user());
        return $this->success($result);
    }

    /**
     * 更新拆检指导书
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string            $engine
     * @param  Request           $request
     * @param  DocumentLogService $documentService
     * @return JsonResponse
     */
    public function overhaulUpdate(string $engine, Request $request, DocumentLogService $documentService): JsonResponse
    {
        $data = $request->validate([
            'file' => 'required|file'
        ], [
            'file.required' => '文件不能为空',
            'file.file' => '文件上传失败'
        ]);
        $result = $documentService->overhaulUpdate($engine, $request->user(), $data['file']);
        return response()->json([
            'errno' => 0,
            'data' => $result
        ]);
    }

    /**
     * 更新装配指导书
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string            $engine
     * @param  Request           $request
     * @param  DocumentLogService $documentService
     * @return JsonResponse
     */
    public function assemblingUpdate(string $engine, Request $request, DocumentLogService $documentService): JsonResponse
    {
        $data = $request->validate([
            'file' => 'required|file'
        ], [
            'file.required' => '文件不能为空',
            'file.file' => '文件上传失败'
        ]);
        $result = $documentService->assemblingUpdate($engine, $request->user(), $data['file']);
        return response()->json([
            'errno' => 0,
            'data' => $result
        ]);
    }

    /**
     * 更新扭矩清单
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string            $engine
     * @param  Request           $request
     * @param  DocumentLogService $documentService
     * @return JsonResponse
     */
    public function torqueUpdate(string $engine, Request $request, DocumentLogService $documentService): JsonResponse
    {
        $data = $request->validate([
            'file' => 'required|file'
        ], [
            'file.required' => '文件不能为空',
            'file.file' => '文件上传失败'
        ]);
        $result = $documentService->torqueUpdate($engine, $request->user(), $data['file']);
        return response()->json([
            'errno' => 0,
            'data' => $result
        ]);
    }

    /**
     * 删除指导书
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string          $id
     * @param  Request         $request
     * @param  DocumentService $documentService
     * @return JsonResponse
     */
    public function delete(string $id, Request $request, DocumentService $documentService): JsonResponse
    {
        $rules = [
            'id' => 'exists:documents,id'
        ];
        $messages = [
            'id.exists' => '指导书不存在',
        ];
        $validator = Validator::make([
            'id' => $id
        ], $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $documentService->deleteFile($id, $request->user());
        return $this->success();
    }

}
