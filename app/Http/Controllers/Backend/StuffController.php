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
 * 产品考核-控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class StuffController extends Controller
{

    /**
     * 产品考核-拆检考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function index(Request $request, DictService $dictService): InertiaResponse
    {
        return Inertia::render('Stuff/Index', [
            'defect_level' => $dictService->getOptionByCode('defect_level'),
            'defect_category' => $dictService->getOptionByCode('defect_category'),
            'task_status' => $dictService->getOptionByCode('task_status'),
            'engine_type' => $dictService->getOptionByCode('engine_type'),
            'examine_product_item_type' => $dictService->getOptionByCode('examine_product_item_type'),
            'examine_type' => $dictService->getOptionByCode('examine_type'),
            'status' => $dictService->getOptionByCode('assembly_status'),
            'plant' => $dictService->getOptionByCode('plant'),
            'line' => $dictService->getOptionByCode('assembly_line'),
            'users' => (new UserService())->getUserByProduct($request->user())
        ]);
    }

    /**
     * 产品考核-装配考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function engine(Request $request, DictService $dictService): InertiaResponse
    {
        return Inertia::render('Stuff/Engine', [
            'defect_level' => $dictService->getOptionByCode('defect_level'),
            'defect_category' => $dictService->getOptionByCode('defect_category'),
            'task_status' => $dictService->getOptionByCode('task_status'),
            'engine_type' => $dictService->getOptionByCode('engine_type'),
            'examine_product_item_type' => $dictService->getOptionByCode('examine_product_item_type'),
            'examine_type' => $dictService->getOptionByCode('examine_type'),
            'status' => $dictService->getOptionByCode('assembly_status'),
            'plant' => $dictService->getOptionByCode('plant'),
            'line' => $dictService->getOptionByCode('assembly_line'),
            'users' => (new UserService())->getUserByProduct($request->user())
        ]);
    }

    /**
     * 产品考核-动态考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function dynamic(Request $request, DictService $dictService): InertiaResponse
    {
        return Inertia::render('Stuff/Dynamic', [
            'defect_level' => $dictService->getOptionByCode('defect_level'),
            'defect_category' => $dictService->getOptionByCode('defect_category'),
            'task_status' => $dictService->getOptionByCode('task_status'),
            'engine_type' => $dictService->getOptionByCode('engine_type'),
            'examine_product_item_type' => $dictService->getOptionByCode('examine_product_item_type'),
            'examine_type' => $dictService->getOptionByCode('examine_type'),
            'status' => $dictService->getOptionByCode('assembly_status'),
            'plant' => $dictService->getOptionByCode('plant'),
            'line' => $dictService->getOptionByCode('assembly_line'),
            'users' => (new UserService())->getUserByProduct($request->user())
        ]);
    }


    /**
     * 产品考核-问题追踪
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function issue(Request $request, DictService $dictService): InertiaResponse
    {
        return Inertia::render('Stuff/Issue', [
            'issue_status' => $dictService->getOptionByCode('issue_status'),
            'defect_level' => $dictService->getOptionByCode('defect_level'),
            'defect_category' => $dictService->getOptionByCode('defect_category'),
            'problem_parts' => $dictService->getOptionByCode('problem_parts'),
            'question_position' => $dictService->getOptionByCode('question_position'),
            'exactly_1' => $dictService->getOptionByCode('exactly_1'),
            'exactly_2' => $dictService->getOptionByCode('exactly_2'),
            'exactly_3' => $dictService->getOptionByCode('exactly_3'),
            'exactly_4' => $dictService->getOptionByCode('exactly_4'),
            'exactly_5' => $dictService->getOptionByCode('exactly_5'),
            'exactly_6' => $dictService->getOptionByCode('exactly_6'),
            'exactly_7' => $dictService->getOptionByCode('exactly_7'),
            'exactly_8' => $dictService->getOptionByCode('exactly_8'),
            'exactly_9' => $dictService->getOptionByCode('exactly_9'),
            'exactly_10' => $dictService->getOptionByCode('exactly_10'),
            'exactly_11' => $dictService->getOptionByCode('exactly_11'),
            'exactly_12' => $dictService->getOptionByCode('exactly_12'),
            'exactly_13' => $dictService->getOptionByCode('exactly_13'),
            'exactly_14' => $dictService->getOptionByCode('exactly_14'),
            'exactly_15' => $dictService->getOptionByCode('exactly_15'),
            'exactly_16' => $dictService->getOptionByCode('exactly_16'),
            'exactly_17' => $dictService->getOptionByCode('exactly_17'),
            'exactly_18' => $dictService->getOptionByCode('exactly_18'),
            'exactly_19' => $dictService->getOptionByCode('exactly_19'),
            'exactly_20' => $dictService->getOptionByCode('exactly_20'),
            'exactly_21' => $dictService->getOptionByCode('exactly_21'),
            'exactly_22' => $dictService->getOptionByCode('exactly_22'),
            'users' => (new UserService())->getUserByProduct($request->user())
        ]);
    }


    /**
     * 获取整车服务-问题列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request             $request
     * @param  TaskService $taskService
     * @return JsonResponse
     */
    public function list(Request $request, TaskService $taskService): JsonResponse
    {
        $rules = [
            'user_id' => 'sometimes|nullable|exists_or_null:users,id',
            'engine' => 'sometimes|nullable|integer',
            'date' => 'sometimes|nullable|array',
            'keyword' => 'sometimes|nullable',
            'type' => 'required|in:1,2,3'
        ];
        $messages = [
            'user_id.exists_or_null' => '考核员不存在或已删除',
            'engine.integer' => '机型不正确',
            'type.integer' => '类型不正确',
            'date.array' => '日期不正确',
        ];
        $data = $request->validate($rules, $messages);
        $result = $taskService->getProductList($request->user(), $data);
        return $this->success($result);
    }

    /**
     * 整车服务-动态考核详情
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string          $id
     * @param  Request         $request
     * @param  DictService     $dictService
     * @param  TaskService     $taskService
     * @return InertiaResponse
     */
    public function detail(string $id, Request $request, DictService $dictService, TaskService $taskService, IssueProductService $service): InertiaResponse
    {
        $rules = [
            'id' => 'exists:tasks,id,type,2',
        ];
        $messages = [
            'id.exists' => '考核记录不存在或已删除',
        ];

        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->all()), $rules, $messages);
        if ($validator->fails()) {
            return abort(500)->withMessages($validator->errors());
        }
        $item = $taskService->getProductDetail($request->user(), $id);
        return Inertia::render('Stuff/Detail', [
            'item' => $item,
            'issues' => $service->getListByProduct($request->user(), $id),
            'defect_level' => $dictService->getOptionByCode('defect_level'),
            'defect_category' => $dictService->getOptionByCode('defect_category'),
            'task_status' => $dictService->getOptionByCode('task_status'),
            'engine_type' => $dictService->getOptionByCode('engine_type'),
            'purpose' => $dictService->getOptionByCode('purpose'),
            'assembly_line' => $dictService->getOptionByCode('assembly_line'),
            'question_position' => $dictService->getOptionByCode('question_position'),
            'plant' => $dictService->getOptionByCode('plant'),
            'assembly_status' => $dictService->getOptionByCode('assembly_status'),
            'issue_status' => $dictService->getOptionByCode('issue_status'),
        ]);
    }

    /**
     * 产品考核-考核预览
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string          $id
     * @param  Request         $request
     * @param  DictService     $dictService
     * @param  TaskService     $taskService
     * @return InertiaResponse
     */
    public function preview(string $id, Request $request, DictService $dictService, TaskService $taskService): InertiaResponse
    {
        $rules = [
            'id' => 'exists:tasks,id,type,2',
        ];
        $messages = [
            'id.exists' => '考核记录不存在或已删除',
        ];

        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->all()), $rules, $messages);
        if ($validator->fails()) {
            return abort(500)->withMessages($validator->errors());
        }
        $item = $taskService->getProductPreview($request->user(), $id);
        return Inertia::render('Stuff/Preview', [
            'item' => $item,
            'defect_level' => $dictService->getOptionByCode('defect_level'),
            'defect_category' => $dictService->getOptionByCode('defect_category'),
            'task_status' => $dictService->getOptionByCode('task_status'),
            'engine_type' => $dictService->getOptionByCode('engine_type'),
            'purpose' => $dictService->getOptionByCode('purpose'),
            'assembly_line' => $dictService->getOptionByCode('assembly_line'),
        ]);
    }

    /**
     * 产品考核-考核导出
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string          $id
     * @param  Request         $request
     * @param  TaskService     $taskService
     * @return JsonResponse
     */
    public function export(string $id, Request $request, TaskService $taskService): JsonResponse
    {
        $rules = [
            'id' => 'exists:tasks,id,type,2',
        ];
        $messages = [
            'id.exists' => '考核记录不存在或已删除',
        ];

        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->all()), $rules, $messages);
        if ($validator->fails()) {
            return abort(500)->withMessages($validator->errors());
        }
        $result = $taskService->getProductExport($request->user(), $id);
        return $this->success($result);
    }

    /**
     * 产品考核-删除考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string          $id
     * @param  Request         $request
     * @param  TaskService     $taskService
     * @return JsonResponse
     */
    public function delete(string $id, Request $request, TaskService $taskService): JsonResponse
    {
        $rules = [
            'id' => 'exists:tasks,id,type,2',
        ];
        $messages = [
            'id.exists' => '考核记录不存在或已删除',
        ];

        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->all()), $rules, $messages);
        if ($validator->fails()) {
            return abort(500)->withMessages($validator->errors());
        }
        $taskService->deleteProduct($request->user(), $id);
        return $this->success();
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
            'defect_level' => 'required|integer',
            'defect_description' => 'sometimes',
            'defect_part' => 'sometimes',
            'defect_position' => 'sometimes',
            'defect_cause' => 'sometimes',
            'soma' => 'sometimes',
            'lama' => 'sometimes',
            'note' => 'sometimes',
            'cause' => 'sometimes',
            'eight_disciplines' => 'sometimes',
            'score_card' => 'sometimes',
            'ira' => 'sometimes',
            'department' => 'sometimes',
            'status' => 'sometimes',
            'type' => 'sometimes',
            'file' => 'sometimes|nullable|file'
        ];
        $messages = [
            'id.exists' => '考核记录不存在',
            'defect_level.required' => '缺陷等级不能为空',
            'defect_level.integer' => '缺陷等级不存在',
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
            'cause',
            'eight_disciplines',
            'score_card',
            'ira',
            'department',
            'status',
            'type',
            'file'
        ]);
        $service->updateIssue($request->user(), $id, $data);
        return $this->success();
    }
}