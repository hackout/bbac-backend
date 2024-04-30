<?php

namespace App\Http\Controllers\Backend;

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
 * 整车服务-控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class VehicleController extends Controller
{

    /**
     * 整车服务-未处理问题
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function index(Request $request, DictService $dictService): InertiaResponse
    {
        return Inertia::render('Vehicle/Index', [
            'motorcycle_type' => $dictService->getOptionByCode('motorcycle_type'),
            'car_series' => $dictService->getOptionByCode('car_series'),
            'sensor_point' => $dictService->getOptionByCode('sensor_point'),
            'service_shift' => $dictService->getOptionByCode('service_shift'),
            'eb_type' => $dictService->getOptionByCode('eb_type'),
            'service_factory' => $dictService->getOptionByCode('service_factory'),
            'block_status' => $dictService->getOptionByCode('block_status'),
            'block_content' => $dictService->getOptionByCode('block_content'),
            'issue_status' => $dictService->getOptionByCode('issue_status'),
            'issue_type' => $dictService->getOptionByCode('issue_type'),
            'detect_area' => $dictService->getOptionByCode('detect_area'),
        ]);
    }

    /**
     * 整车服务-问题详情
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string          $id
     * @param  Request         $request
     * @param  DictService     $dictService
     * @return InertiaResponse
     */
    public function detail(string $id, Request $request, DictService $dictService): InertiaResponse
    {
        $rules = [
            'id' => 'exists:issue_vehicles,id',
        ];
        $messages = [
            'id.exists' => '未找到相关问题',
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return abort(404);
        }
        return Inertia::render('Vehicle/Detail', [
            'motorcycle_type' => $dictService->getOptionByCode('motorcycle_type'),
            'car_series' => $dictService->getOptionByCode('car_series'),
            'sensor_point' => $dictService->getOptionByCode('sensor_point'),
            'service_shift' => $dictService->getOptionByCode('service_shift'),
            'eb_type' => $dictService->getOptionByCode('eb_type'),
            'service_factory' => $dictService->getOptionByCode('service_factory'),
            'block_status' => $dictService->getOptionByCode('block_status'),
            'block_content' => $dictService->getOptionByCode('block_content'),
            'issue_status' => $dictService->getOptionByCode('issue_status'),
            'issue_type' => $dictService->getOptionByCode('issue_type'),
            'detect_area' => $dictService->getOptionByCode('detect_area'),
            'defect_level' => $dictService->getOptionByCode('defect_level'),
            'root_cause_type' => $dictService->getOptionByCode('root_cause_type'),
            'item' => (new IssueVehicleService)->findById($id),
            'logs' => (new IssueVehicleLogService)->getListById($id)
        ]);
    }

    /**
     * 整车服务-问题预览
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string          $id
     * @param  Request         $request
     * @param  DictService     $dictService
     * @return InertiaResponse
     */
    public function report(string $id, Request $request, DictService $dictService): InertiaResponse
    {
        $rules = [
            'id' => 'exists:issue_vehicles,id',
        ];
        $messages = [
            'id.exists' => '未找到相关问题',
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return abort(404);
        }
        return Inertia::render('Vehicle/Report', [
            'motorcycle_type' => $dictService->getOptionByCode('motorcycle_type'),
            'car_series' => $dictService->getOptionByCode('car_series'),
            'sensor_point' => $dictService->getOptionByCode('sensor_point'),
            'service_shift' => $dictService->getOptionByCode('service_shift'),
            'eb_type' => $dictService->getOptionByCode('eb_type'),
            'service_factory' => $dictService->getOptionByCode('service_factory'),
            'block_status' => $dictService->getOptionByCode('block_status'),
            'block_content' => $dictService->getOptionByCode('block_content'),
            'issue_status' => $dictService->getOptionByCode('issue_status'),
            'issue_type' => $dictService->getOptionByCode('issue_type'),
            'detect_area' => $dictService->getOptionByCode('detect_area'),
            'defect_level' => $dictService->getOptionByCode('defect_level'),
            'root_cause_type' => $dictService->getOptionByCode('root_cause_type'),
            'item' => (new IssueVehicleService)->findById($id),
            'logs' => (new IssueVehicleLogService)->getListById($id)
        ]);
    }

    /**
     * 获取整车服务-问题列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request             $request
     * @param  IssueVehicleService $issueVehicleService
     * @return JsonResponse
     */
    public function list(Request $request, IssueVehicleService $issueVehicleService): JsonResponse
    {
        $rules = [
            'plant' => 'sometimes|nullable|integer',
            'eb_type' => 'sometimes|nullable|integer',
            'type' => 'sometimes|nullable|integer',
            'date' => 'sometimes|nullable|array',
            'keyword' => 'sometimes|nullable',
            'status' => 'required|in:a,b'
        ];
        $messages = [
            'plant.integer' => '厂区不正确',
            'eb_type.integer' => '机型不正确',
            'type.integer' => '问题类型不正确',
            'date.array' => '日期不正确',
            'status.required' => '参数错误',
            'status.in' => '参数错误',
        ];
        $data = $request->validate($rules, $messages);
        $result = $issueVehicleService->getList($request->user(), $data);
        return $this->success($result);
    }


    /**
     * 上传图片到整车服务问题追踪
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string                   $id
     * @param  Request                  $request
     * @param  IssueVehicleService $issueVehicleService
     * @return JsonResponse
     */
    public function upload(string $id, Request $request, IssueVehicleService $issueVehicleService): JsonResponse
    {
        $rules = [
            'id' => 'exists:issue_vehicles,id',
            'file' => 'required|mimetypes:video/*,image/*'
        ];
        $messages = [
            'id.exists' => '当前模板无法编辑',
            'file.required' => '文件不能为空',
            'file.mimetypes' => '文件不合规'
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->all()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'file'
        ]);
        $result = $issueVehicleService->upload($data['file']);
        return response()->json([
            'errno' => 0,
            'data' => $result
        ]);
    }

    /**
     * 关闭问题
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string              $id
     * @param  Request             $request
     * @param  IssueVehicleService $issueVehicleService
     * @return JsonResponse
     */
    public function close(string $id, Request $request, IssueVehicleService $issueVehicleService): JsonResponse
    {
        $rules = [
            'id' => 'exists:issue_vehicles,id',
        ];
        $messages = [
            'id.exists' => '问题不存在或已删除'
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->all()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $issueVehicleService->close($request->user(), $id);
        return $this->success();
    }

    public function update(string $id, Request $request, IssueVehicleService $issueVehicleService): JsonResponse
    {
        $rules = [
            'id' => 'exists:issue_vehicles,id',
            'initial_analysis' => 'sometimes|nullable',
            'initial_action' => 'sometimes|nullable',
            'status' => 'sometimes|nullable|integer',
            'type' => 'sometimes|nullable|integer',
            'defect_level' => 'sometimes|nullable|integer',
            'soma' => 'sometimes|nullable',
            'lama' => 'sometimes|nullable',
            'eight_disciplines' => 'sometimes|nullable',
            'ira' => 'sometimes|nullable',
            'is_ppm' => 'sometimes|nullable|boolean',
            'is_pre_highlight' => 'sometimes|nullable|boolean',
            'detect_area' => 'sometimes|nullable|integer',
            'quantity' => 'sometimes|nullable|integer',
            'cause' => 'sometimes|nullable',
            'relate_parts' => 'sometimes|nullable',
            'cause_type' => 'sometimes|nullable|integer',
            'due_date' => 'sometimes|nullable|date',
            'delivery_confirm' => 'sometimes|nullable|boolean',
            'overview_attaches' => 'sometimes|nullable|array|max:3',
            'overview_attaches.*.name' => 'required',
            'overview_attaches.*.url' => 'required',
            'overview_attaches.*.uuid' => 'required',
            'master_overview_attaches' => 'sometimes|nullable|array|max:3',
            'master_overview_attaches.*.name' => 'required',
            'master_overview_attaches.*.url' => 'required',
            'master_overview_attaches.*.uuid' => 'required',
            'detail_attaches' => 'sometimes|nullable|array',
            'detail_attaches.*.name' => 'required',
            'detail_attaches.*.url' => 'required',
            'detail_attaches.*.uuid' => 'required',
            'master_detail_attaches' => 'sometimes|nullable|array|max:3',
            'master_detail_attaches.*.name' => 'required',
            'master_detail_attaches.*.url' => 'required',
            'master_detail_attaches.*.uuid' => 'required',
            'videos' => 'sometimes|nullable|array|max:2',
            'videos.*.name' => 'required',
            'videos.*.url' => 'required',
            'videos.*.uuid' => 'required',
            'videos.*.poster' => 'sometimes|nullable',
            'media' => 'sometimes|nullable|array|max:11',
            'media.*' => 'required'
        ];
        $messages = [
            'status.integer' => 'Issue Status不正确',
            'type.integer' => 'Issue Type/问题类型不正确',
            'defect_level.integer' => '问题等级不正确',
            'is_ppm.boolean' => '是否升级为PPM问题不正确',
            'is_pre_highlight.boolean' => '是否需要Pre-Highlight不正确',
            'detect_area.integer' => 'Detect Area探测区域不正确',
            'quantity.integer' => 'Quantity问题数量不正确',
            'cause_type.integer' => 'Root Cause Type根本原因类型不正确',
            'due_date.date' => 'Due Date不正确',
            'delivery_confirm.boolean' => 'Delivery Confirm/E车辆交付确认/工程师不正确',
            'overview_attaches.array' => '整体图片不正确',
            'overview_attaches.*.name.required' => '整体图片不正确',
            'overview_attaches.*.url.required' => '整体图片不正确',
            'overview_attaches.*.uuid.required' => '整体图片不正确',
            'master_overview_attaches.array' => '工程师整体图片不正确',
            'master_overview_attaches.*.name.required' => '工程师整体图片不正确',
            'master_overview_attaches.*.url.required' => '工程师整体图片不正确',
            'master_overview_attaches.*.uuid.required' => '工程师整体图片不正确',
            'detail_attaches.array' => '细节图片不正确',
            'detail_attaches.*.name.required' => '细节图片不正确',
            'detail_attaches.*.url.required' => '细节图片不正确',
            'detail_attaches.*.uuid.required' => '细节图片不正确',
            'master_detail_attaches.array' => '工程师细节图片不正确',
            'master_detail_attaches.*.name.required' => '工程师细节图片不正确',
            'master_detail_attaches.*.url.required' => '工程师细节图片不正确',
            'master_detail_attaches.*.uuid.required' => '工程师细节图片不正确',
            'videos.array' => '视频不正确',
            'videos.*.name.required' => '视频不正确',
            'videos.*.url.required' => '视频不正确',
            'videos.*.uuid.required' => '视频不正确',
            'media.array' => '附件参数不正确',
            'media.*.required' => '附件参数不正确',
            'overview_attaches.max' => '整体图片不正确',
            'master_overview_attaches.max' => '工程师整体图片不正确',
            'detail_attaches.max' => '细节图片不正确',
            'master_detail_attaches.max' => '工程师细节图片不正确',
            'videos.max' => '视频不正确',
            'media.max' => '附件参数不正确',
            'id.exists' => '问题不存在或已删除',
        ];

        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->all()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'initial_analysis',
            'initial_action',
            'status',
            'type',
            'defect_level',
            'soma',
            'lama',
            'eight_disciplines',
            'ira',
            'is_ppm',
            'is_pre_highlight',
            'detect_area',
            'quantity',
            'cause',
            'relate_parts',
            'cause_type',
            'due_date',
            'delivery_confirm',
            'overview_attaches',
            'master_overview_attaches',
            'detail_attaches',
            'master_detail_attaches',
            'videos',
            'media',
        ]);
        $issueVehicleService->updateVehicle($request->user(), $id, $data);
        return $this->success();
    }

    /**
     * 整车服务-已处理问题
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function finish(Request $request, DictService $dictService): InertiaResponse
    {
        return Inertia::render('Vehicle/Finish', [
            'motorcycle_type' => $dictService->getOptionByCode('motorcycle_type'),
            'car_series' => $dictService->getOptionByCode('car_series'),
            'sensor_point' => $dictService->getOptionByCode('sensor_point'),
            'service_shift' => $dictService->getOptionByCode('service_shift'),
            'eb_type' => $dictService->getOptionByCode('eb_type'),
            'service_factory' => $dictService->getOptionByCode('service_factory'),
            'block_status' => $dictService->getOptionByCode('block_status'),
            'block_content' => $dictService->getOptionByCode('block_content'),
            'issue_status' => $dictService->getOptionByCode('issue_status'),
            'issue_type' => $dictService->getOptionByCode('issue_type'),
            'detect_area' => $dictService->getOptionByCode('detect_area'),
        ]);
    }

    /**
     * 整车服务-动态考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function task(Request $request, DictService $dictService): InertiaResponse
    {
        return Inertia::render('Vehicle/Task', [
            'motorcycle_type' => $dictService->getOptionByCode('motorcycle_type'),
            'car_series' => $dictService->getOptionByCode('car_series'),
            'sensor_point' => $dictService->getOptionByCode('sensor_point'),
            'service_shift' => $dictService->getOptionByCode('service_shift'),
            'eb_type' => $dictService->getOptionByCode('eb_type'),
            'service_factory' => $dictService->getOptionByCode('service_factory'),
            'block_status' => $dictService->getOptionByCode('block_status'),
            'block_content' => $dictService->getOptionByCode('block_content'),
            'issue_status' => $dictService->getOptionByCode('issue_status'),
            'issue_type' => $dictService->getOptionByCode('issue_type'),
            'detect_area' => $dictService->getOptionByCode('detect_area'),
        ]);
    }
}
