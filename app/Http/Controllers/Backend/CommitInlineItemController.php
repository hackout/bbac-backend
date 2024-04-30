<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\Backend\CommitInlineItemService;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * 考核定义-在线考核-考核项控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class CommitInlineItemController extends Controller
{

    /**
     * 获取考核定义-在线考核-考核项列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string            $id
     * @param  Request           $request
     * @param  CommitInlineItemService $commitInlineItemService
     * @return JsonResponse
     */
    public function list(string $id, Request $request, CommitInlineItemService $commitInlineItemService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commit_inlines,id'
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
        $result = $commitInlineItemService->getList($id);
        return $this->success($result);
    }

    /**
     * 添加考核定义-在线考核-考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string          $id
     * @param  Request         $request
     * @param  CommitInlineItemService $commitInlineItemService
     * @return JsonResponse
     */
    public function create(string $id, Request $request, CommitInlineItemService $commitInlineItemService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commit_inlines,id,status,0',
            'content' => 'required',
            'content_en' => 'sometimes|nullable',
            'name' => 'sometimes|nullable',
            'standard' => 'sometimes|nullable',
            'standard_en' => 'sometimes|nullable',
            'gluing' => 'sometimes|nullable',
            'bolt_number' => 'required_if:type,1|nullable',
            'unit' => 'sometimes|nullable',
            'special' => 'sometimes|nullable',
            'bolt_model' => 'sometimes|nullable',
            'bolt_type' => 'sometimes|nullable',
            'bolt_status' => 'sometimes|nullable',
            'lower_limit' => 'sometimes|nullable',
            'upper_limit' => 'sometimes|nullable',
            'number' => 'required|integer',
            'type' => 'required|integer',
            'sort_order' => 'sometimes|nullable|integer',
            'thumbnail' => 'sometimes|array|nullable',
            'thumbnail.*.uuid' => 'required',
            'thumbnail.*.url' => 'required',
            'thumbnail.*.name' => 'required'
        ];
        $messages = [
            'bolt_number.required_if' => '螺栓编号不能为空',
            'content.required' => '内容描述不能为空',
            'type.required' => '检查类型不能为空',
            'number.required' => '数量不能为空',
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
            'content',
            'content_en',
            'name',
            'standard',
            'standard_en',
            'gluing',
            'bolt_number',
            'unit',
            'special',
            'bolt_model',
            'bolt_type',
            'bolt_status',
            'lower_limit',
            'upper_limit',
            'number',
            'type',
            'sort_order',
            'thumbnail',
        ]);
        $commitInlineItemService->createByCommitId($id, $data);
        return $this->success();
    }

    /**
     * 更新考核定义-在线考核-考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string            $id
     * @param  string            $item_id
     * @param  Request           $request
     * @param  CommitInlineItemService $commitInlineItemService
     * @return JsonResponse
     */
    public function update(string $id, string $item_id, Request $request, CommitInlineItemService $commitInlineItemService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commit_inlines,id,status,0',
            'item_id' => 'exists:commit_inline_items,id,commit_inline_id,' . $id,
            'content' => 'required',
            'content_en' => 'sometimes|nullable',
            'name' => 'sometimes|nullable',
            'standard' => 'sometimes|nullable',
            'standard_en' => 'sometimes|nullable',
            'gluing' => 'sometimes|nullable',
            'bolt_number' => 'required_if:type,1|nullable',
            'unit' => 'sometimes|nullable',
            'special' => 'sometimes|nullable',
            'bolt_model' => 'sometimes|nullable',
            'bolt_type' => 'sometimes|nullable',
            'bolt_status' => 'sometimes|nullable',
            'lower_limit' => 'sometimes|nullable',
            'upper_limit' => 'sometimes|nullable',
            'number' => 'required|integer',
            'type' => 'required|integer',
            'sort_order' => 'sometimes|nullable|integer',
            'thumbnail' => 'sometimes|array|nullable',
            'thumbnail.*.uuid' => 'required',
            'thumbnail.*.url' => 'required',
            'thumbnail.*.name' => 'required'
        ];
        $messages = [
            'content.required' => '工作内容不能为空',
            'type.required' => '检查类型不能为空',
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
            'name',
            'standard',
            'standard_en',
            'gluing',
            'bolt_number',
            'unit',
            'special',
            'bolt_model',
            'bolt_type',
            'bolt_status',
            'lower_limit',
            'upper_limit',
            'number',
            'type',
            'sort_order',
            'thumbnail',
        ]);
        $commitInlineItemService->updateByCommitId($item_id, $data);
        return $this->success();
    }

    /**
     * 上传考核定义-在线考核-考核项图示
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string                   $id
     * @param  Request                  $request
     * @param  CommitInlineItemService $commitInlineItemService
     * @return JsonResponse
     */
    public function upload(string $id, Request $request, CommitInlineItemService $commitInlineItemService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commit_inlines,id,status,0',
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
        $result = $commitInlineItemService->upload($data['file']);
        return response()->json([
            'errno' => 0,
            'data' => $result
        ]);
    }

    /**
     * 保存考核定义-在线考核-考核项排序
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string        $id
     * @param  string        $item_id
     * @param  CommitInlineItemService $commitInlineItemService
     * @return JsonResponse
     */
    public function order(string $id, Request $request, CommitInlineItemService $commitInlineItemService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commit_inlines,id',
            'list' => 'required|array|min:1',
            'list.*' => 'exists:commit_inline_items,id,commit_inline_id,' . $id,
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
        $result = $commitInlineItemService->updateOrder($data['list']);
        return $this->success($result);
    }


    /**
     * 保存考核定义-在线考核-实际测量项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string        $id
     * @param  string        $item_id
     * @param  CommitInlineItemService $commitInlineItemService
     * @return JsonResponse
     */
    public function option(string $id, string $item_id, Request $request, CommitInlineItemService $commitInlineItemService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commit_inlines,id',
            'item_id' => 'exists:commit_inline_items,id,commit_inline_id,' . $id,
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
        $commitInlineItemService->updateOption($item_id, $data['items']);
        return $this->success();
    }

    /**
     * 删除考核定义-在线考核-考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string        $id
     * @param  string        $item_id
     * @param  CommitInlineItemService $commitInlineItemService
     * @return JsonResponse
     */
    public function delete(string $id, string $item_id, CommitInlineItemService $commitInlineItemService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commit_inlines,id',
            'item_id' => 'exists:commit_inline_items,id,commit_inline_id,' . $id
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

        $commitInlineItemService->delete($item_id);
        return $this->success();
    }

}
