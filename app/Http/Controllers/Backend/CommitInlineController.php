<?php

namespace App\Http\Controllers\Backend;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Backend\DictService;
use Inertia\Response as InertiaResponse;
use Illuminate\Support\Facades\Validator;
use App\Services\Backend\CommitInlineService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * 在线考核-考核定义控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class CommitInlineController extends Controller
{
    /**
     * 考核定义-在线考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request           $request
     * @param  DictService       $dictService
     * @return InertiaResponse
     */
    public function index(Request $request, DictService $dictService): InertiaResponse
    {
        return Inertia::render('Examine/Inline/Index', [
            'special' => $dictService->getOptionByCode('special'),
            'bolt_status' => $dictService->getOptionByCode('bolt_status'),
            'bolt_model' => $dictService->getOptionByCode('bolt_model'),
            'bolt_type' => $dictService->getOptionByCode('bolt_type'),
            'examine_inline_type' => $dictService->getOptionByCode('examine_inline_type'),
            'examine_inline_item_type' => $dictService->getOptionByCode('examine_inline_item_type'),
            'template_status' => $dictService->getOptionByCode('template_status'),
            'engine_type' => $dictService->getOptionByCode('engine_type'),
        ]);
    }

    /**
     * 在线考核-考核定义列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request       $request
     * @param  CommitInlineService $commitInlineService
     * @return JsonResponse
     */
    public function list(Request $request, CommitInlineService $commitInlineService): JsonResponse
    {
        $rules = [
            'engine' => 'sometimes|nullable|integer',
            'status' => 'sometimes|nullable|integer',
            'type' => 'sometimes|nullable|integer',
            'keyword' => 'sometimes|nullable'
        ];
        $messages = [
            'engine.integer' => '考核机型不正确',
            'status.integer' => '审核状态不正确',
            'type.integer' => '考核类型不正确',
        ];
        $data = request()->validate($rules, $messages);
        $result = $commitInlineService->getList($request->user(), $data);
        return $this->success($result);
    }

    /**
     * 在线考核-考核定义选项列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @param  CommitInlineService $commitInlineService
     * @return JsonResponse
     */
    public function option(Request $request, CommitInlineService $commitInlineService): JsonResponse
    {
        $rules = [
            'engine' => 'sometimes|nullable|integer'
        ];
        $messages = [
            'engine.integer' => '机型不正确'
        ];
        $data = $request->validate($rules, $messages);
        $result = $commitInlineService->getOption($data);
        return $this->success($result);
    }

    /**
     * 添加在线考核-考核定义
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @param  CommitInlineService $commitInlineService
     * @return JsonResponse
     */
    public function create(Request $request, CommitInlineService $commitInlineService): JsonResponse
    {
        $rules = [
            'name' => 'required|max:100',
            'engine' => 'required|integer',
            'parent_id' => 'sometimes|nullable|exists_or_null:commit_inlines,id',
            'version' => 'required|max:36',
            'description' => 'sometimes|nullable|max:200',
            'period' => 'required|integer',
            'type' => 'required|integer',
        ];
        $messages = [
            'name.required' => '模板标题不能为空',
            'name.max' => '模板标题最大支持100个字符',
            'engine.required' => '发动机机型不能为空',
            'engine.integer' => '发动机机型不正确',
            'parent_id.exists_or_null' => '上一个版本不正确',
            'version.required' => '版本号不能为空',
            'version.max' => '版本号最大支持36个字符',
            'description.max' => '备注说明最大支持200个字符',
            'period.required' => '标准工时不能为空',
            'period.integer' => '标准工时不正确',
            'type.required' => '考核类型不能为空',
            'type.integer' => '考核类型不正确'
        ];
        $data = $request->validate($rules, $messages);
        $commitInlineService->createByDepartment($request->user(), $data);
        return $this->success();
    }

    /**
     * 更新在线考核-考核定义
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string         $id
     * @param  Request         $request
     * @param  CommitInlineService $commitInlineService
     * @return JsonResponse
     */
    public function update(string $id, Request $request, CommitInlineService $commitInlineService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commit_inlines,id,status,0',
            'name' => 'required|max:100',
            'engine' => 'required|integer',
            'version' => 'required|max:36',
            'description' => 'sometimes|nullable|max:200',
            'period' => 'required|integer'
        ];
        $messages = [
            'name.required' => '模板标题不能为空',
            'name.max' => '模板标题最大支持100个字符',
            'engine.required' => '发动机机型不能为空',
            'engine.integer' => '发动机机型不正确',
            'version.required' => '版本号不能为空',
            'version.max' => '版本号最大支持36个字符',
            'description.max' => '备注说明最大支持200个字符',
            'period.required' => '标准工时不能为空',
            'period.integer' => '标准工时不正确',
            'id.exists' => '当前模板无法编辑',
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'name',
            'engine',
            'version',
            'description',
            'period'
        ]);
        $commitInlineService->update($id, $data);
        return $this->success();
    }

    /**
     * 在线考核-考核定义详情
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string         $id
     * @param  Request         $request
     * @param  CommitInlineService $commitInlineService
     * @return JsonResponse
     */
    public function detail(string $id, Request $request, CommitInlineService $commitInlineService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commit_inlines,id',
        ];
        $messages = [
            'id.exists' => '模板不存在',
        ];
        $validator = Validator::make([
            'id' => $id
        ], $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $result = $commitInlineService->detail($id);
        return $this->success($result);
    }


    /**
     * 获取在线考核-考核定义变更项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string         $id
     * @param  Request         $request
     * @param  CommitInlineService $commitInlineService
     * @return JsonResponse
     */
    public function changed(string $id, Request $request, CommitInlineService $commitInlineService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commit_inlines,id',
        ];
        $messages = [
            'id.exists' => '模板不存在',
        ];
        $validator = Validator::make([
            'id' => $id
        ], $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $result = $commitInlineService->getChanged($id);
        return $this->success($result);
    }

    /**
     * 删除考核模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string         $id
     * @param  Request         $request
     * @param  CommitInlineService $commitInlineService
     * @return JsonResponse
     */
    public function delete(string $id, Request $request, CommitInlineService $commitInlineService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commit_inlines,id,status,0',
        ];
        $messages = [
            'id.exists' => '当前模板无法删除',
        ];
        $validator = Validator::make([
            'id' => $id
        ], $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $commitInlineService->delete($id);
        return $this->success();
    }

    /**
     * 下载在线考核-考核定义模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  CommitInlineService $commitInlineService
     * @return BinaryFileResponse
     */
    public function template(CommitInlineService $commitInlineService): BinaryFileResponse
    {
        return $commitInlineService->downloadImportTemplate();
    }

    /**
     * 导入在线考核-考核定义模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request              $request
     * @param  CommitInlineService $service
     * @return JsonResponse
     */
    public function import(Request $request, CommitInlineService $service): JsonResponse
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
        $service->importFile($request->user(), $data['file']);
        return $this->success();
    }
}
