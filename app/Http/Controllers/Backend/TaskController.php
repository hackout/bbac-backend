<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\AssemblyService;
use App\Services\Backend\DictService;
use App\Services\Backend\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * 任务单控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class TaskController extends Controller
{
    /**
     * 考核单中心
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function index(Request $request, DictService $dictService): InertiaResponse
    {
        return Inertia::render('Task/Index', [
            'plant' => $dictService->getOptionByCode('plant'),
            'line' => $dictService->getOptionByCode('assembly_line'),
            'engine_type' => $dictService->getOptionByCode('engine_type'),
            'status' => $dictService->getOptionByCode('assembly_status'),
            'task_status' => $dictService->getOptionByCode('task_status'),
            'examine_type' => $dictService->getOptionByCode('examine_type'),
            'assemblies' => (new AssemblyService)->getOptions()
        ]);
    }

    /**
     * 获取考核单列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request           $request
     * @param  TaskService $taskService
     * @return JsonResponse
     */
    public function list(Request $request, TaskService $taskService): JsonResponse
    {
        $rules = [
            'keyword' => 'sometimes|nullable',
            'date' => 'sometimes|nullable|date',
            'task_status' => 'sometimes|nullable|integer',
            'examine_type' => 'sometimes|nullable|integer',
        ];
        $messages = [
            'date.date' => '参数错误',
            'task_status.integer' => '参数错误',
            'examine_type.integer' => '参数错误',
        ];
        $data = $request->validate($rules, $messages);
        $result = $taskService->getList($data);
        return $this->success($result);
    }

    /**
     * 获取考核单选项列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request           $request
     * @param  TaskService $taskService
     * @return JsonResponse
     */
    public function option(Request $request, TaskService $taskService): JsonResponse
    {
        $rules = [
            'keyword' => 'sometimes|nullable',
            'date' => 'sometimes|nullable|date',
            'task_status' => 'sometimes|nullable|integer',
            'examine_type' => 'sometimes|nullable|integer',
        ];
        $messages = [
            'date.date' => '参数错误',
            'task_status.integer' => '参数错误',
            'examine_type.integer' => '参数错误',
        ];
        $data = $request->validate($rules, $messages);
        $result = $taskService->getOption($data);
        return $this->success($result);
    }

    /**
     * 添加考核单
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  TaskService  $taskService
     * @return JsonResponse
     */
    public function create(Request $request, TaskService $taskService): JsonResponse
    {
        $rules = [
            'assembly_id' => 'required|exists:assemblies,id',
            'name' => 'required|max:100',
            'examine_id' => 'required|exists:examines,id',
            'type' => 'required|integer',
            'plant' => 'required|integer',
            'line' => 'required|integer',
            'engine' => 'required|integer',
            'status' => 'required|integer',
            'number' => 'required|numeric',
            'period' => 'required|numeric',
        ];

        $messages = [
            'assembly_id.required' => '总成不能为空',
            'assembly_id.exists' => '总成不存在或已删除',
            'examine_id.required' => '考核模板不能为空',
            'examine_id.exists' => '考核模板不存在或已删除',
            'plant.required' => '工厂不能为空',
            'plant.integer' => '工厂不正确',
            'line.required' => '产线不能为空',
            'line.integer' => '产线不正确',
            'type.required' => '考核类型不能为空',
            'type.integer' => '考核类型不正确',
            'engine.required' => '机型不能为空',
            'engine.integer' => '机型不正确',
            'name.required' => '考核单名称不能为空',
            'name.max' => '考核单名称最大支持100个字符',
            'status.required' => '项目阶段不能为空',
            'status.integer' => '项目阶段不正确',
            'number.required' => '生成单数不能为空',
            'number.numeric' => '生成单数不正确',
            'period.required' => '工时不能为空',
            'period.numeric' => '工时不正确',
        ];

        $data = $request->validate($rules, $messages);
        $taskService->createByNumber($data);
        return $this->success();
    }

    /**
     * 删除任务单
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $id
     * @param  TaskService  $taskService
     * @return JsonResponse
     */
    public function delete(string $id, TaskService $taskService): JsonResponse
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
        $taskService->delete($id);
        return $this->success();
    }
}
