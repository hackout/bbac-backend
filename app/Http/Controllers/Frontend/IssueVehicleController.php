<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Services\Frontend\IssueVehicleService;
use Symfony\Component\HttpFoundation\JsonResponse;

class IssueVehicleController extends Controller
{

    /**
     * 整车服务-问题提交
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  IssueVehicleService $issueVehicleService
     * @return JsonResponse
     */
    public function create(Request $request, IssueVehicleService $issueVehicleService): JsonResponse
    {
        $rules = [
            'shift' => 'required|integer',
            'eb_type' => 'required|integer',
            'plant' => 'required|integer',
            'sensor' => 'required|integer',
            'car_line' => 'required|integer',
            'car_type' => 'required|integer',
            'product_number' => 'required',
            'eb_number' => 'required|unique:issue_vehicles,eb_number',
            'is_block' => 'sometimes|nullable|boolean',
            'description' => 'sometimes|nullable',
            'initial_analysis' => 'sometimes|nullable',
            'overview' => 'sometimes|nullable|array',
            'overview.*' => 'image',
            'picture' => 'sometimes|nullable|array',
            'picture.*' => 'image',
            'detail' => 'sometimes|nullable|array',
            'detail.*' => 'image',
            'video' => 'sometimes|nullable|array',
            'video.*' => 'mimetypes:video/avi,video/mpeg,video/quicktime,video/mp4'
        ];

        $messages = [
            'shift.required' => __('issue_vehicle.shift.required'),
            'eb_type.required' => __('issue_vehicle.eb_type.required'),
            'plant.required' => __('issue_vehicle.plant.required'),
            'sensor.required' => __('issue_vehicle.sensor.required'),
            'car_line.required' => __('issue_vehicle.car_line.required'),
            'car_type.required' => __('issue_vehicle.car_type.required'),
            'product_number.required' => __('issue_vehicle.product_number.required'),
            'eb_number.required' => __('issue_vehicle.eb_number.required'),
            'eb_number.unique' => __('issue_vehicle.eb_number.unique'),
            'shift.integer' => __('issue_vehicle.shift.integer'),
            'eb_type.integer' => __('issue_vehicle.eb_type.integer'),
            'plant.integer' => __('issue_vehicle.plant.integer'),
            'sensor.integer' => __('issue_vehicle.sensor.integer'),
            'car_line.integer' => __('issue_vehicle.car_line.integer'),
            'car_type.integer' => __('issue_vehicle.car_type.integer'),
            'is_block.boolean' => __('issue_vehicle.is_block.boolean'),
            'overview.array' => __('issue_vehicle.picture.array'),
            'detail.array' => __('issue_vehicle.detail.array'),
            'video.array' => __('issue_vehicle.video.array'),
            'overview.*.image' => __('issue_vehicle.picture.*.image'),
            'detail.*.image' => __('issue_vehicle.detail.*.image'),
            'video.*.mimetypes' => __('issue_vehicle.video.*.mimetypes'),
        ];

        $data = $request->validate($rules, $messages);
        $issueVehicleService->createVehicle($request->user(), $data);
        return $this->success();
    }

    /**
     * 整车服务-问题追踪
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  IssueVehicleService $issueVehicleService
     * @return JsonResponse
     */
    public function list(Request $request, IssueVehicleService $issueVehicleService): JsonResponse
    {
        $rules = [
            'keyword' => 'sometimes|nullable',
            'start' => 'sometimes|nullable|date',
            'end' => 'sometimes|nullable|date',
        ];

        $messages = [
            'start.date' => __('issue_vehicle.list.start.date'),
            'end.date' => __('issue_vehicle.list.end.date')
        ];

        $data = $request->validate($rules, $messages);
        $result = $issueVehicleService->getList($data);
        return $this->success($result);
    }

