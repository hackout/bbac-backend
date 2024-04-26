<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\IssueService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class IssueController extends Controller
{

    public function vehicle(Request $request, IssueService $issueService): JsonResponse
    {
        $rules = [
            'shift' => 'required|integer',
            'eb_type' => 'required|integer',
            'plant' => 'required|integer',
            'sensor_point' => 'required|integer',
            'car_series' => 'required|integer',
            'motorcycle_type' => 'required|integer',
            'pn_number' => 'required',
            'number' => 'required',
            'delay' => 'sometimes|nullable|boolean',
            'description' => 'sometimes|nullable|max:200',
            'reason' => 'sometimes|nullable|max:200',
            'picture' => 'sometimes|nullable|array',
            'picture.*' => 'image',
            'image' => 'sometimes|nullable|array',
            'image.*' => 'image',
            'video' => 'sometimes|nullable|array',
            'video.*' => 'mimetypes:video/avi,video/mpeg,video/quicktime,video/mp4'
        ];

        $messages = [
            'shift.required' => __('issue.vehicle.shift.required'),
            'eb_type.required' => __('issue.vehicle.eb_type.required'),
            'plant.required' => __('issue.vehicle.plant.required'),
            'sensor_point.required' => __('issue.vehicle.sensor_point.required'),
            'car_series.required' => __('issue.vehicle.car_series.required'),
            'motorcycle_type.required' => __('issue.vehicle.motorcycle_type.required'),
            'pn_number.required' => __('issue.vehicle.pn_number.required'),
            'number.required' => __('issue.vehicle.number.required'),
            'shift.integer' => __('issue.vehicle.shift.integer'),
            'eb_type.integer' => __('issue.vehicle.eb_type.integer'),
            'plant.integer' => __('issue.vehicle.plant.integer'),
            'sensor_point.integer' => __('issue.vehicle.sensor_point.integer'),
            'car_series.integer' => __('issue.vehicle.car_series.integer'),
            'motorcycle_type.integer' => __('issue.vehicle.motorcycle_type.integer'),
            'delay.delay' => __('issue.vehicle.delay.delay'),
            'description.max' => __('issue.vehicle.description.max'),
            'reason.max' => __('issue.vehicle.reason.max'),
            'picture.array' => __('issue.vehicle.picture.array'),
            'image.array' => __('issue.vehicle.image.array'),
            'video.array' => __('issue.vehicle.video.array'),
            'picture.*.image' => __('issue.vehicle.picture.*.image'),
            'image.*.image' => __('issue.vehicle.image.*.image'),
            'video.*.mimetypes' => __('issue.vehicle.video.*.mimetypes'),
        ];

        $data = $request->validate($rules, $messages);
        $issueService->createVehicle($request->user(), $data);
        return $this->success();
    }

    /**
     * 整车服务-问题追踪
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  IssueService $issueService
     * @return JsonResponse
     */
    public function getVehicle(Request $request, IssueService $issueService): JsonResponse
    {
        $rules = [
            'keyword' => 'sometimes|nullable',
            'start' => 'sometimes|nullable|date',
            'end' => 'sometimes|nullable|date',
        ];

        $messages = [
            'start.date' => __('issue.vehicle.start.date'),
            'end.date' => __('issue.vehicle.end.date')
        ];

        $data = $request->validate($rules, $messages);
        $result = $issueService->getListByVehicle($data);
        return $this->success($result);
    }


    /**
     * 整车服务-滞留车记录
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  IssueService $issueService
     * @return JsonResponse
     */
    public function getVehicleBlock(Request $request, IssueService $issueService): JsonResponse
    {
        $rules = [
            'keyword' => 'sometimes|nullable',
            'start' => 'sometimes|nullable|date',
            'end' => 'sometimes|nullable|date',
        ];

        $messages = [
            'start.date' => __('issue.vehicle.start.date'),
            'end.date' => __('issue.vehicle.end.date')
        ];

        $data = $request->validate($rules, $messages);
        $result = $issueService->getListByBlock($data);
        return $this->success($result);
    }

    /**
     * 整车问题详情
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $id
     * @param  Request      $request
     * @param  IssueService $issueService
     * @return JsonResponse
     */
    public function getVehicleDetail(string $id, Request $request, IssueService $issueService): JsonResponse
    {
        $rules = [
            'id' => 'exists_plus:issues,id,3,type',
        ];

        $messages = [
            'id.exists_plus' => __('issue.vehicle.id.exists_plus')
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->input()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $result = $issueService->getDetailByVehicle($id);
        return $this->success($result);
    }

    /**
     * 整车问题-更新内容
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $id
     * @param  Request      $request
     * @param  IssueService $issueService
     * @return JsonResponse
     */
    public function updateVehicle(string $id, Request $request, IssueService $issueService): JsonResponse
    {
        $rules = [
            'shift' => 'required|integer',
            'eb_type' => 'required|integer',
            'plant' => 'required|integer',
            'sensor_point' => 'required|integer',
            'car_series' => 'required|integer',
            'motorcycle_type' => 'required|integer',
            'pn_number' => 'required',
            'number' => 'required',
            'delay' => 'sometimes|nullable|boolean',
            'description' => 'sometimes|nullable|max:200',
            'reason' => 'sometimes|nullable|max:200',
            'media' => 'sometimes|nullable|array',
            'media.*' => 'uuid',
            'picture' => 'sometimes|nullable|array',
            'picture.*' => 'image',
            'image' => 'sometimes|nullable|array',
            'image.*' => 'image',
            'video' => 'sometimes|nullable|array',
            'video.*' => 'mimetypes:video/avi,video/mpeg,video/quicktime,video/mp4'
        ];

        $messages = [
            'shift.required' => __('issue.vehicle.shift.required'),
            'eb_type.required' => __('issue.vehicle.eb_type.required'),
            'plant.required' => __('issue.vehicle.plant.required'),
            'sensor_point.required' => __('issue.vehicle.sensor_point.required'),
            'car_series.required' => __('issue.vehicle.car_series.required'),
            'motorcycle_type.required' => __('issue.vehicle.motorcycle_type.required'),
            'pn_number.required' => __('issue.vehicle.pn_number.required'),
            'number.required' => __('issue.vehicle.number.required'),
            'shift.integer' => __('issue.vehicle.shift.integer'),
            'eb_type.integer' => __('issue.vehicle.eb_type.integer'),
            'plant.integer' => __('issue.vehicle.plant.integer'),
            'sensor_point.integer' => __('issue.vehicle.sensor_point.integer'),
            'car_series.integer' => __('issue.vehicle.car_series.integer'),
            'motorcycle_type.integer' => __('issue.vehicle.motorcycle_type.integer'),
            'delay.delay' => __('issue.vehicle.delay.delay'),
            'description.max' => __('issue.vehicle.description.max'),
            'reason.max' => __('issue.vehicle.reason.max'),
            'media.array' => __('issue.vehicle.media.array'),
            'picture.array' => __('issue.vehicle.picture.array'),
            'image.array' => __('issue.vehicle.image.array'),
            'video.array' => __('issue.vehicle.video.array'),
            'media.*.uuid' => __('issue.vehicle.media.*.uuid'),
            'picture.*.image' => __('issue.vehicle.picture.*.image'),
            'image.*.image' => __('issue.vehicle.image.*.image'),
            'video.*.mimetypes' => __('issue.vehicle.video.*.mimetypes'),
        ];
        $data = $request->validate($rules,$messages);
        $issueService->updateVehicle($request->user(), $id, $data);
        return $this->success();
    }

    public function updateVehicleBlock(string $id,Request $request,IssueService $issueService):JsonResponse
    {
        $rules = [
            'id' => 'exists_plus:issues,id,3,type',
            'block_status' => 'required|integer',
            'block_content' => 'required_if:block_status,2|nullable|integer'
        ];
        $messages = [
            'id.exists_plus' => __('issue.vehicle_block.id.exists_plus'),
            'block_status.required' => __('issue.vehicle_block.block_status.required'),
            'block_status.integer' => __('issue.vehicle_block.block_status.integer'),
            'block_content.required_if' => __('issue.vehicle_block.block_content.required_if'),
            'block_content.integer' => __('issue.vehicle_block.block_content.integer'),
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
        $issueService->updateVehicleBlock($request->user(), $id, $data);
        return $this->success();
    }
}
