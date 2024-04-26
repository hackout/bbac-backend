<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\LocalePackageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * 语言包控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class LocalePackageController extends Controller
{

    /**
     * 语言包管理
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function index(Request $request): InertiaResponse
    {
        return Inertia::render('Language/Index');
    }

    /**
     * 查询语言包
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  LocalePackageService  $localePackageService
     * @return JsonResponse
     */
    public function list(Request $request, LocalePackageService $localePackageService): JsonResponse
    {
        $rules = [
            'keyword' => 'sometimes|nullable'
        ];
        $data = $request->validate($rules);
        $result = $localePackageService->getList($data);
        return $this->success($result);
    }


    /**
     * 新建语言包
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  LocalePackageService  $localePackageService
     * @return JsonResponse
     */
    public function create(Request $request, LocalePackageService $localePackageService): JsonResponse
    {
        $rules = [
            'code' => 'required|unique:locale_packages,code',
            'content_zh' => 'sometimes|nullable',
            'content_en' => 'sometimes|nullable',
        ];
        $messages = [
            "code.required" => "语言标识不能为空",
            "code.unique" => "语言标识已存在",
        ];
        $data = $request->validate($rules, $messages);
        $localePackageService->create($data);
        return $this->success();
    }

    /**
     * 更新语言包
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  int       $id
     * @param  Request      $request
     * @param  LocalePackageService  $localePackageService
     * @return JsonResponse
     */
    public function update(int $id, Request $request, LocalePackageService $localePackageService): JsonResponse
    {
        $rules = [
            'id' => 'exists:locale_packages,id',
            'code' => 'required|unique:locale_packages,code,' . $id,
            'content_zh' => 'sometimes|nullable',
            'content_en' => 'sometimes|nullable',
        ];
        $messages = [
            "code.required" => "语言标识不能为空",
            "code.unique" => "语言标识已存在",
            "id.exists" => "语言项不存在"
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'code',
            'content_zh',
            'content_en'
        ]);
        $localePackageService->update($id, $data);
        return $this->success();
    }



    /**
     * 下载导入模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  LocalePackageService  $localePackageService
     * @return BinaryFileResponse
     */
    public function template(LocalePackageService $localePackageService): BinaryFileResponse
    {
        return $localePackageService->downloadImportTemplate();
    }

    /**
     * 导入语言包
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  LocalePackageService  $localePackageService
     * @return JsonResponse
     */
    public function import(Request $request, LocalePackageService $localePackageService): JsonResponse
    {
        $rules = [
            'file' => 'required|file|mimes:xls,xlsx',
        ];
        $messages = [
            'file.required' => '上传文件不能为空',
            'file.file' => '上传文件错误',
            'file.mimes' => '上传文件格式错误'
        ];
        $data = $request->validate($rules, $messages);
        $localePackageService->import($data['file']);
        return $this->success();
    }

    /**
     * 导出语言包
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  LocalePackageService  $localePackageService
     * @return JsonResponse
     */
    public function export(Request $request, LocalePackageService $localePackageService): JsonResponse
    {
        $rules = [
            'keyword' => 'sometimes|nullable'
        ];
        $data = $request->validate($rules);
        $result = $localePackageService->export($data);
        return $this->success($result);
    }

}
