<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\DictItemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * 字典项控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class DictItemController extends Controller
{

    /**
     * 查询字典项列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string          $code
     * @param  Request         $request
     * @param  DictItemService $dictItemService
     * @return JsonResponse
     */
    public function list(string $code,Request $request, DictItemService $dictItemService): JsonResponse
    {
        $rules = [
            'code' => 'exists:dicts,code',
        ];
        $messages = [
            'code.exists' => '字典标识不正确',
        ];
        $validator = Validator::make(array_merge([
            'code' => $code
        ], $request->input()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'code'
        ]);
        $result = $dictItemService->getList($data);
        return $this->success($result);
    }

    /**
     * 创建信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string          $code
     * @param  Request         $request
     * @param  DictItemService $dictItemService
     * @return JsonResponse
     */
    public function create(string $code,Request $request, DictItemService $dictItemService): JsonResponse
    {
        $rules = [
            'code' => 'exists:dicts,code',
            'name' => 'required|max:100',
            'content' => 'required|integer',
            'sort_order' => 'required|integer'
        ];
        $messages = [
            'name.required' => '字典项键名不能为空',
            'name.max' => '字典项键名最大支持100个字符',
            'content.required' => '字典项键值不能为空',
            'content.integer' => '字典项键值不正确',
            'sort_order.required' => '字典项排序不能为空',
            'sort_order.integer' => '字典项排序不正确',
            'code.exists' => '字典标识不正确',
        ];
        $validator = Validator::make(array_merge([
            'code' => $code
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'code',
            'name',
            'content',
            'sort_order'
        ]);
        $dictItemService->create($data);
        return $this->success();
    }

    /**
     * 更新信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string          $code
     * @param  integer         $id
     * @param  Request         $request
     * @param  DictItemService $dictItemService
     * @return JsonResponse
     */
    public function update(string $code,int $id, Request $request, DictItemService $dictItemService): JsonResponse
    {
        $rules = [
            'code' => 'exists:dicts,code',
            'id' => 'exists:dict_items,id',
            'name' => 'required|max:100',
            'content' => 'required|integer',
            'sort_order' => 'required|integer',
            'thumbnail' => 'sometimes|nullable|image'
        ];
        $messages = [
            'name.required' => '字典项键名不能为空',
            'name.max' => '字典项键名最大支持100个字符',
            'content.required' => '字典项键值不能为空',
            'content.integer' => '字典项键值不正确',
            'sort_order.required' => '字典项排序不能为空',
            'sort_order.integer' => '字典项排序不正确',
            'code.exists' => '字典标识不正确',
            'id.exists' => '字典标识不正确',
            'thumbnail.image' => '缩率图仅支持图片格式',
        ];
        $validator = Validator::make(array_merge([
            'code' => $code,
            'id' => $id
        ], $request->all()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'code',
            'name',
            'content',
            'sort_order',
            'thumbnail'
        ]);
        $dictItemService->update($id, $data);
        return $this->success();
    }

    /**
     * 批量删除信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string          $code
     * @param  Request         $request
     * @param  DictItemService $dictItemService
     * @return JsonResponse
     */
    public function batchDelete(string $code,Request $request, DictItemService $dictItemService): JsonResponse
    {
        $rules = [
            'code' => 'exists:dicts,code',
            'ids' => 'required|array',
            'ids.*' => 'required|exists:departments,id'
        ];
        $messages = [
            'ids.required' => '请选择一个字典项',
            'ids.array' => '字典项参数错误',
            'ids.*.required' => '字典项参数不能为空',
            'ids.*.exists' => '字典项参数错误',
            'code.exists' => '字典标识不正确'
        ];
        $validator = Validator::make(array_merge([
            'code' => $code
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'code',
            'ids'
        ]);
        $dictItemService->batch_delete($data['ids']);
        return $this->success();
    }
}
