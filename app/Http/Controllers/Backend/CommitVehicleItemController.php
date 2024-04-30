<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\Backend\CommitVehicleItemService;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * 考核定义-整车服务-考核项控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class CommitVehicleItemController extends Controller
{

    /**
     * 获取考核定义-整车服务-考核项列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string            $id
     * @param  Request           $request
     * @param  CommitVehicleItemService $commitVehicleItemService
     * @return JsonResponse
     */
    public function list(string $id, Request $request, CommitVehicleItemService $commitVehicleItemService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commit_vehicles,id'
        ];
        $messages = [
            'id.exists' => '当前模板无法查看',
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $result = $commitVehicleItemService->getList($id);
        return $this->success($result);
    }

    /**
     * 添加考核定义-整车服务-考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string          $id
     * @param  Request         $request
     * @param  CommitVehicleItemService $commitVehicleItemService
     * @return JsonResponse
     */
    public function create(string $id, Request $request, CommitVehicleItemService $commitVehicleItemService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commit_vehicles,id,status,0',
            'content' => 'required',
            'content_en' => 'sometimes|nullable',
            'standard' => 'sometimes|nullable',
            'standard_en' => 'sometimes|nullable',
            'other' => 'sometimes|nullable',
            'other_en' => 'sometimes|nullable',
            'type' => 'required|integer',
            'sort_order' => 'required|integer',
            'thumbnail' => 'sometimes|array|nullable',
            'thumbnail.*.uuid' => 'required',
            'thumbnail.*.url' => 'required',
            'thumbnail.*.name' => 'required'
        ];
        $messages = [
            'content.required' => '工作内容不能为空',
            'type.required' => '检查类型不能为空',
            'sort_order.required' => '工作序号不能为空',
            'type.integer' => '检查类型不正确',
            'sort_order.integer' => '工作序号不正确',
            'thumbnail.array' => '参数错误#1',
            'thumbnail.min' => '参数错误#2',
            'thumbnail.*.uuid.required' => '参数错误#3',
            'thumbnail.*.url.required' => '参数错误#4',
            'thumbnail.*.name.required' => '参数错误#5',
            'id.exists' => '考核模板不存在'
        ];
        $validator = Validator::make(array_merge($request->post(), [
            'id' => $id
        ]), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'content',
            'content_en',
            'standard',
            'standard_en',
            'other',
            'other_en',
            'type',
            'sort_order',
            'thumbnail',
        ]);
        $commitVehicleItemService->createByCommitId($id, $data);
        return $this->success();
    }

    /**
     * 更新考核定义-整车服务-考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string            $id
     * @param  string            $item_id
     * @param  Request           $request
     * @param  CommitVehicleItemService $commitVehicleItemService
     * @return JsonResponse
     */
    public function update(string $id, string $item_id, Request $request, CommitVehicleItemService $commitVehicleItemService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commit_vehicles,id,status,0',
            'item_id' => 'exists:commit_vehicle_items,id,commit_vehicle_id,' . $id,
            'content' => 'required',
            'content_en' => 'sometimes|nullable',
            'standard' => 'sometimes|nullable',
            'standard_en' => 'sometimes|nullable',
            'other' => 'sometimes|nullable',
            'other_en' => 'sometimes|nullable',
            'type' => 'required|integer',
            'sort_order' => 'required|integer',
            'thumbnail' => 'sometimes|array|nullable',
            'thumbnail.*.uuid' => 'required',
            'thumbnail.*.url' => 'required',
            'thumbnail.*.name' => 'required'
        ];
        $messages = [
            'content.required' => '工作内容不能为空',
            'type.required' => '检查类型不能为空',
            'sort_order.required' => '工作序号不能为空',
            'type.integer' => '检查类型不正确',
            'sort_order.integer' => '工作序号不正确',
            'thumbnail.array' => '参数错误#1',
            'thumbnail.min' => '参数错误#2',
            'thumbnail.*.uuid.required' => '参数错误#3',
            'thumbnail.*.url.required' => '参数错误#4',
            'thumbnail.*.name.required' => '参数错误#5',
            'id.exists' => '考核模板不存在',
            'item_id.exists' => '考核项不存在'
        ];
        $validator = Validator::make(array_merge($request->post(), [
            'id' => $id,
            'item_id' => $item_id,
        ]), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'content',
            'content_en',
            'standard',
            'standard_en',
            'other',
            'other_en',
            'type',
            'sort_order',
            'thumbnail',
        ]);
        $commitVehicleItemService->updateByCommitId($item_id, $data);
        return $this->success();
    }

    /**
     * 上传考核定义-整车服务-考核项图示
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string                   $id
     * @param  Request                  $request
     * @param  CommitVehicleItemService $commitVehicleItemService
     * @return JsonResponse
     */
    public function upload(string $id, Request $request, CommitVehicleItemService $commitVehicleItemService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commit_vehicles,id,status,0',
            'file' => 'required|image'
        ];
        $messages = [
            'id.exists' => '当前模板无法编辑',
            'file.required' => '文件不能为空',
            'file.image' => '图片文件不合规'
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
        $result = $commitVehicleItemService->upload($data['file']);
        return response()->json([
            'errno' => 0,
            'data' => $result
        ]);
    }

    /**
     * 保存考核定义-整车服务-考核项排序
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string        $id
     * @param  string        $item_id
     * @param  CommitVehicleItemService $commitVehicleItemService
     * @return JsonResponse
     */
    public function order(string $id, Request $request, CommitVehicleItemService $commitVehicleItemService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commit_vehicles,id',
            'list' => 'required|array|min:1',
            'list.*' => 'exists:commit_vehicle_items,id,commit_vehicle_id,' . $id,
        ];
        $messages = [
            'id.exists' => '考核定义不存在',
            'list.required' => '考核项不存在',
            'list.array' => '考核项不存在',
            'list.min' => '考核项不存在',
            'list.*.exists' => '考核项不存在',
        ];
        $validator = Validator::make(array_merge([
            'id' => $id,
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'list'
        ]);
        $result = $commitVehicleItemService->updateOrder($data['list']);
        return $this->success($result);
    }


    /**
     * 删除考核定义-整车服务-考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string        $id
     * @param  string        $item_id
     * @param  CommitVehicleItemService $commitVehicleItemService
     * @return JsonResponse
     */
    public function delete(string $id, string $item_id, CommitVehicleItemService $commitVehicleItemService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commit_vehicles,id',
            'item_id' => 'exists:commit_vehicle_items,id,commit_vehicle_id,' . $id
        ];
        $messages = [
            'id.exists' => '考核定义不存在',
            'item_id.exists' => '考核项不存在',
        ];
        $validator = Validator::make([
            'id' => $id,
            'item_id' => $item_id,
        ], $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $commitVehicleItemService->delete($item_id);
        return $this->success();
    }

}
