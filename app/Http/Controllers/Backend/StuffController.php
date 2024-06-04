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
use App\Services\Backend\IssueVehicleService;
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
    public function detail(string $id, Request $request, DictService $dictService, TaskService $taskService): InertiaResponse
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
}