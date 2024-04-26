<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\AssemblyService;
use App\Services\Backend\DictService;
use App\Services\Backend\TaskCronService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * 计划任务控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class TaskCronController extends Controller
{
    /**
     * 任务配置
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function index(Request $request, DictService $dictService): InertiaResponse
    {
        return Inertia::render('Task/Cron', [
            'plant' => $dictService->getOptionByCode('plant'),
            'line' => $dictService->getOptionByCode('assembly_line'),
            'engine_type' => $dictService->getOptionByCode('engine_type'),
            'status' => $dictService->getOptionByCode('assembly_status'),
            'examine_type' => $dictService->getOptionByCode('examine_type'),
            'assemblies' => (new AssemblyService)->getOptions()
        ]);
    }

    /**
     * 获取扭矩数据列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request           $request
     * @param  TaskCronService $taskCronService
     * @return JsonResponse
     */
    public function list(Request $request, TaskCronService $taskCronService): JsonResponse
    {
        $rules = [
            'keyword' => 'sometimes|nullable',
            'is_valid' => 'sometimes|nullable|boolean',
            'examine_type' => 'sometimes|nullable|integer',
        ];
        $messages = [
            'is_valid.boolean' => '参数错误',
            'examine_type.integer' => '参数错误',
        ];
        $data = $request->validate($rules, $messages);
        $result = $taskCronService->getList($data);
        return $this->success($result);
    }

    /**
     * 添加计划任务
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  TaskCronService  $taskCronService
     * @return JsonResponse
     */
    public function create(Request $request, TaskCronService $taskCronService): JsonResponse
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
            'period' => 'required|numeric',
            'days' => 'required|array|min:1',
            'days.*.day' => 'required|integer',
            'days.*.unit' => 'required|integer',
            'days.*.date' => 'required|array|between:2,2',
            'days.*.date.*' => 'required|date',
            'yield' => 'required|integer',
            'yield_unit' => 'required|integer',
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
            'period.required' => '工时不能为空',
            'period.numeric' => '工时不正确',
            'days.required' => '循环任务不能为空',
            'days.array' => '循环任务不正确',
            'days.min' => '请至少填写一个循环任务',
            'days.*.day.required' => '循环任务天数不能为空',
            'days.*.day.integer' => '循环任务天数不正确',
            'days.*.unit.required' => '循环任务次数不能为空',
            'days.*.unit.integer' => '循环任务次数不正确',
            'days.*.date.required' => '循环任务时间不能为空',
            'days.*.date.array' => '循环任务时间不正确',
            'days.*.date.between' => '循环任务时间不正确',
            'days.*.date.*.required' => '循环任务时间不正确',
            'days.*.date.*.date' => '循环任务时间不正确',
            'yield.required' => '产量生产排产不能为空',
            'yield.integer' => '产量生产排产不正确',
            'yield_unit.required' => '产量生产次数不能为空',
            'yield_unit.integer' => '产量生产次数不正确',
        ];

        $data = $request->validate($rules, $messages);
        $taskCronService->create(array_merge(['user_id' => $request->user()->id], $data));
        return $this->success();
    }

    /**
     * 更新任务配置
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string            $id
     * @param  Request           $request
     * @param  TaskCronService  $taskCronService
     * @return JsonResponse
     */
    public function patch(string $id, string $status, Request $request, TaskCronService $taskCronService): JsonResponse
    {
        $rules = [
            'id' => 'exists:task_crons,id',
        ];
        $messages = [
            'id.exists' => '任务配置信息错误',
        ];
        $validator = Validator::make([
            'id' => $id
        ], $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $taskCronService->setValue($id, 'is_valid', $status == 'valid');
        return $this->success();
    }

    /**
     * 删除任务配置
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $id
     * @param  TaskCronService  $taskCronService
     * @return JsonResponse
     */
    public function delete(string $id, TaskCronService $taskCronService): JsonResponse
    {

        $rules = [
            'id' => 'exists:task_crons,id',
        ];
        $messages = [
            'id.exists' => '任务配置信息错误',
        ];
        $validator = Validator::make([
            'id' => $id
        ], $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $taskCronService->delete($id);
        return $this->success();
    }
}