    /**
     * 整车服务-问题详情
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $id
     * @param  Request      $request
     * @param  IssueVehicleService $issueVehicleService
     * @return JsonResponse
     */
    public function detail(string $id, Request $request, IssueVehicleService $issueVehicleService): JsonResponse
    {
        $rules = [
            'id' => 'exists:issue_vehicles,id',
        ];

        $messages = [
            'id.exists' => __('issue_vehicle.id_exists')
        ];
        $validator = Validator::make([
            'id' => $id
        ], $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $result = $issueVehicleService->detail($id);
        return $this->success($result);
    }

    /**
     * 整车问题-修改问题
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $id
     * @param  Request      $request
     * @param  IssueVehicleService $issueVehicleService
     * @return JsonResponse
     */
    public function update(string $id, Request $request, IssueVehicleService $issueVehicleService): JsonResponse
    {
        $rules = [
            'id' => 'exists:issue_vehicles,id',
            'shift' => 'required|integer',
            'eb_type' => 'required|integer',
            'plant' => 'required|integer',
            'sensor' => 'required|integer',
            'car_line' => 'required|integer',
            'car_type' => 'required|integer',
            'product_number' => 'required',
            'eb_number' => 'required|unique:issue_vehicles,eb_number,' . $id . ',id',
            'is_block' => 'sometimes|nullable|boolean',
            'description' => 'sometimes|nullable',
            'initial_analysis' => 'sometimes|nullable',
            'media' => 'sometimes|nullable|array',
            'media.*' => 'exists:media,uuid,model_id,' . $id,
            'overview' => 'sometimes|nullable|array',
            'overview.*' => 'image',
            'detail' => 'sometimes|nullable|array',
            'detail.*' => 'image',
            'video' => 'sometimes|nullable|array',
            'video.*' => 'mimetypes:video/avi,video/mpeg,video/quicktime,video/mp4'
        ];
        $messages = [
            'shift.required' => __('issue_vehicle.shift.required'),
            'eb_type.required' => __('issue_vehicle.eb_type.required'),
            'plant.required' => __('issue_vehicle.plant.required'),
            'sensor.required' => __('issue_vehicle.sensor.required'),
            'car_line.required' => __('issue_vehicle.car_line.required'),
            'car_type.required' => __('issue_vehicle.car_type.required'),
            'product_number.required' => __('issue_vehicle.product_number.required'),
            'eb_number.required' => __('issue_vehicle.eb_number.required'),
            'eb_number.unique' => __('issue_vehicle.eb_number.unique'),
            'shift.integer' => __('issue_vehicle.shift.integer'),
            'eb_type.integer' => __('issue_vehicle.eb_type.integer'),
            'plant.integer' => __('issue_vehicle.plant.integer'),
            'sensor.integer' => __('issue_vehicle.sensor.integer'),
            'car_line.integer' => __('issue_vehicle.car_line.integer'),
            'car_type.integer' => __('issue_vehicle.car_type.integer'),
            'is_block.boolean' => __('issue_vehicle.is_block.boolean'),
            'overview.array' => __('issue_vehicle.overview.array'),
            'media.array' => __('issue_vehicle.media.array'),
            'detail.array' => __('issue_vehicle.detail.array'),
            'video.array' => __('issue_vehicle.video.array'),
            'media.*.exists' => __('issue_vehicle.media.*.exists'),
            'overview.*.image' => __('issue_vehicle.overview.*.image'),
            'detail.*.image' => __('issue_vehicle.detail.*.image'),
            'video.*.mimetypes' => __('issue_vehicle.video.*.mimetypes'),
            'id.exists' => __('issue_vehicle.id_exists')
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->all()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'shift',
            'eb_type',
            'plant',
            'sensor',
            'car_line',
            'car_type',
            'product_number',
            'eb_number',
            'is_block',
            'description',
            'initial_analysis',
            'media',
            'overview',
            'detail',
            'video',
        ]);
        $issueVehicleService->updateVehicle($request->user(), $id, $data);
        return $this->success();
    }

    /**
     * 整车服务-同步视频截图
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string              $id
     * @param  Request             $request
     * @param  IssueVehicleService $issueVehicleService
     * @return JsonResponse
     */
    public function poster(string $id, Request $request, IssueVehicleService $issueVehicleService): JsonResponse
    {
        $rules = [
            'id' => 'exists:issue_vehicles,id',
            'uuid' => 'required|exists:media,uuid,model_id,' . $id,
            'poster' => 'required',
        ];
        $messages = [
            'id.exists' => __('issue_vehicle.list.start.date'),
            'uuid.required' => __('issue_vehicle.poster.uuid.required'),
            'uuid.exists' => __('issue_vehicle.poster.uuid.exists'),
            'poster.required' => __('issue_vehicle.poster.poster.required'),
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'uuid',
            'poster',
        ]);
        $issueVehicleService->poster($request->user(), $data);
        return $this->success();
    }

    
    /**
     * 整车服务-滞留车记录
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  IssueVehicleService $issueVehicleService
     * @return JsonResponse
     */
    public function block(Request $request, IssueVehicleService $issueVehicleService): JsonResponse
    {
        $rules = [
            'keyword' => 'sometimes|nullable',
            'start' => 'sometimes|nullable|date',
            'end' => 'sometimes|nullable|date',
        ];

        $messages = [
            'start.date' => __('issue_vehicle.list.start.date'),
            'end.date' => __('issue_vehicle.list.end.date')
        ];

        $data = $request->validate($rules, $messages);
        $result = $issueVehicleService->block($data);
        return $this->success($result);
    }

    /**
     * 整车服务-修改滞留车状态
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string              $id
     * @param  Request             $request
     * @param  IssueVehicleService $issueVehicleService
     * @return JsonResponse
     */
    public function updateBlock(string $id, Request $request, IssueVehicleService $issueVehicleService): JsonResponse
    {
        $rules = [
            'id' => 'exists:issue_vehicles,id',
            'block_status' => 'required|integer',
            'block_content' => 'required_if:block_status,2|nullable|integer'
        ];
        $messages = [
            'id.exists' => __('issue_vehicle.block.id.exists'),
            'block_status.required' => __('issue_vehicle.block.block_status.required'),
            'block_status.integer' => __('issue_vehicle.block.block_status.integer'),
            'block_content.required_if' => __('issue_vehicle.block.block_content.required_if'),
            'block_content.integer' => __('issue_vehicle.block.block_content.integer'),
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'block_status',
            'block_content'
        ]);
        $issueVehicleService->updateBlock($request->user(), $id, $data);
        return $this->success();
    }
}
