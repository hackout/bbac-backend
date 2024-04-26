<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\AssemblyService;
use App\Services\Backend\DepartmentService;
use App\Services\Backend\DictService;
use App\Services\Backend\WorkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * 任务分配控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class WorkController extends Controller
{
    /**
     * 任务分配
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function index(Request $request, DictService $dictService): InertiaResponse
    {
        return Inertia::render('Work/Index', [
            'work_type' => $dictService->getOptionByCode('work_type'),
            'examine_type' => $dictService->getOptionByCode('examine_type'),
            'departments' => (new DepartmentService)->getFullOptions($request->user())
        ]);
    }

    /**
     * 获取考核单列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request           $request
     * @param  WorkService $workService
     * @return JsonResponse
     */
    public function list(Request $request, WorkService $workService): JsonResponse
    {
        $rules = [
            'department_id' => 'required|exists:departments,id',
            'month' => 'sometimes|nullable|date',
        ];
        $messages = [
            'month.date' => '参数错误',
            'department_id.required' => '请选择一个部门',
            'department_id.exists' => '部门不存在或已删除',
        ];
        $data = $request->validate($rules, $messages);
        $result = $workService->getList($data);
        return $this->success($result);
    }

    /**
     * 添加考核单
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  WorkService  $workService
     * @return JsonResponse
     */
    public function create(Request $request, WorkService $workService): JsonResponse
    {
        $rules = [
            'task_id' => 'sometimes|nullable|exists:tasks,id',
            'content' => 'required|max:200',
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'type' => 'required|integer',
            'period' => 'required|numeric',
        ];

        $messages = [
            'task_id.exists' => '考核单不存在',
            'user_id.required' => '员工不能为空',
            'user_id.exists' => '员工不存在或已删除',
            'content.required' => '工作内容不能为空',
            'content.max' => '工作内容最大支持200个字符',
            'date.required' => '工作时间不能为空',
            'date.date' => '工作时间不正确',
            'type.required' => '任务类型不能为空',
            'type.integer' => '任务类型不正确',
            'period.required' => '工时不能为空',
            'period.numeric' => '工时不正确',
        ];

        $data = $request->validate($rules, $messages);
        $workService->createWork($data);
        return $this->success();
    }

    /**
     * 删除任务单
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $id
     * @param  WorkService  $workService
     * @return JsonResponse
     */
    public function delete(string $id, WorkService $workService): JsonResponse
    {

        $rules = [
            'id' => 'exists:tasks,id',
        ];
        $messages = [
            'id.exists' => '任务单信息错误',
        ];
        $validator = Validator::make([
            'id' => $id
        ], $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $workService->delete($id);
        return $this->success();
    }
}
