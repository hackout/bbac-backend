<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\CommitItemService;
use App\Services\Backend\CommitService;
use App\Services\Backend\DictService;
use App\Services\Backend\ExamineService;
use App\Services\Backend\TorqueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;

/**
 * 考核历史版本控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class CommitController extends Controller
{
    /**
     * 在线考核定义
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request           $request
     * @param  DictService       $dictService
     * @param  CommitService     $commitService
     * @param  CommitItemService $commitItemService
     * @return InertiaResponse
     */
    public function inline(Request $request, DictService $dictService, CommitService $commitService, CommitItemService $commitItemService): InertiaResponse
    {
        return Inertia::render('Examine/Inline', [
            'sub_type_allow' => $commitService->getInlineOptions(),
            'standard_type' => $commitItemService->getStandardOption(),
            'gluing_type' => $commitItemService->getGluingOption(),
            'dynamic_type' => $commitItemService->getDynamicOption(),
            'sub_type' => $dictService->getOptionByCode('sub_type'),
            'special' => $dictService->getOptionByCode('special'),
            'bolt_model' => $dictService->getOptionByCode('bolt_model'),
            'bolt_type' => $dictService->getOptionByCode('bolt_type'),
            'bolt_status' => $dictService->getOptionByCode('bolt_status'),
            'template_status' => $dictService->getOptionByCode('template_status'),
            'examine_item_type' => $dictService->getOptionByCode('examine_item_type'),
            'engine_type' => $dictService->getOptionByCode('engine_type')
        ]);
    }

    /**
     * 产品考核-考核定义
     */
    public function product(Request $request, DictService $dictService, CommitService $commitService, CommitItemService $commitItemService): InertiaResponse
    {
        return Inertia::render('Examine/Product', [
            'sub_type_allow' => $commitService->getProductOptions(),
            'other_type' => $commitItemService->getOtherOption(),
            'sub_type' => $dictService->getOptionByCode('sub_type'),
            'special' => $dictService->getOptionByCode('special'),
            'template_status' => $dictService->getOptionByCode('template_status'),
            'examine_item_type' => $dictService->getOptionByCode('examine_item_type'),
            'engine_type' => $dictService->getOptionByCode('engine_type'),
            'torque' => (new TorqueService)->getOptions()
        ]);
    }

    /**
     * 整车考核-考核定义
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request           $request
     * @param  DictService       $dictService
     * @param  CommitService     $commitService
     * @param  CommitItemService $commitItemService
     * @return InertiaResponse
     */
    public function service(Request $request, DictService $dictService, CommitService $commitService, CommitItemService $commitItemService): InertiaResponse
    {
        return Inertia::render('Examine/Service', [
            'sub_type_allow' => $commitService->getServiceOptions(),
            'other_type' => $commitItemService->getOtherOption(),
            'sub_type' => $dictService->getOptionByCode('sub_type'),
            'special' => $dictService->getOptionByCode('special'),
            'template_status' => $dictService->getOptionByCode('template_status'),
            'examine_item_type' => $dictService->getOptionByCode('examine_item_type'),
            'engine_type' => $dictService->getOptionByCode('engine_type'),
            'torque' => (new TorqueService)->getOptions()
        ]);
    }

    /**
     * 获取考核定义-历史版本列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @param  CommitService $commitService
     * @return JsonResponse
     */
    public function option(Request $request, CommitService $commitService): JsonResponse
    {
        $rules = [
            'engine' => 'sometimes|nullable|integer',
            'type' => 'required|in:inline,product,service',
            'sub_type' => 'sometimes|nullable|integer'
        ];
        $messages = [
            'engine.integer' => '机型不正确',
            'sub_type.integer' => '考核类型不正确',
        ];
        $data = $request->validate($rules, $messages);
        $result = $commitService->getOption($data);
        return $this->success($result);
    }

    /**
     * 考核定义列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string        $type
     * @param  Request       $request
     * @param  CommitService $commitService
     * @return JsonResponse
     */
    public function list(string $type, Request $request, CommitService $commitService): JsonResponse
    {
        $rules = [
            'sub_type' => 'sometimes|nullable|integer',
            'keyword' => 'sometimes|nullable',
            'status' => 'sometimes|nullable|integer',
        ];
        $messages = [
            'sub_type.integer' => '考核类型不正确',
            'status.integer' => '审核状态不正确',
        ];
        $data = request()->validate($rules, $messages);
        $result = $commitService->getList($type, $data);
        return $this->success($result);
    }

    /**
     * 添加考核模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string          $type
     * @param  Request         $request
     * @param  CommitService $commitService
     * @return JsonResponse
     */
    public function create(string $type, Request $request, CommitService $commitService): JsonResponse
    {
        $rules = [
            'name' => 'required|max:100',
            'engine' => 'required|integer',
            'parent_id' => 'sometimes|nullable|exists_or_null:commits,id',
            'version' => 'required|max:36',
            'description' => 'sometimes|nullable|max:200',
            'period' => 'required|integer',
            'sub_type' => 'required|integer'
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
            'sub_type.required' => '考核类型不能为空',
            'sub_type.integer' => '考核类型不正确',
        ];
        $data = $request->validate($rules, $messages);
        $commitService->createByType($request->user(), $type, $data);
        return $this->success();
    }

    /**
     * 更新考核模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string         $id
     * @param  Request         $request
     * @param  CommitService $commitService
     * @return JsonResponse
     */
    public function update(string $id, Request $request, CommitService $commitService): JsonResponse
    {
        $rules = [
            'id' => 'exists_plus:commits,id,0,status',
            'name' => 'required|max:100',
            'engine' => 'required|integer',
            'version' => 'required|max:36',
            'description' => 'sometimes|nullable|max:200',
            'period' => 'required|integer',
            'sub_type' => 'required|integer'
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
            'sub_type.required' => '考核类型不能为空',
            'sub_type.integer' => '考核类型不正确',
            'id.exists_plus' => '当前模板无法编辑',
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
            'period',
            'sub_type',
        ]);
        $commitService->update($id, $data);
        return $this->success();
    }

    /**
     * 更新考核模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string         $id
     * @param  Request         $request
     * @param  CommitService $commitService
     * @return JsonResponse
     */
    public function detail(string $id, Request $request, CommitService $commitService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commits,id',
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
        $result = $commitService->detail($id);
        return $this->success($result);
    }

    
    /**
     * 获取模板变更项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string         $id
     * @param  Request         $request
     * @param  CommitService $commitService
     * @return JsonResponse
     */
    public function changed(string $id, Request $request, CommitService $commitService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commits,id',
        ];
        $messages = [
            'id.exists_plus' => '模板不存在',
        ];
        $validator = Validator::make([
            'id' => $id
        ], $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $result = $commitService->getChanged($id);
        return $this->success($result);
    }
    
    /**
     * 删除考核模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string         $id
     * @param  Request         $request
     * @param  CommitService $commitService
     * @return JsonResponse
     */
    public function delete(string $id, Request $request, CommitService $commitService): JsonResponse
    {
        $rules = [
            'id' => 'exists_plus:commits,id,0,status',
        ];
        $messages = [
            'id.exists_plus' => '当前模板无法删除',
        ];
        $validator = Validator::make([
            'id' => $id
        ], $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $commitService->delete($id);
        return $this->success();
    }

    /**
     * 获取导入模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string             $type
     * @param  CommitService    $commitService
     * @return BinaryFileResponse
     */
    public function template(string $type, CommitService $commitService): BinaryFileResponse
    {
        return $commitService->getTemplateByType($type);
    }

    

    /**
     * 导入考核定义
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string          $type
     * @param  Request         $request
     * @param  CommitService $service
     * @return JsonResponse
     */
    public function import(string $type, Request $request, CommitService $service): JsonResponse
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
        $service->importByType($type, $data['file']);
        return $this->success();
    }
}
