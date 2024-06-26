<?php

namespace App\Http\Controllers\Backend;

use App\Services\Backend\TaskService;
use App\Services\Backend\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Backend\DictService;
use App\Services\Backend\ExamineService;
use Inertia\Response as InertiaResponse;
use App\Services\Backend\IssueVehicleService;
use App\Services\Backend\VehicleTargetService;
use App\Services\Backend\VehicleOutboundService;
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
            'vehicle_issue_type' => $dictService->getOptionByCode('vehicle_issue_type'),
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
            'vehicle_issue_type' => $dictService->getOptionByCode('vehicle_issue_type'),
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
            'vehicle_issue_type' => $dictService->getOptionByCode('vehicle_issue_type'),
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
            'issue_type' => 'sometimes|nullable|integer',
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
            'issue_type.integer' => '问题标注不正确',
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
            'issue_type'
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
     * 整车服务-每日发运量
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @param  DictService $dictService
     * @param  VehicleOutboundService $service
     * @return InertiaResponse
     */
    public function outbound(Request $request, DictService $dictService, VehicleOutboundService $service): InertiaResponse
    {
        $month = $request->get('month', today()->firstOfMonth()->format('Y-m'));
        return Inertia::render('Vehicle/OutBound', [
            'month' => $month,
            'eb_type' => $dictService->getOptionByCode('eb_type'),
            'items' => $service->getList($month)
        ]);
    }

    /**
     * 整车服务-PPM Target
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @param  DictService $dictService
     * @param  VehicleTargetService $service
     * @return InertiaResponse
     */
    public function target(Request $request, DictService $dictService, VehicleTargetService $service): InertiaResponse
    {
        return Inertia::render('Vehicle/Target', [
            'eb_type' => $dictService->getOptionByCode('eb_type'),
            'items' => $service->getList()
        ]);
    }

    /**
     * 保存每日发运量
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return RedirectResponse
     */
    public function outboundUpdate(Request $request, VehicleOutboundService $service): RedirectResponse
    {
        $rules = [
            'daily' => 'required',
            'outbound' => 'array'
        ];
        $messages = [
            'daily.required' => '请输入日期',
            'outbound.array' => '请输入发运量'
        ];

        $data = $request->validate($rules, $messages);
        $service->saveDaily($data);
        return back()->with('success', '保存信息成功');
    }

    /**
     * 保存PPM Target
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @param  VehicleTargetService $service
     * @return RedirectResponse
     */
    public function targetUpdate(Request $request, VehicleTargetService $service): RedirectResponse
    {
        $rules = [
            'yearly' => 'required',
            'eb_type' => 'integer',
            'target' => 'integer'
        ];
        $messages = [
            'yearly.required' => '请输入年份',
            'eb_type.integer' => '请选择机型',
            'target.integer' => '请输入发运量'
        ];

        $data = $request->validate($rules, $messages);
        $service->saveYearly($data);
        return back()->with('success', '保存信息成功');
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
            'defect_level' => $dictService->getOptionByCode('defect_level'),
            'defect_category' => $dictService->getOptionByCode('defect_category'),
            'task_status' => $dictService->getOptionByCode('task_status'),
            'engine_type' => $dictService->getOptionByCode('engine_type'),
            'examine_vehicle_item_type' => $dictService->getOptionByCode('examine_vehicle_item_type'),
            'examine_type' => $dictService->getOptionByCode('examine_type'),
            'status' => $dictService->getOptionByCode('assembly_status'),
            'plant' => $dictService->getOptionByCode('plant'),
            'line' => $dictService->getOptionByCode('assembly_line'),
            'users' => (new UserService())->getUserByVehicle($request->user())
        ]);
    }

    /**
     * 获取整车服务动态考核列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  TaskService  $taskService
     * @return JsonResponse
     */
    public function taskList(Request $request, TaskService $taskService): JsonResponse
    {
        $rules = [
            'keyword' => 'sometimes|nullable',
            'engine' => 'sometimes|nullable|integer',
            'status' => 'sometimes|nullable|integer'
        ];
        $messages = [
            'engine.integer' => '机型参数错误',
            'status.integer' => '状态参数错误'
        ];
        $data = $request->validate($rules, $messages);
        $result = $taskService->getVehicleList($request->user(), $data);
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
    public function taskDetail(string $id, Request $request, DictService $dictService, TaskService $taskService): InertiaResponse
    {
        $rules = [
            'id' => 'exists:tasks,id,type,3',
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
        $item = $taskService->getVehicleDetail($request->user(), $id);
        return Inertia::render('Vehicle/TaskDetail', [
            'item' => $item,
            'defect_level' => $dictService->getOptionByCode('defect_level'),
            'defect_category' => $dictService->getOptionByCode('defect_category'),
            'task_status' => $dictService->getOptionByCode('task_status'),
            'engine_type' => $dictService->getOptionByCode('engine_type'),
            'purpose' => $dictService->getOptionByCode('purpose'),
            'assembly_line' => $dictService->getOptionByCode('assembly_line'),
        ]);
    }

    public function taskAssign(Request $request, TaskService $taskService): JsonResponse
    {
        $rules = [
            'task_id' => 'required|exists:tasks,id,type,3',
            'user_id' => 'required|exists:users,id',
            'period' => 'required|between:0,100'
        ];
        $messages = [
            'task_id.required' => '考核单不能为空',
            'user_id.required' => '员工不能为空',
            'period.required' => '分配工时不能为空',
            'task_id.exists' => '考核单不存在或已删除',
            'user_id.exists' => '员工不存在或已删除',
            'period.between' => '分配工时错误'
        ];
        $data = $request->validate($rules, $messages);
        $taskService->assignVehicle($request->user(), $data);
        return $this->success();
    }

    /**
     * 整车服务-动态考核编辑
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string                       $id
     * @param  Request                      $request
     * @param  DictService                  $dictService
     * @param  TaskService                  $taskService
     * @return InertiaResponse|JsonResponse
     */
    public function taskEdit(string $id, Request $request, DictService $dictService, TaskService $taskService): InertiaResponse|JsonResponse
    {
        if ($request->ajax() && $request->method() == 'PUT') {
            $rules = [
                'id' => 'exists:tasks,id,type,3',
                'line' => 'required|integer',
                'engine' => 'required|integer',
                'purpose' => 'required|integer',
                'eight' => 'sometimes|nullable',
                'level' => 'sometimes|nullable|integer',
                'eb_number' => 'sometimes|nullable',
                'description' => 'sometimes|nullable|max:500',
                'resp' => 'sometimes|nullable|max:500',
                'next' => 'sometimes|nullable|max:750',
                'thumbnails' => 'sometimes|nullable|array',
                'thumbnails.*.name' => 'required',
                'thumbnails.*.url' => 'required',
                'thumbnails.*.uuid' => 'required',
                'media' => 'sometimes|nullable|array|max:5',
            ];
            $messages = [
                'id.exists' => '考核记录不存在或已删除',
                'line.required' => '生产线不能为空',
                'engine.required' => '机型不能为空',
                'purpose.required' => 'Purpose不能为空',
                'line.integer' => '生产线不正确',
                'engine.integer' => '机型不正确',
                'purpose.integer' => 'Purpose不正确',
                'level.integer' => '问题等级不正确',
                'description.max' => '问题描述最大支持500个字符',
                'resp.max' => '责任部门/人最大支持500个字符',
                'next.max' => 'Next Step最大支持750个字符',
                'thumbnails.array' => '关联图片不正确',
                'thumbnails.*.name.required' => '关联图片不正确',
                'thumbnails.*.url.required' => '关联图片不正确',
                'thumbnails.*.uuid.required' => '关联图片不正确',
                'media.array' => '附件参数不正确',
                'media.*.required' => '附件参数不正确',
            ];

            $validator = Validator::make(array_merge([
                'id' => $id
            ], $request->all()), $rules, $messages);
            if ($validator->fails()) {
                return $this->error($validator->errors()->first());
            }
            $data = $validator->safe()->only([
                'line',
                'engine',
                'purpose',
                'level',
                'description',
                'resp',
                'eight',
                'eb_number',
                'next',
                'thumbnails',
                'media'
            ]);
            $taskService->updateVehicle($request->user(), $id, $data);
            return $this->success();
        }
        $rules = [
            'id' => 'exists:tasks,id,type,3',
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
        $item = $taskService->getVehicleDetail($request->user(), $id);
        return Inertia::render('Vehicle/TaskEdit', [
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
     * 上传图片到整车服务动态考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string              $id
     * @param  Request             $request
     * @param  TaskService $taskService
     * @return JsonResponse
     */
    public function taskUpload(string $id, Request $request, TaskService $taskService): JsonResponse
    {
        $rules = [
            'id' => 'exists:tasks,id,type,3',
            'file' => 'required|image'
        ];
        $messages = [
            'id.exists' => '当前模板无法编辑',
            'file.required' => '文件不能为空',
            'file.image' => '文件不合规'
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
        $result = $taskService->upload($data['file']);
        return response()->json([
            'errno' => 0,
            'data' => $result
        ]);
    }

    /**
     * 删除整车服务-动态考核单
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $id
     * @param  Request      $request
     * @param  TaskService  $taskService
     * @return JsonResponse
     */
    public function taskDelete(string $id, Request $request, TaskService $taskService): JsonResponse
    {
        $rules = [
            'id' => 'exists:tasks,id,type,3',
        ];
        $messages = [
            'id.exists' => '考核记录不存在或已删除',
        ];

        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->all()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $taskService->deleteVehicle($request->user(), $id);
        return $this->success();
    }
}
