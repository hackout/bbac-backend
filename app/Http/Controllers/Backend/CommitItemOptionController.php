<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\CommitItemOptionService;
use App\Services\Backend\CommitService;
use App\Services\Backend\DictService;
use App\Services\Backend\ExamineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;

/**
 * 考核历史-实际测量项控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class CommitItemOptionController extends Controller
{
    /**
     * 获取实际测量项列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string                  $id
     * @param  string                  $item_id
     * @param  Request                 $request
     * @param  CommitItemOptionService $commitItemOptionService
     * @return JsonResponse
     */
    public function list(string $id, string $item_id, Request $request, CommitItemOptionService $commitItemOptionService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commits,id',
            'item_id' => 'exists_plus:commit_items,id,' . $id . ',commit_id',
        ];
        $messages = [
            'id.exists' => '考核定义不存在',
            'id.exists_plus' => '考核项不存在',
        ];
        $validator = Validator::make(array_merge([
            'id' => $id,
            'item_id' => $item_id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $result = $commitItemOptionService->getList($item_id);
        return $this->success($result);
    }

    /**
     * 添加实际测量项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string        $id
     * @param  string        $item_id
     * @param  Request       $request
     * @param  CommitItemOptionService $commitItemOptionService
     * @return JsonResponse
     */
    public function save(string $id, string $item_id, Request $request, CommitItemOptionService $commitItemOptionService): JsonResponse
    {
        $rules = [
            'id' => 'exists_plus:commits,id,0,status',
            'item_id' => 'exists_plus:commit_items,id,' . $id . ',commit_id',
            'items' => 'required|array',
            'items.*.id' => 'sometimes|nullable|exists_or_null:commit_item_options,id',
            'items.*.name_zh' => 'required|max:100',
            'items.*.name_en' => 'required|max:100',
            'items.*.sort_order' => 'sometimes|nullable|integer'
        ];
        $messages = [
            'id.exists_plus' => '考核定义不存在',
            'item_id.exists_plus' => '考核项不存在',
            'items.required' => '实际测量项不能为空',
            'items.array' => '实际测量项不正确',
            'items.*.id.exists_or_null' => '实际测量项不存在',
            'items.*.name_zh.required' => '中文名称不能为空',
            'items.*.name_zh.max' => '中文名称最大支持100个字符',
            'items.*.name_en.required' => '英文名称不能为空',
            'items.*.name_en.max' => '英文名称最大支持100个字符',
            'items.*.sort_order.integer' => '序号不正确',
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
        $commitItemOptionService->saveData($item_id, $data['items']);
        return $this->success();
    }

}
