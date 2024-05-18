<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\Backend\CommitProductItemService;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * 考核定义-产品考核-考核项控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class CommitProductItemController extends Controller
{

    /**
     * 获取考核定义-产品考核-考核项列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string            $id
     * @param  Request           $request
     * @param  CommitProductItemService $commitProductItemService
     * @return JsonResponse
     */
    public function list(string $id, Request $request, CommitProductItemService $commitProductItemService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commit_products,id'
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
        $result = $commitProductItemService->getList($id);
        return $this->success($result);
    }

    /**
     * 添加考核定义-产品考核-考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string          $id
     * @param  Request         $request
     * @param  CommitProductItemService $commitProductItemService
     * @return JsonResponse
     */
    public function create(string $id, Request $request, CommitProductItemService $commitProductItemService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commit_products,id,status,0',
            'part_id' => 'sometimes|nullable|exists:parts,id',
            'name' => 'sometimes|nullable',
            'name_en' => 'sometimes|nullable',
            'content' => 'required',
            'content_en' => 'sometimes|nullable',
            'standard' => 'sometimes|nullable',
            'standard_en' => 'sometimes|nullable',
            'eye' => 'sometimes|nullable',
            'eye_en' => 'sometimes|nullable',
            'number' => 'sometimes|nullable|integer',
            'lower_limit' => 'sometimes|nullable',
            'upper_limit' => 'sometimes|nullable',
            'unit' => 'sometimes|nullable',
            'torque' => 'sometimes|nullable',
            'is_scan' => 'sometimes|nullable|boolean',
            'is_camera' => 'sometimes|nullable|boolean',
            'is_ds' => 'sometimes|nullable|boolean',
            'scan' => 'sometimes|nullable',
            'camera' => 'sometimes|nullable',
            'record' => 'sometimes|nullable',
            'process' => 'sometimes|nullable|integer',
            'type' => 'sometimes|nullable|integer',
            'sort_order' => 'sometimes|nullable|integer',
            'thumbnail' => 'sometimes|array|nullable',
            'thumbnail.*.uuid' => 'required',
            'thumbnail.*.url' => 'required',
            'thumbnail.*.name' => 'required'
        ];
        $messages = [
            'content.required' => '内容描述不能为空',
            'is_scan.boolean' => '是否扫码不正确',
            'is_camera.boolean' => '是否拍照不正确',
            'is_ds.boolean' => '是否DS不正确',
            'type.required' => '检查类型不能为空',
            'number.integer' => '数量不正确',
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
            'part_id',
            'name',
            'name_en',
            'content',
            'content_en',
            'standard',
            'standard_en',
            'eye',
            'eye_en',
            'number',
            'lower_limit',
            'upper_limit',
            'unit',
            'torque',
            'is_scan',
            'is_camera',
            'is_ds',
            'scan',
            'camera',
            'record',
            'process',
            'type',
            'sort_order',
            'thumbnail',
        ]);
        $commitProductItemService->createByCommitId($id, $data);
        return $this->success();
    }

    /**
     * 更新考核定义-产品考核-考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string            $id
     * @param  string            $item_id
     * @param  Request           $request
     * @param  CommitProductItemService $commitProductItemService
     * @return JsonResponse
     */
    public function update(string $id, string $item_id, Request $request, CommitProductItemService $commitProductItemService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commit_products,id,status,0',
            'item_id' => 'exists:commit_product_items,id,commit_product_id,' . $id,
            'part_id' => 'sometimes|nullable|exists:parts,id',
            'name' => 'sometimes|nullable',
            'name_en' => 'sometimes|nullable',
            'content' => 'required',
            'content_en' => 'sometimes|nullable',
            'standard' => 'sometimes|nullable',
            'standard_en' => 'sometimes|nullable',
            'eye' => 'sometimes|nullable',
            'eye_en' => 'sometimes|nullable',
            'number' => 'sometimes|nullable|integer',
            'lower_limit' => 'sometimes|nullable',
            'upper_limit' => 'sometimes|nullable',
            'unit' => 'sometimes|nullable',
            'torque' => 'sometimes|nullable',
            'is_scan' => 'sometimes|nullable|boolean',
            'is_camera' => 'sometimes|nullable|boolean',
            'is_ds' => 'sometimes|nullable|boolean',
            'scan' => 'sometimes|nullable',
            'camera' => 'sometimes|nullable',
            'record' => 'sometimes|nullable',
            'process' => 'sometimes|nullable|integer',
            'type' => 'sometimes|nullable|integer',
            'sort_order' => 'sometimes|nullable|integer',
            'thumbnail' => 'sometimes|array|nullable',
            'thumbnail.*.uuid' => 'required',
            'thumbnail.*.url' => 'required',
            'thumbnail.*.name' => 'required'
        ];
        $messages = [
            'content.required' => '内容描述不能为空',
            'is_scan.boolean' => '是否扫码不正确',
            'is_camera.boolean' => '是否拍照不正确',
            'is_ds.boolean' => '是否DS不正确',
            'type.required' => '检查类型不能为空',
            'number.integer' => '数量不正确',
            'type.integer' => '检查类型不正确',
            'sort_order.integer' => '工作序号不正确',
            'thumbnail.array' => '参数错误#1',
            'thumbnail.min' => '参数错误#2',
            'thumbnail.*.uuid.required' => '参数错误#3',
            'thumbnail.*.url.required' => '参数错误#4',
            'thumbnail.*.name.required' => '参数错误#5',
            'id.exists_plus' => '考核模板不存在',
            'item_id.exists_plus' => '考核项不存在'
        ];
        $validator = Validator::make(array_merge($request->post(), [
            'id' => $id,
            'item_id' => $item_id,
        ]), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'part_id',
            'name',
            'name_en',
            'content',
            'content_en',
            'standard',
            'standard_en',
            'eye',
            'eye_en',
            'number',
            'lower_limit',
            'upper_limit',
            'unit',
            'torque',
            'is_scan',
            'is_camera',
            'is_ds',
            'scan',
            'camera',
            'record',
            'process',
            'type',
            'sort_order',
            'thumbnail',
        ]);
        $commitProductItemService->updateByCommitId($item_id, $data);
        return $this->success();
    }

    /**
     * 上传考核定义-产品考核-考核项图示
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string                   $id
     * @param  Request                  $request
     * @param  CommitProductItemService $commitProductItemService
     * @return JsonResponse
     */
    public function upload(string $id, Request $request, CommitProductItemService $commitProductItemService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commit_products,id,status,0',
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
        $result = $commitProductItemService->upload($data['file']);
        return response()->json([
            'errno' => 0,
            'data' => $result
        ]);
    }

    /**
     * 保存考核定义-产品考核-考核项排序
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string        $id
     * @param  string        $item_id
     * @param  CommitProductItemService $commitProductItemService
     * @return JsonResponse
     */
    public function order(string $id, Request $request, CommitProductItemService $commitProductItemService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commit_products,id',
            'list' => 'required|array|min:1',
            'list.*' => 'exists:commit_product_items,id,commit_product_id,' . $id,
        ];
        $messages = [
            'id.exists' => '考核定义不存在',
            'list.required' => '实际测量项不存在',
            'list.array' => '实际测量项不存在',
            'list.min' => '实际测量项不存在',
            'list.*.exists' => '实际测量项不存在',
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
        $result = $commitProductItemService->updateOrder($data['list']);
        return $this->success($result);
    }


    /**
     * 保存考核定义-产品考核-实际测量项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string        $id
     * @param  string        $item_id
     * @param  CommitProductItemService $commitProductItemService
     * @return JsonResponse
     */
    public function option(string $id, string $item_id, Request $request, CommitProductItemService $commitProductItemService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commit_products,id',
            'item_id' => 'exists:commit_product_items,id,commit_product_id,' . $id,
            'items' => 'required|array|min:1',
            'items.*.name' => 'required',
            'items.*.sort_order' => 'sometimes|nullable|integer'
        ];
        $messages = [
            'id.exists' => '考核定义不存在',
            'item_id.exists' => '考核项不存在',
            'items.required' => '测量项不存在',
            'items.array' => '测量项不存在',
            'items.min' => '测量项不存在',
            'items.*.name.required' => '测量项名称不能为空',
            'items.*.sort_order.integer' => '测量项排序不正确',
        ];
        $validator = Validator::make(array_merge([
            'id' => $id,
            'item_id' => $item_id,
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'items'
        ]);
        $commitProductItemService->updateOption($item_id, $data['items']);
        return $this->success();
    }

    /**
     * 删除考核定义-产品考核-考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string        $id
     * @param  string        $item_id
     * @param  CommitProductItemService $commitProductItemService
     * @return JsonResponse
     */
    public function delete(string $id, string $item_id, CommitProductItemService $commitProductItemService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commit_products,id',
            'item_id' => 'exists:commit_product_items,id,commit_product_id,' . $id
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

        $commitProductItemService->delete($item_id);
        return $this->success();
    }

}
