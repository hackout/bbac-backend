<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\TaskService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskController extends Controller
{

    /**
     * 整车服务-动态考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  TaskService $taskService
     * @return JsonResponse
     */
    public function vehicle(Request $request, TaskService $taskService): JsonResponse
    {
        $result = $taskService->getVehicleList($request->user());
        return $this->success($result);
    }

    /**
     * 在线考核-考核记录
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  TaskService $taskService
     * @return JsonResponse
     */
    public function inline(Request $request, TaskService $taskService): JsonResponse
    {
        $rules = [
            'sub_type' => 'sometimes|nullable|integer',
            'status' => 'sometimes|nullable|integer'
        ];
        $messages = [
            'sub_type.integer' => __('task.inline.sub_type.integer'),
            'status.integer' => __('task.inline.status.integer'),
        ];
        $data = $request->validate($rules, $messages);
        $result = $taskService->getListByInline($request->user(), $data);
        return $this->success($result);
    }

    /**
     * 产品考核-考核记录
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  TaskService $taskService
     * @return JsonResponse
     */
    public function product(Request $request, TaskService $taskService): JsonResponse
    {
        $rules = [
            'sub_type' => 'sometimes|nullable|integer',
            'status' => 'sometimes|nullable|integer'
        ];
        $messages = [
            'sub_type.integer' => __('task.product.sub_type.integer'),
            'status.integer' => __('task.product.status.integer'),
        ];
        $data = $request->validate($rules, $messages);
        $result = $taskService->getListByProduct($request->user(), $data);
        return $this->success($result);
    }

    /**
     * 在线考核-常规考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  TaskService $taskService
     * @return JsonResponse
     */
    public function inlineStandard(Request $request, TaskService $taskService): JsonResponse
    {
        $result = $taskService->getListByInlineStandard($request->user());
        return $this->success($result);
    }

    /**
     * 在线考核-涂胶考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  TaskService $taskService
     * @return JsonResponse
     */
    public function inlineGluing(Request $request, TaskService $taskService): JsonResponse
    {
        $result = $taskService->getListByInlineGluing($request->user());
        return $this->success($result);
    }

    /**
     * 在线考核-动态考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  TaskService $taskService
     * @return JsonResponse
     */
    public function inlineDynamic(Request $request, TaskService $taskService): JsonResponse
    {
        $result = $taskService->getListByInlineDynamic($request->user());
        return $this->success($result);
    }

    /**
     * 产品考核-拆检考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  TaskService $taskService
     * @return JsonResponse
     */
    public function productOverhaul(Request $request, TaskService $taskService): JsonResponse
    {
        $result = $taskService->getListByProductOverhaul($request->user());
        return $this->success($result);
    }

    /**
     * 产品考核-装配考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  TaskService $taskService
     * @return JsonResponse
     */
    public function productAssembling(Request $request, TaskService $taskService): JsonResponse
    {
        $result = $taskService->getListByProductAssembling($request->user());
        return $this->success($result);
    }

    /**
     * 产品考核-动态考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  TaskService $taskService
     * @return JsonResponse
     */
    public function productDynamic(Request $request, TaskService $taskService): JsonResponse
    {
        $result = $taskService->getListByProductDynamic($request->user());
        return $this->success($result);
    }

    /**
     * 扫码进入
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  TaskService  $taskService
     * @return JsonResponse
     */
    public function productEnter(Request $request, TaskService $taskService): JsonResponse
    {
        $rules = [
            'number' => 'required|exists:products,number',
        ];

        $messages = [
            'number.required' => __('task.product.number.required'),
            'number.exists' => __('task.product.number.exists'),
        ];

        $data = $request->validate($rules, $messages);

        $result = $taskService->getProductEnter($request->user(), $data);

        return $this->success($result);
    }

    /**
     * 查看整车动态考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $id
     * @param  Request      $request
     * @param  TaskService  $taskService
     * @return JsonResponse
     */
    public function vehicleDetail(string $id, Request $request, TaskService $taskService): JsonResponse
    {
        $rules = [
            'id' => 'exists:tasks,id,user_id,' . $request->user()->id
        ];
        $messages = [
            'id.exists' => __('task.id_exists')
        ];
        $validator = Validator::make([
            'id' => $id
        ], $rules, $messages);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $result = $taskService->detailVehicle($request->user(), $id);

        return $this->success($result);

    }

    /**
     * 查看在线考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $id
     * @param  Request      $request
     * @param  TaskService  $taskService
     * @return JsonResponse
     */
    public function inlineDetail(string $id, Request $request, TaskService $taskService): JsonResponse
    {
        $rules = [
            'id' => 'exists_plus:tasks,id,' . $request->user()->id . ',user_id'
        ];
        $messages = [
            'id.exists_plus' => __('task.inline.detail.id.exists_plus')
        ];
        $validator = Validator::make([
            'id' => $id
        ], $rules, $messages);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $result = $taskService->inlineDetail($request->user(), $id);

        return $this->success($result);

    }

    /**
     * 提交在线考核项结果
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $id
     * @param  Request      $request
     * @param  TaskService  $taskService
     * @return JsonResponse
     */
    public function inlineUpdate(string $id, Request $request, TaskService $taskService): JsonResponse
    {
        $rules = [
            'id' => 'exists_plus:tasks,id,' . $request->user()->id . ',user_id',
            'item_id' => 'required|exists_plus:task_items,id,' . $id . ',task_id',
            'content' => 'required|in:0,1',
            'remark' => 'sometimes|nullable|max:200',
            'options' => 'nullable|array',
            'number' => 'required_if:content,0|nullable|max:200',
            'status' => 'required_if:content,0|nullable|integer',
            'scope' => 'required_if:content,0|nullable|max:200',
            'ira' => 'required_if:content,0|nullable|max:200',
            'description' => 'required_if:content,0|nullable|integer',
            'image' => 'required_if:content,0|nullable|array',
            'picture' => 'required_if:content,0|nullable|array',
            'image.*' => 'image',
            'picture.*' => 'image',
        ];
        $messages = [
            'id.exists_plus' => __('task.inline.update.id.exists_plus'),
            'item_id.required' => __('task.inline.update.item_id.required'),
            'item_id.exists_plus' => __('task.inline.update.item_id.exists_plus'),
            'content.required' => __('task.inline.update.content.required'),
            'content.in' => __('task.inline.update.content.in'),
            'remark.max' => __('task.inline.update.remark.max'),
            'options.required_if' => __('task.inline.update.options.required_if'),
            'number.required_if' => __('task.inline.update.number.required_if'),
            'status.required_if' => __('task.inline.update.status.required_if'),
            'scope.required_if' => __('task.inline.update.scope.required_if'),
            'ira.required_if' => __('task.inline.update.ira.required_if'),
            'description.required_if' => __('task.inline.update.description.required_if'),
            'image.required_if' => __('task.inline.update.image.required_if'),
            'picture.required_if' => __('task.inline.update.picture.required_if'),
            'options.array' => __('task.inline.update.options.array'),
            'number.max' => __('task.inline.update.number.max'),
            'status.integer' => __('task.inline.update.status.integer'),
            'scope.max' => __('task.inline.update.scope.max'),
            'ira.max' => __('task.inline.update.ira.max'),
            'description.integer' => __('task.inline.update.description.integer'),
            'image.array' => __('task.inline.update.image.array'),
            'picture.array' => __('task.inline.update.picture.array'),
            'image.*.image' => __('task.inline.update.image.*.image'),
            'picture.*.image' => __('task.inline.update.picture.*.image'),
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->all()), $rules, $messages);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $data = $validator->safe()->only([
            'item_id',
            'content',
            'remark',
            'options',
            'number',
            'status',
            'scope',
            'ira',
            'description',
            'image',
            'picture',
        ]);

        $result = $taskService->inlineUpdate($request->user(), $id, $data);

        return $this->success($result);

    }

    /**
     * 查看产品考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $id
     * @param  Request      $request
     * @param  TaskService  $taskService
     * @return JsonResponse
     */
    public function productDetail(string $id, Request $request, TaskService $taskService): JsonResponse
    {
        $rules = [
            'id' => 'exists_plus:tasks,id,' . $request->user()->id . ',user_id'
        ];
        $messages = [
            'id.exists_plus' => __('task.product.detail.id.exists_plus')
        ];
        $validator = Validator::make([
            'id' => $id
        ], $rules, $messages);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $result = $taskService->productDetail($request->user(), $id);

        return $this->success($result);

    }



    /**
     * 提交产品考核项结果
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $id
     * @param  Request      $request
     * @param  TaskService  $taskService
     * @return JsonResponse
     */
    public function productUpdate(string $id, Request $request, TaskService $taskService): JsonResponse
    {
        $rules = [
            'id' => 'exists_plus:tasks,id,' . $request->user()->id . ',user_id',
            'item_id' => 'required|exists_plus:task_items,id,' . $id . ',task_id',
            'content' => 'required|in:0,1',
            'remark' => 'sometimes|nullable|max:200',
            'options' => 'nullable|array',
            'number' => 'required_if:content,0|nullable|max:200',
            'status' => 'required_if:content,0|nullable|integer',
            'scope' => 'required_if:content,0|nullable|max:200',
            'ira' => 'required_if:content,0|nullable|max:200',
            'description' => 'required_if:content,0|nullable|max:200',
            'image' => 'required_if:content,0|nullable|array',
            'picture' => 'required_if:content,0|nullable|array',
            'image.*' => 'image',
            'picture.*' => 'image',
        ];
        $messages = [
            'id.exists_plus' => __('task.product.update.id.exists_plus'),
            'item_id.required' => __('task.product.update.item_id.required'),
            'item_id.exists_plus' => __('task.product.update.item_id.exists_plus'),
            'content.required' => __('task.product.update.content.required'),
            'content.in' => __('task.product.update.content.in'),
            'remark.max' => __('task.product.update.remark.max'),
            'options.required_if' => __('task.product.update.options.required_if'),
            'number.required_if' => __('task.product.update.number.required_if'),
            'status.required_if' => __('task.product.update.status.required_if'),
            'scope.required_if' => __('task.product.update.scope.required_if'),
            'ira.required_if' => __('task.product.update.ira.required_if'),
            'description.required_if' => __('task.product.update.description.required_if'),
            'image.required_if' => __('task.product.update.image.required_if'),
            'picture.required_if' => __('task.product.update.picture.required_if'),
            'options.array' => __('task.product.update.options.array'),
            'number.max' => __('task.product.update.number.max'),
            'status.integer' => __('task.product.update.status.integer'),
            'scope.max' => __('task.product.update.scope.max'),
            'ira.max' => __('task.product.update.ira.max'),
            'description.integer' => __('task.product.update.description.max'),
            'image.array' => __('task.product.update.image.array'),
            'picture.array' => __('task.product.update.picture.array'),
            'image.*.image' => __('task.product.update.image.*.image'),
            'picture.*.image' => __('task.product.update.picture.*.image'),
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->all()), $rules, $messages);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $data = $validator->safe()->only([
            'item_id',
            'content',
            'remark',
            'options',
            'number',
            'status',
            'scope',
            'ira',
            'description',
            'image',
            'picture',
        ]);

        $result = $taskService->productUpdate($request->user(), $id, $data);

        return $this->success($result);

    }



    /**
     * 提交整车考核考核项结果
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $id
     * @param  Request      $request
     * @param  TaskService  $taskService
     * @return JsonResponse
     */
    public function vehicleUpdate(string $id, Request $request, TaskService $taskService): JsonResponse
    {
        $rules = [
            'id' => 'exists:tasks,id,user_id,' . $request->user()->id,
            'remark' => 'sometimes|nullable|max:250',
            'status' => 'required|in:0,1',
            'number' => 'required|array',
            'number.*.id' => 'required|exists:task_items,id,task_id,' . $id,
            'number.*.number' => 'required|between:1,100',
            'image' => 'required|image',
        ];
        $messages = [
            'id.exists' => __('task.vehicle.update.id.exists'),
            'remark.max' => __('task.vehicle.update.remark.max'),
            'status.required' => __('task.vehicle.update.status.required'),
            'status.in' => __('task.vehicle.update.status.in'),
            'image.required' => __('task.vehicle.update.image.required'),
            'image.image' => __('task.vehicle.update.image.image'),
            'number.required' => __('task.vehicle.update.number.required'),
            'number.array' => __('task.vehicle.update.number.array'),
            'number.*.id.required' => __('task.vehicle.update.number_id.required'),
            'number.*.id.exists' => __('task.vehicle.update.number_id.exists'),
            'number.*.number.required' => __('task.vehicle.update.number_number.required'),
            'number.*.number.between' => __('task.vehicle.update.number_number.between'),
        ];
        $_data = $request->all();
        if (array_key_exists('number', $_data)) {
            $_data['number'] = json_decode($_data['number'], true);
        }
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $_data), $rules, $messages);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $data = $validator->safe()->only([
            'remark',
            'status',
            'number',
            'image',
        ]);

        $result = $taskService->vehicleUpdate($request->user(), $id, $data);

        return $this->success($result);

    }


    public function vehicleAll(string $id, Request $request, TaskService $taskService): JsonResponse
    {
        $rules = [
            'id' => 'exists_plus:tasks,id,3,type',
            'item_id' => 'exists_plus:task_items,id,' . $id . ',task_id'
        ];
        $messages = [
            'id.exists_plus' => __('task.vehicle_all.id.exists_plus'),
            'item_id.exists_plus' => __('task.vehicle_all.item_id.exists_plus')
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'item_id'
        ]);

        $result = $taskService->vehicleAll($request->user(), $id, $data);
        return $this->success($result);
    }

    public function inlineAll(string $id, Request $request, TaskService $taskService): JsonResponse
    {
        $rules = [
            'id' => 'exists_plus:tasks,id,3,type',
            'item_id' => 'exists_plus:task_items,id,' . $id . ',task_id'
        ];
        $messages = [
            'id.exists_plus' => __('task.vehicle_all.id.exists_plus'),
            'item_id.exists_plus' => __('task.vehicle_all.item_id.exists_plus')
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'item_id'
        ]);

        $result = $taskService->inlineAll($request->user(), $id, $data);
        return $this->success($result);
    }

    public function productAll(string $id, Request $request, TaskService $taskService): JsonResponse
    {
        $rules = [
            'id' => 'exists_plus:tasks,id,3,type',
            'item_id' => 'exists_plus:task_items,id,' . $id . ',task_id'
        ];
        $messages = [
            'id.exists_plus' => __('task.vehicle_all.id.exists_plus'),
            'item_id.exists_plus' => __('task.vehicle_all.item_id.exists_plus')
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'item_id'
        ]);

        $result = $taskService->productAll($request->user(), $id, $data);
        return $this->success($result);
    }

    public function vehicleReset(string $id, Request $request, TaskService $taskService): JsonResponse
    {
        $rules = [
            'id' => 'exists_plus:tasks,id,3,type',
            'item_id' => 'exists_plus:task_items,id,' . $id . ',task_id'
        ];
        $messages = [
            'id.exists_plus' => __('task.vehicle_all.id.exists_plus'),
            'item_id.exists_plus' => __('task.vehicle_all.item_id.exists_plus')
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'item_id'
        ]);

        $result = $taskService->vehicleReset($request->user(), $id, $data);
        return $this->success($result);
    }

    public function inlineReset(string $id, Request $request, TaskService $taskService): JsonResponse
    {
        $rules = [
            'id' => 'exists_plus:tasks,id,3,type',
            'item_id' => 'exists_plus:task_items,id,' . $id . ',task_id'
        ];
        $messages = [
            'id.exists_plus' => __('task.vehicle_all.id.exists_plus'),
            'item_id.exists_plus' => __('task.vehicle_all.item_id.exists_plus')
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'item_id'
        ]);

        $result = $taskService->inlineReset($request->user(), $id, $data);
        return $this->success($result);
    }

    public function productReset(string $id, Request $request, TaskService $taskService): JsonResponse
    {
        $rules = [
            'id' => 'exists_plus:tasks,id,3,type',
            'item_id' => 'exists_plus:task_items,id,' . $id . ',task_id'
        ];
        $messages = [
            'id.exists_plus' => __('task.vehicle_all.id.exists_plus'),
            'item_id.exists_plus' => __('task.vehicle_all.item_id.exists_plus')
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'item_id'
        ]);

        $result = $taskService->productReset($request->user(), $id, $data);
        return $this->success($result);
    }

    public function vehicleItemDetail(string $id, Request $request, TaskService $taskService): JsonResponse
    {
        $rules = [
            'id' => 'exists_plus:tasks,id,3,type',
            'item_id' => 'exists_plus:task_items,id,' . $id . ',task_id'
        ];
        $messages = [
            'id.exists_plus' => __('task.vehicle_all.id.exists_plus'),
            'item_id.exists_plus' => __('task.vehicle_all.item_id.exists_plus')
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'item_id'
        ]);

        $result = $taskService->vehicleItemDetail($request->user(), $id, $data);
        return $this->success($result);
    }

    public function inlineItemDetail(string $id, Request $request, TaskService $taskService): JsonResponse
    {
        $rules = [
            'id' => 'exists_plus:tasks,id,3,type',
            'item_id' => 'exists_plus:task_items,id,' . $id . ',task_id'
        ];
        $messages = [
            'id.exists_plus' => __('task.vehicle_all.id.exists_plus'),
            'item_id.exists_plus' => __('task.vehicle_all.item_id.exists_plus')
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'item_id'
        ]);

        $result = $taskService->inlineItemDetail($request->user(), $id, $data);
        return $this->success($result);
    }

    public function productItemDetail(string $id, Request $request, TaskService $taskService): JsonResponse
    {
        $rules = [
            'id' => 'exists_plus:tasks,id,3,type',
            'item_id' => 'exists_plus:task_items,id,' . $id . ',task_id'
        ];
        $messages = [
            'id.exists_plus' => __('task.vehicle_all.id.exists_plus'),
            'item_id.exists_plus' => __('task.vehicle_all.item_id.exists_plus')
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'item_id'
        ]);

        $result = $taskService->productItemDetail($request->user(), $id, $data);
        return $this->success($result);
    }

}
