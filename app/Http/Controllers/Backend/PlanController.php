<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\AssemblyService;
use App\Services\Backend\DictService;
use App\Services\Backend\PlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * 排产计划控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class PlanController extends Controller
{
    /**
     * 排产计划
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function index(Request $request, DictService $dictService): InertiaResponse
    {
        return Inertia::render('Plan/Index', [
            'plant' => $dictService->getOptionByCode('plant'),
            'line' => $dictService->getOptionByCode('assembly_line'),
            'engine_type' => $dictService->getOptionByCode('engine_type'),
            'assemblies' => (new AssemblyService)->getOptions()
        ]);
    }

    /**
     * 获取扭矩数据列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request           $request
     * @param  PlanService $planService
     * @return JsonResponse
     */
    public function list(Request $request, PlanService $planService): JsonResponse
    {
        $rules = [
            'keyword' => 'sometimes|nullable',
            'plant' => 'sometimes|nullable|integer',
            'line' => 'sometimes|nullable|integer',
            'type' => 'sometimes|nullable|integer',
        ];
        $messages = [
            'plant.integer' => '参数错误',
            'line.integer' => '参数错误',
            'type.integer' => '参数错误',
        ];
        $data = $request->validate($rules, $messages);
        $result = $planService->getList($data);
        return $this->success($result);
    }

    /**
     * 添加排查计划
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  PlanService  $planService
     * @return JsonResponse
     */
    public function create(Request $request,PlanService $planService):JsonResponse
    {
        $rules = [
            'assembly_id' => 'required|exists:assemblies,id',
            'plant' => 'required|integer',
            'line' => 'required|integer',
            'type' => 'required|integer',
            'quantity' => 'required|integer',
            'plan_at' => 'required|date',
            'remark' => 'sometimes|nullable|max:200',
        ];

        $messages = [
            'assembly_id.required' => '总成不能为空',
            'assembly_id.exists' => '总成不存在或已删除',
            'plant.required' => '工厂不能为空',
            'plant.integer' => '工厂不正确',
            'line.required' => '产线不能为空',
            'line.integer' => '产线不正确',
            'type.required' => '机型不能为空',
            'type.integer' => '机型不正确',
            'quantity.required' => '产量不能为空',
            'quantity.integer' => '产量不正确',
            'plan_at.required' => '计划日期不能为空',
            'plan_at.date' => '计划日期不正确',
            'remark.max' => '备注信息最大支持200个字符',
        ];

        $data = $request->validate($rules,$messages);
        $planService->create($data);
        return $this->success();
    }

    /**
     * 更新排产计划
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string            $id
     * @param  Request           $request
     * @param  PlanService  $planService
     * @return JsonResponse
     */
    public function update(string $id, Request $request, PlanService  $planService): JsonResponse
    {
        $rules = [
            'id' => 'exists:plans,id',
            'assembly_id' => 'required|exists:assemblies,id',
            'plant' => 'required|integer',
            'line' => 'required|integer',
            'type' => 'required|integer',
            'quantity' => 'required|integer',
            'plan_at' => 'required|date',
            'remark' => 'sometimes|nullable|max:200',
        ];
        $messages = [
            'assembly_id.required' => '总成不能为空',
            'assembly_id.exists' => '总成不存在或已删除',
            'plant.required' => '工厂不能为空',
            'plant.integer' => '工厂不正确',
            'line.required' => '产线不能为空',
            'line.integer' => '产线不正确',
            'type.required' => '机型不能为空',
            'type.integer' => '机型不正确',
            'quantity.required' => '产量不能为空',
            'quantity.integer' => '产量不正确',
            'plan_at.required' => '计划日期不能为空',
            'plan_at.date' => '计划日期不正确',
            'remark.max' => '备注信息最大支持200个字符',
            'id.exists' => '排产计划信息错误',
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'assembly_id',
            'plant',
            'line',
            'type',
            'quantity',
            'plan_at',
            'remark',
        ]);
        $planService->update($id, $data);
        return $this->success();
    }

    /**
     * 导出排产计划
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request           $request
     * @param  PlanService $planService
     * @return JsonResponse
     */
    public function export(Request $request, PlanService $planService): JsonResponse
    {
        $rules = [
            'keyword' => 'sometimes|nullable',
            'plant' => 'sometimes|nullable|integer',
            'line' => 'sometimes|nullable|integer',
            'type' => 'sometimes|nullable|integer',
        ];
        $messages = [
            'plant.integer' => '参数错误',
            'line.integer' => '参数错误',
            'type.integer' => '参数错误',
        ];
        $data = $request->validate($rules, $messages);
        $result = $planService->export($data);
        return $this->success($result);
    }

    /**
     * 删除排产计划
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $id
     * @param  PlanService  $planService
     * @return JsonResponse
     */
    public function delete(string $id,PlanService $planService):JsonResponse
    {
        
        $rules = [
            'id' => 'exists:plans,id',
        ];
        $messages = [
            'id.exists' => '排产计划信息错误',
        ];
        $validator = Validator::make([
            'id' => $id
        ], $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $planService->delete($id);
        return $this->success();
    }
}
