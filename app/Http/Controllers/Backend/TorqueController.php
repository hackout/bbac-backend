<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\AssemblyService;
use App\Services\Backend\DictService;
use App\Services\Backend\TorqueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * 扭矩数据库控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class TorqueController extends Controller
{

    /**
     * 扭矩数据库
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function index(Request $request, DictService $dictService): InertiaResponse
    {
        return Inertia::render('Torque/Index', [
            'plant' => $dictService->getOptionByCode('plant'),
            'line' => $dictService->getOptionByCode('assembly_line'),
            'engine_type' => $dictService->getOptionByCode('engine_type'),
            'motorcycle_type' => $dictService->getOptionByCode('motorcycle_type'),
            'model' => $dictService->getOptionByCode('bolt_model'),
            'type' => $dictService->getOptionByCode('bolt_type'),
            'status' => $dictService->getOptionByCode('bolt_status'),
            'stage' => $dictService->getOptionByCode('assembly_status'),
            'special' => $dictService->getOptionByCode('special'),
            'assemblies' => (new AssemblyService)->getOptions()
        ]);
    }

    /**
     * 获取扭矩数据列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request           $request
     * @param  TorqueService $torqueService
     * @return JsonResponse
     */
    public function list(Request $request, TorqueService $torqueService): JsonResponse
    {
        $rules = [
            'keyword' => 'sometimes|nullable',
            'plant' => 'sometimes|nullable|integer',
            'line' => 'sometimes|nullable|integer',
        ];
        $messages = [
            'plant.integer' => '参数错误',
            'line.integer' => '参数错误',
        ];
        $data = $request->validate($rules, $messages);
        $result = $torqueService->getList($data);
        return $this->success($result);
    }

    /**
     * 更新扭矩数据库
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string            $id
     * @param  Request           $request
     * @param  TorqueService $torqueService
     * @return JsonResponse
     */
    public function update(string $id, Request $request, TorqueService $torqueService): JsonResponse
    {
        $rules = [
            'id' => 'exists:torques,id',
            'plant' => 'required|integer',
            'line' => 'required|integer',
            'engine' => 'required|integer',
            'vehicle_type' => 'required|integer',
            'assembly_id' => 'required|exists:assemblies,id',
            'number' => 'required|unique:torques,number,' . $id,
            'content_zh' => 'required|max:200',
            'content_en' => 'required|max:200',
            'quantity' => 'required|integer',
            'model' => 'required|integer',
            'type' => 'required|integer',
            'status' => 'required|integer',
            'stage' => 'required|integer',
            'station' => 'required|max:100',
            'sub_station' => 'required|max:100',
            'special' => 'required|integer',
            'param' => 'required|max:100',
            'torque_target' => 'required|numeric',
            'torque_lower' => 'required|numeric',
            'torque_upper' => 'required|numeric',
            'angle_target' => 'required|numeric',
            'angle_lower' => 'required|numeric',
            'angle_upper' => 'required|numeric',
            'lasted_at' => 'sometimes|nullable|date',
            'expected_at' => 'sometimes|nullable|date',
            'final_at' => 'sometimes|nullable|date',
            'start_torque' => 'required|numeric',
            'residual_torque' => 'required|numeric',
            'pfu_test' => 'required|numeric',
            'pfu_lower' => 'required|numeric',
            'pfu_upper' => 'required|numeric',
            'pfu_early_lower' => 'required|numeric',
            'pfu_early_upper' => 'required|numeric',
            'l_pfu_test' => 'required|numeric',
            'l_pfu_lower' => 'required|numeric',
            'l_pfu_upper' => 'required|numeric',
            'l_pfu_early_lower' => 'required|numeric',
            'l_pfu_early_upper' => 'required|numeric',
            'is_io' => 'required|boolean'
        ];
        $messages = [
            'plant.required' => '工厂不能为空',
            'line.required' => '产线不能为空',
            'engine.required' => '发动机型号不能为空',
            'vehicle_type.required' => '车型不能为空',
            'assembly_id.required' => '总成号不能为空',
            'number.required' => '螺栓编号不能为空',
            'content_zh.required' => '描述-中文不能为空',
            'content_en.required' => '描述-英文不能为空',
            'quantity.required' => '螺栓数量不能为空',
            'model.required' => '螺栓分类1不能为空',
            'type.required' => '螺栓分类2不能为空',
            'status.required' => '放行状态不能为空',
            'stage.required' => '项目阶段不能为空',
            'station.required' => '工位不能为空',
            'sub_station.required' => '工位2不能为空',
            'special.required' => '特殊特性不能为空',
            'param.required' => '螺栓参数不能为空',
            'torque_target.required' => '目标扭矩不能为空',
            'torque_lower.required' => '扭矩下限不能为空',
            'torque_upper.required' => '扭矩上限不能为空',
            'angle_target.required' => '角度标准不能为空',
            'angle_lower.required' => '角度下限不能为空',
            'angle_upper.required' => '角度上限不能为空',
            'lasted_at.date' => '最近放行时间参数不正确',
            'expected_at.date' => '预计放行时间参数不正确',
            'final_at.date' => '最终放行时间参数不正确',
            'start_torque.required' => '起始扭矩不能为空',
            'residual_torque.required' => '转矩角不能为空',
            'pfu_test.required' => 'PFU-测试值不能为空',
            'pfu_lower.required' => 'PFU-考核下限不能为空',
            'pfu_upper.required' => 'PFU-考核上限不能为空',
            'pfu_early_lower.required' => 'PFU-预警上限不能为空',
            'pfu_early_upper.required' => 'PFU-预警下限不能为空',
            'l_pfu_test.required' => 'L-PFU-测试值不能为空',
            'l_pfu_lower.required' => 'L-PFU-考核下限不能为空',
            'l_pfu_upper.required' => 'L-PFU-考核上限不能为空',
            'l_pfu_early_lower.required' => 'L-PFU-预警上限不能为空',
            'l_pfu_early_upper.required' => 'L-PFU-预警下限不能为空',
            'plant.integer' => '工厂不正确',
            'line.integer' => '产线不正确',
            'engine.integer' => '发动机型号不正确',
            'vehicle_type.integer' => '车型不正确',
            'assembly_id.exists' => '总成号不存在',
            'number.unique' => '螺栓编号已存在',
            'content_zh.max' => '描述-中文不能超过200个字符',
            'content_en.max' => '描述-英文不能超过200个字符',
            'quantity.integer' => '螺栓数量不正确',
            'model.integer' => '螺栓分类1不正确',
            'type.integer' => '螺栓分类2不正确',
            'status.integer' => '放行状态不正确',
            'stage.integer' => '项目阶段不正确',
            'station.max' => '工位不能超过100个字符',
            'sub_station.max' => '工位2不能超过100个字符',
            'special.integer' => '特殊特性不正确',
            'param.max' => '螺栓参数不能超过100个字符',
            'torque_target.numeric' => '目标扭矩不正确',
            'torque_lower.numeric' => '扭矩下限不正确',
            'torque_upper.numeric' => '扭矩上限不正确',
            'angle_target.numeric' => '角度标准不正确',
            'angle_lower.numeric' => '角度下限不正确',
            'angle_upper.numeric' => '角度上限不正确',
            'start_torque.numeric' => '起始扭矩不正确',
            'residual_torque.numeric' => '转矩角不正确',
            'pfu_test.numeric' => 'PFU-测试值不正确',
            'pfu_lower.numeric' => 'PFU-考核下限不正确',
            'pfu_upper.numeric' => 'PFU-考核上限不正确',
            'pfu_early_lower.numeric' => 'PFU-预警上限不正确',
            'pfu_early_upper.numeric' => 'PFU-预警下限不正确',
            'l_pfu_test.numeric' => 'L-PFU-测试值不正确',
            'l_pfu_lower.numeric' => 'L-PFU-考核下限不正确',
            'l_pfu_upper.numeric' => 'L-PFU-考核上限不正确',
            'l_pfu_early_lower.numeric' => 'L-PFU-预警上限不正确',
            'l_pfu_early_upper.numeric' => 'L-PFU-预警下限不正确',
            'id.exists' => '扭矩信息错误',
            'is_io.required' => 'IO信息不能为空',
            'is_io.boolean' => 'IO信息不正确',
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'plant',
            'line',
            'engine',
            'vehicle_type',
            'assembly_id',
            'number',
            'content_zh',
            'content_en',
            'quantity',
            'model',
            'type',
            'status',
            'stage',
            'station',
            'sub_station',
            'special',
            'param',
            'torque_target',
            'torque_lower',
            'torque_upper',
            'angle_target',
            'angle_lower',
            'angle_upper',
            'lasted_at',
            'expected_at',
            'final_at',
            'start_torque',
            'residual_torque',
            'pfu_test',
            'pfu_lower',
            'pfu_upper',
            'pfu_early_lower',
            'pfu_early_upper',
            'l_pfu_test',
            'l_pfu_lower',
            'l_pfu_upper',
            'l_pfu_early_lower',
            'l_pfu_early_upper',
            'is_io'
        ]);
        $torqueService->updateByUser($request->user(),$id, $data);
        return $this->success();
    }

    
    /**
     * 获取导入模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  TorqueService    $torqueService
     * @return BinaryFileResponse
     */
    public function template(TorqueService $torqueService): BinaryFileResponse
    {
        return $torqueService->downloadImportTemplate();
    }

    /**
     * 导入扭矩数据
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @param  TorqueService $service
     * @return JsonResponse
     */
    public function import(Request $request, TorqueService $service): JsonResponse
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
        $service->importByUser($request->user(),$data['file']);
        return $this->success();
    }

}
