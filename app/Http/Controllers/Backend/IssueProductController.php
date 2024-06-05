<?php

namespace App\Http\Controllers\Backend;

use App\Services\Backend\TaskService;
use App\Services\Backend\UserService;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Backend\DictService;
use App\Services\Backend\ExamineService;
use Inertia\Response as InertiaResponse;
use App\Services\Backend\IssueProductService;
use App\Services\Backend\IssueVehicleLogService;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * 产品考核问题追踪-控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class IssueProductController extends Controller
{
    /**
     * 获取整车服务-问题列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request             $request
     * @param  IssueProductService $service
     * @return JsonResponse
     */
    public function list(Request $request, IssueProductService $service): JsonResponse
    {
        $rules = [
            'user_id' => 'sometimes|nullable|exists_or_null:users,id',
            'status' => 'sometimes|nullable|integer',
            'date' => 'sometimes|nullable|array',
            'keyword' => 'sometimes|nullable'
        ];
        $messages = [
            'user_id.exists_or_null' => '考核员不存在或已删除',
            'status.integer' => '状态不正确',
            'date.array' => '日期不正确',
        ];
        $data = $request->validate($rules, $messages);
        $result = $service->getList($request->user(), $data);
        return $this->success($result);
    }


    /**
     * 产品考核-删除考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string          $id
     * @param  Request         $request
     * @param  IssueProductService     $service
     * @return JsonResponse
     */
    public function delete(string $id, Request $request, IssueProductService $service): JsonResponse
    {
        $rules = [
            'id' => 'exists:issue_products,id',
        ];
        $messages = [
            'id.exists' => '问题记录不存在或已删除',
        ];

        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->all()), $rules, $messages);
        if ($validator->fails()) {
            return abort(500)->withMessages($validator->errors());
        }
        $result = $service->deleteIssue($request->user(), $id);
        return $this->success($result);
    }

    /**
     * 产品考核-维护记录
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string          $id
     * @param  Request         $request
     * @param  IssueProductService     $service
     * @return JsonResponse
     */
    public function update(string $id, Request $request, IssueProductService $service): JsonResponse
    {
        $rules = [
            'id' => 'exists:issue_products,id',
            'defect_description' => 'required|integer',
            'defect_level' => 'required|integer',
            'defect_part' => 'required|integer',
            'defect_position' => 'required|integer',
            'defect_cause' => 'required|integer',
            'soma' => 'sometimes|nullable',
            'lama' => 'sometimes|nullable',
            'note' => 'sometimes|nullable',
            'eight_disciplines' => 'sometimes|nullable',
            'status' => 'required|integer',
            'file' => 'sometimes|nullable|file'
        ];
        $messages = [
            'id.exists' => '考核记录不存在',
            'defect_level.required' => '缺陷等级不能为空',
            'defect_level.integer' => '缺陷等级不存在',
            'defect_part.required' => '问题零件不能为空',
            'defect_part.integer' => '问题零件不存在',
            'defect_description.required' => '问题描述不能为空',
            'defect_description.integer' => '问题描述不存在',
            'defect_position.required' => '问题位置不能为空',
            'defect_position.integer' => '问题位置不存在',
            'defect_cause.required' => '具体位置不能为空',
            'defect_cause.integer' => '具体位置不存在',
            'status.required' => '问题状态不能为空',
            'status.integer' => '问题状态不存在',
            'file.file' => '维护记录错误',
        ];

        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->all()), $rules, $messages);
        if ($validator->fails()) {
            return abort(500)->withMessages($validator->errors());
        }
        $data = $validator->safe()->only([
            'defect_description',
            'defect_level',
            'defect_part',
            'defect_position',
            'defect_cause',
            'soma',
            'lama',
            'note',
            'eight_disciplines',
            'status',
            'file',
        ]);
        $service->updateIssue($request->user(), $id, $data);
        return $this->success();
    }
}