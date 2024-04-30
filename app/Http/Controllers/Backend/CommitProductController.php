<?php

namespace App\Http\Controllers\Backend;

use App\Services\Backend\PartService;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Backend\DictService;
use Inertia\Response as InertiaResponse;
use Illuminate\Support\Facades\Validator;
use App\Services\Backend\CommitProductService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * 产品考核-考核定义控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class CommitProductController extends Controller
{
    /**
     * 考核定义-产品考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request           $request
     * @param  DictService       $dictService
     * @return InertiaResponse
     */
    public function index(Request $request, DictService $dictService): InertiaResponse
    {
        return Inertia::render('Examine/Product/Index', [
            'parts' => (new PartService)->getOption(),
            'examine_product_type' => $dictService->getOptionByCode('examine_product_type'),
            'examine_product_item_type' => $dictService->getOptionByCode('examine_product_item_type'),
            'template_status' => $dictService->getOptionByCode('template_status'),
            'engine_type' => $dictService->getOptionByCode('engine_type'),
        ]);
    }

    /**
     * 产品考核-考核定义列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request       $request
     * @param  CommitProductService $commitProductService
     * @return JsonResponse
     */
    public function list(Request $request, CommitProductService $commitProductService): JsonResponse
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
        $result = $commitProductService->getList($request->user(), $data);
        return $this->success($result);
    }

    /**
     * 产品考核-考核定义选项列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @param  CommitProductService $commitProductService
     * @return JsonResponse
     */
    public function option(Request $request, CommitProductService $commitProductService): JsonResponse
    {
        $rules = [
            'engine' => 'sometimes|nullable|integer'
        ];
        $messages = [
            'engine.integer' => '机型不正确'
        ];
        $data = $request->validate($rules, $messages);
        $result = $commitProductService->getOption($data);
        return $this->success($result);
    }

    /**
     * 添加产品考核-考核定义
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @param  CommitProductService $commitProductService
     * @return JsonResponse
     */
    public function create(Request $request, CommitProductService $commitProductService): JsonResponse
    {
        $rules = [
            'name' => 'required|max:100',
            'engine' => 'required|integer',
            'parent_id' => 'sometimes|nullable|exists_or_null:commit_products,id',
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
        $commitProductService->createByDepartment($request->user(), $data);
        return $this->success();
    }

    /**
     * 更新产品考核-考核定义
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string         $id
     * @param  Request         $request
     * @param  CommitProductService $commitProductService
     * @return JsonResponse
     */
    public function update(string $id, Request $request, CommitProductService $commitProductService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commit_products,id,status,0',
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
        $commitProductService->update($id, $data);
        return $this->success();
    }

    /**
     * 产品考核-考核定义详情
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string         $id
     * @param  Request         $request
     * @param  CommitProductService $commitProductService
     * @return JsonResponse
     */
    public function detail(string $id, Request $request, CommitProductService $commitProductService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commit_products,id',
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
        $result = $commitProductService->detail($id);
        return $this->success($result);
    }


    /**
     * 获取产品考核-考核定义变更项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string         $id
     * @param  Request         $request
     * @param  CommitProductService $commitProductService
     * @return JsonResponse
     */
    public function changed(string $id, Request $request, CommitProductService $commitProductService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commit_products,id',
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
        $result = $commitProductService->getChanged($id);
        return $this->success($result);
    }

    /**
     * 删除考核模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string         $id
     * @param  Request         $request
     * @param  CommitProductService $commitProductService
     * @return JsonResponse
     */
    public function delete(string $id, Request $request, CommitProductService $commitProductService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commit_products,id,status,0',
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
        $commitProductService->delete($id);
        return $this->success();
    }

    /**
     * 下载产品考核-考核定义模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  CommitProductService $commitProductService
     * @return BinaryFileResponse
     */
    public function template(CommitProductService $commitProductService): BinaryFileResponse
    {
        return $commitProductService->downloadImportTemplate();
    }

    /**
     * 导入产品考核-考核定义模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request              $request
     * @param  CommitProductService $service
     * @return JsonResponse
     */
    public function import(Request $request, CommitProductService $service): JsonResponse
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
