<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\FileService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class FileController extends Controller
{
    /**
     * 知识库视图
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function index(Request $request): InertiaResponse
    {
        return Inertia::render('File/Index', []);
    }

    /**
     * 知识库列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  FileService  $service
     * @return JsonResponse
     */
    public function list(Request $request, FileService $service): JsonResponse
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
     * 添加文件(夹)
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  FileService  $service
     * @return JsonResponse
     */
    public function create(Request $request, FileService $service): JsonResponse
    {
        $rules = [
            'path' => 'required',
        ];
        $messages = [
            'path.required' => '文件路径不能为空'
        ];
        $data = $request->validate($rules, $messages);
        $service->create($data['path']);
        return $this->success();
    }

    /**
     * 修改文件(夹)
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $id
     * @param  Request      $request
     * @param  FileService  $service
     * @return JsonResponse
     */
    public function update(string $id, Request $request, FileService $service): JsonResponse
    {
        $rules = [
            'name' => 'required_without:visit|max:100',
            'visit' => 'required_without:name|boolean',
            'path' => 'required',
        ];
        $messages = [
            'path.required' => '文件路径不能为空',
            'visit.required_without' => '状态不能为空',
            'name.required_without' => '文件名不能为空',
            'name.max' => '文件名称不能超过100个字符'
        ];
        $data = $request->validate($rules, $messages);
        if (array_key_exists('name', $data)) {
            $service->rename($id, (string) $data['path'], (string) $data['name']);
        }
        if (array_key_exists('visit', $data)) {
            $service->visit($id, (string) $data['path'], (string) $data['visit']);
        }
        return $this->success();
    }

    /**
     * 删除文件(夹)
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $id
     * @param  Request      $request
     * @param  FileService  $service
     * @return JsonResponse
     */
    public function delete(string $id, Request $request, FileService $service): JsonResponse
    {
        $rules = [
            'path' => 'required',
        ];
        $messages = [
            'path.required' => '文件路径不能为空',
        ];
        $data = $request->validate($rules, $messages);
        $service->remove($id, (string) $data['path']);
        return $this->success();
    }

    /**
     * 批量删除文件(夹)
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  FileService  $service
     * @return JsonResponse
     */
    public function batchDelete(Request $request, FileService $service): JsonResponse
    {
        $rules = [
            'path' => 'required',
            'ids' => 'required|array|min:1'
        ];
        $messages = [
            'path.required' => '文件路径不能为空',
            'ids.required' => '请选择一个文件(夹)',
            'ids.array' => '参数错误',
            'ids.min' => '请选择一个文件(夹)',
        ];
        $data = $request->validate($rules, $messages);
        $service->batchRemove($data);
        return $this->success();
    }

    /**
     * 批量移动文件(夹)
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  FileService  $service
     * @return JsonResponse
     */
    public function batchMove(Request $request, FileService $service): JsonResponse
    {
        $rules = [
            'path' => 'required',
            'target' => 'required',
            'ids' => 'required|array|min:1'
        ];
        $messages = [
            'path.required' => '文件路径不能为空',
            'target.required' => '目标文件夹不能为空',
            'ids.required' => '请选择一个文件(夹)',
            'ids.array' => '参数错误',
            'ids.min' => '请选择一个文件(夹)',
        ];
        $data = $request->validate($rules, $messages);
        $service->batchMove($data);
        return $this->success();
    }

    /**
     * 预览文件
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string      $id
     * @param  Request     $request
     * @param  FileService $service
     * @return Response
     */
    public function view(string $id, Request $request, FileService $service):Response
    {
        $rules = [
            'path' => 'required',
        ];
        $messages = [
            'path.required' => '文件路径不能为空',
        ];
        $data = $request->validate($rules, $messages);
        return $service->viewer($id, $data['path']);
    }

    /**
     * 下载文件
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string      $id
     * @param  Request     $request
     * @param  FileService $service
     * @return Response
     */
    public function download(string $id, Request $request, FileService $service):Response
    {
        $rules = [
            'path' => 'required',
        ];
        $messages = [
            'path.required' => '文件路径不能为空'
        ];
        $data = $request->validate($rules, $messages);
        return $service->download($id, $data['path']);
    }

    /**
     * 上传文件
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  FileService  $service
     * @return JsonResponse
     */
    public function upload(Request $request, FileService $service): JsonResponse
    {
        $rules = [
            'path' => 'required',
            'file' => 'required|file',
        ];
        $messages = [
            'path.required' => '文件路径不能为空',
            'file.required' => '上传文件不能为空',
            'file.file' => '上传文件错误'
        ];
        $data = $request->validate($rules, $messages);
        $service->upload($data['path'], $data['file']);
        return $this->success();
    }
}
