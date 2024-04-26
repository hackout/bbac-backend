<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\DepartmentService;
use App\Services\Backend\DictService;
use App\Services\Backend\TrainingService;
use App\Services\Backend\TrainingUserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * 培训管理控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class TrainingController extends Controller
{

    /**
     * 安全培训
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function safe(Request $request, DictService $dictService, DepartmentService $departmentService): InertiaResponse
    {
        return Inertia::render('Training/Safe', [
            'training_type' => $dictService->getOptionByCode('training_type'),
            'training_status' => $dictService->getOptionByCode('training_status'),
            'training_item_status' => $dictService->getOptionByCode('training_1_status'),
            'departments' => $departmentService->getFullOptions($request->user())
        ]);
    }

    /**
     * 技能培训
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function skill(Request $request, DictService $dictService, DepartmentService $departmentService): InertiaResponse
    {
        return Inertia::render('Training/Skill', [
            'training_type' => $dictService->getOptionByCode('training_type'),
            'training_status' => $dictService->getOptionByCode('training_status'),
            'training_item_status' => $dictService->getOptionByCode('training_2_status'),
            'departments' => $departmentService->getFullOptions($request->user())
        ]);
    }

    /**
     * 综合培训
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function multiple(Request $request, DictService $dictService, DepartmentService $departmentService): InertiaResponse
    {
        return Inertia::render('Training/Multiple', [
            'training_type' => $dictService->getOptionByCode('training_type'),
            'training_status' => $dictService->getOptionByCode('training_status'),
            'training_item_status' => $dictService->getOptionByCode('training_3_status'),
            'departments' => $departmentService->getFullOptions($request->user())
        ]);
    }

    /**
     * 查询培训列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string          $type
     * @param  Request         $request
     * @param  TrainingService $trainingService
     * @return JsonResponse
     */
    public function list(string $type, Request $request, TrainingService $trainingService): JsonResponse
    {
        $rules = [
            'keyword' => 'sometimes|nullable',
            'department_id' => 'sometimes|nullable|exists_or_null:departments,id',
            'date' => 'sometimes|nullable|array|max:2'
        ];
        $messages = [
            'department_id.exists_or_null' => '部门信息不正确',
            'date.array' => '时间参数不正确',
            'date.max' => '时间参数不正确'
        ];
        $validator = Validator::make(array_merge([
            'type' => $type
        ], $request->input()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'keyword',
            'department_id',
            'date',
        ]);
        $data['type'] = $type;
        $result = $trainingService->getList($data);
        return $this->success($result);
    }

    /**
     * 获取导入模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string             $type
     * @param  TrainingService    $trainingService
     * @return BinaryFileResponse
     */
    public function template(string $type, TrainingService $trainingService): BinaryFileResponse
    {
        return $trainingService->getTemplateByType($type);
    }

    /**
     * 导入培训数据
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string          $type
     * @param  Request         $request
     * @param  TrainingService $service
     * @return JsonResponse
     */
    public function import(string $type, Request $request, TrainingService $service): JsonResponse
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

    /**
     * 上传附件到培训
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string          $type
     * @param  string          $id
     * @param  Request         $request
     * @param  TrainingService $trainingService
     * @return JsonResponse
     */
    public function upload(string $type, string $id, Request $request, TrainingService $trainingService): JsonResponse
    {
        $rules = [
            'file' => 'required|file',
            'id' => 'exists:trainings,id'
        ];
        $messages = [
            'id.exists' => '信息参数不正确',
            'file.required' => '上传文件不能为空',
            'file.file' => '上传文件错误',
        ];
        $validator = Validator::make([
            'id' => $id,
            'file' => $request->file('file')
        ], $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'file'
        ]);
        $trainingService->uploadByTypeId($type, $id, $data['file']);
        return $this->success();
    }

    /**
     * 删除培训附件
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string          $type
     * @param  string          $id
     * @param  string          $file
     * @param  TrainingService $trainingService
     * @return JsonResponse
     */
    public function fileDelete(string $type, string $id, string $file, TrainingService $trainingService): JsonResponse
    {
        $rules = [
            'file_uuid' => 'exists:media,uuid',
            'id' => 'exists:trainings,id'
        ];
        $messages = [
            'id.exists' => '信息参数不正确',
            'file_uuid.exists' => '信息参数不正确',
        ];
        $validator = Validator::make([
            'id' => $id,
            'file_uuid' => $file,
        ], $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $trainingService->deleteFile($id, $file);
        return $this->success();
    }

    /**
     * 删除培训
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string          $type
     * @param  Request         $request
     * @param  TrainingService $service
     * @return JsonResponse
     */
    public function batchDelete(string $type, Request $request, TrainingService $service): JsonResponse
    {

        $rules = [
            'ids' => 'required|array',
            'ids.*' => 'required|exists_plus:trainings,id'
        ];
        $messages = [
            'ids.required' => '请选择一条数据',
            'ids.array' => '数据参数错误',
            'ids.*.required' => '数据参数不能为空',
            'ids.*.exists_plus' => '数据参数不存在',
        ];
        $data = $request->validate($rules, $messages);
        $service->batch_delete($data['ids']);
        return $this->success();
    }

    /**
     * 设置员工培训状态
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string              $type
     * @param  Request             $request
     * @param  TrainingUserService $trainingUserService
     * @return JsonResponse
     */
    public function patch(string $type, Request $request, TrainingUserService $trainingUserService): JsonResponse
    {
        $rules = [
            'id' => 'exists:trainings,id',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|integer',
            'type' => 'sometimes'
        ];
        $messages = [
            'id.exists_plus' => '培训不存在或已删除',
            'user_id.exists' => '员工账号不存在',
            'status.in' => '状态参数错误'
        ];
        $validator = Validator::make(array_merge([
            'type' => $type,
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'id',
            'user_id',
            'type',
            'status'
        ]);
        $trainingUserService->updateStatus($data,$request->user());
        return $this->success();
    }

    /**
     * 导出培训数据
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string          $type
     * @param  Request         $request
     * @param  TrainingService $service
     * @return JsonResponse
     */
    public function export(string $type, Request $request, TrainingService $service): JsonResponse
    {
        $rules = [
            'keyword' => 'sometimes|nullable',
            'department_id' => 'sometimes|nullable|exists_or_null:departments,id',
            'date' => 'sometimes|nullable|array|max:2'
        ];
        $messages = [
            'department_id.exists_or_null' => '部门信息不正确',
            'date.array' => '时间参数不正确',
            'date.max' => '时间参数不正确'
        ];
        $data = $request->validate($rules, $messages);
        $data['type'] = $type;
        $result = $service->export($data);
        return $this->success($result);
    }

    /**
     * 修改培训数据
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string          $type
     * @param  string          $id
     * @param  Request         $request
     * @param  TrainingService $service
     * @return JsonResponse
     */
    public function update(string $type, string $id, Request $request, TrainingService $service): JsonResponse
    {
        $rules = [
            'name' => 'required_without:status,started_at,period,ended_at|nullable|max:100',
            'status' => 'required_without:name,started_at,period,ended_at|nullable|integer',
            'started_at' => 'required_without:status,name,period,ended_at|nullable|date',
            'period' => 'required_without:status,started_at,name,ended_at|nullable|integer',
            'ended_at' => 'required_without:status,started_at,period,name|nullable|date',
            'id' => 'exists:trainings,id',
            'type' => 'sometimes',
        ];
        $messages = [
            'id.exists' => '培训参数不正确',
            'name.required_without' => '培训参数不正确',
            'status.required_without' => '培训参数不正确',
            'started_at.required_without' => '培训参数不正确',
            'period.required_without' => '培训参数不正确',
            'ended_at.required_without' => '培训参数不正确',
            'name.max' => '培训内容不能超过100个字符',
            'status.integer' => '培训状态不正确',
            'started_at.date' => '培训开始时间不正确',
            'period.integer' => '培训课时周期不正确',
            'ended_at.date' => '培训结束时间不正确',
        ];
        $validator = Validator::make(array_merge([
            'type' => $type,
            'id' => $id
        ], $request->input()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'name',
            'status',
            'started_at',
            'period',
            'ended_at',
        ]);
        $result = $service->setItemValue($id, $data);
        return $this->success($result);
    }
}
