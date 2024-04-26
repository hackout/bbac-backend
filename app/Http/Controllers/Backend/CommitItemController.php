<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\CommitItemService;
use App\Services\Backend\CommitService;
use App\Services\Backend\DictService;
use App\Services\Backend\ExamineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;

/**
 * 考核历史-考核项控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class CommitItemController extends Controller
{

    /**
     * 获取考核项列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string            $id
     * @param  Request           $request
     * @param  CommitItemService $commitItemService
     * @return JsonResponse
     */
    public function list(string $id, Request $request, CommitItemService $commitItemService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commits,id'
        ];
        $messages = [
            'id.exists' => '当前模板无法编辑',
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $result = $commitItemService->getList($id);
        return $this->success($result);
    }

    /**
     * 添加考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string          $id
     * @param  Request         $request
     * @param  CommitItemService $commitItemService
     * @return JsonResponse
     */
    public function create(string $id, Request $request, CommitItemService $commitItemService): JsonResponse
    {
        $rules = [
            'id' => 'exists_plus:commits,id,0,status',
            'station' => 'sometimes|nullable|max:200',
            'sub_station' => 'sometimes|nullable',
            'name_zh' => 'sometimes|nullable|max:200',
            'name_en' => 'sometimes|nullable|max:200',
            'content_zh' => 'sometimes|nullable|max:200',
            'content_en' => 'sometimes|nullable|max:200',
            'standard_zh' => 'sometimes|nullable|max:200',
            'standard_en' => 'sometimes|nullable|max:200',
            'eye_zh' => 'sometimes|nullable|max:200',
            'eye_en' => 'sometimes|nullable|max:200',
            'number' => 'sometimes|nullable|integer',
            'special' => 'sometimes|nullable|integer',
            'gluing' => 'sometimes|nullable',
            'bolt_number' => 'sometimes|nullable',
            'bolt_model' => 'sometimes|nullable|integer',
            'bolt_type' => 'sometimes|nullable|integer',
            'bolt_status' => 'sometimes|nullable|integer',
            'blot_close' => 'sometimes|nullable|max:200',
            'lower_limit' => 'sometimes|nullable|numeric',
            'upper_limit' => 'sometimes|nullable|numeric',
            'unit' => 'sometimes|nullable',
            'is_scan' => 'sometimes|nullable|boolean',
            'is_camera' => 'sometimes|nullable|boolean',
            'part_number' => 'sometimes|nullable',
            'process' => 'sometimes|nullable',
            'type' => 'sometimes|nullable|integer',
            'sort_order' => 'sometimes|nullable|integer',
            'thumbnail' => 'sometimes|array|nullable',
            'thumbnail.*.uuid' => 'required',
            'thumbnail.*.url' => 'required',
            'thumbnail.*.name' => 'required'
        ];
        $messages = [
            'station.max' => '工位号最大支持200个字符',
            'name_zh.max' => '测量项(中文)最大支持200个字符',
            'name_en.max' => '测量项(英文)最大支持200个字符',
            'content_zh.max' => '内容说明(中文)最大支持200个字符',
            'content_en.max' => '内容说明(英文)最大支持200个字符',
            'standard_zh.max' => '检查标准(中文)最大支持200个字符',
            'standard_en.max' => '检查标准(英文)最大支持200个字符',
            'eye_zh.max' => '目视检测(中文)最大支持200个字符',
            'eye_en.max' => '目视检测(英文)最大支持200个字符',
            'blot_close.max' => '拧紧螺距要求最大支持200个字符',
            'number.integer' => '数量不正确',
            'special.integer' => '特殊特性不正确',
            'bolt_model.integer' => '螺栓分类1不正确',
            'bolt_type.integer' => '螺栓分类2不正确',
            'bolt_status.integer' => '方形状态不正确',
            'lower_limit.numeric' => '测量下限不正确',
            'upper_limit.numeric' => '测量上限不正确',
            'is_scan.boolean' => '是否扫码不正确',
            'is_camera.boolean' => '是否拍照不正确',
            'type.integer' => '考核项类型不正确',
            'sort_order.integer' => '序号不正确',
            'thumbnail.array' => '参数错误#1',
            'thumbnail.min' => '参数错误#2',
            'thumbnail.*.uuid.required' => '参数错误#3',
            'thumbnail.*.url.required' => '参数错误#4',
            'thumbnail.*.name.required' => '参数错误#5',
            'id.exists_plus' => '考核模板不存在'
        ];
        $validator = Validator::make(array_merge($request->post(),[
            'id' => $id
        ]), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'station',
            'sub_station',
            'name_zh',
            'name_en',
            'content_zh',
            'content_en',
            'standard_zh',
            'standard_en',
            'eye_zh',
            'eye_en',
            'number',
            'special',
            'gluing',
            'bolt_number',
            'bolt_model',
            'bolt_type',
            'bolt_status',
            'blot_close',
            'lower_limit',
            'upper_limit',
            'unit',
            'is_scan',
            'is_camera',
            'part_number',
            'process',
            'type',
            'sort_order',
            'thumbnail',
        ]);
        $commitItemService->createByCommitId($id,$data);
        return $this->success();
    }

    /**
     * 更新考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string            $id
     * @param  string            $item_id
     * @param  Request           $request
     * @param  CommitItemService $commitItemService
     * @return JsonResponse
     */
    public function update(string $id,string $item_id, Request $request, CommitItemService $commitItemService): JsonResponse
    {
        $rules = [
            'id' => 'exists_plus:commits,id,0,status',
            'item_id' => 'exists_plus:commit_items,id,'.$id.',commit_id',
            'station' => 'sometimes|nullable|max:200',
            'sub_station' => 'sometimes|nullable',
            'name_zh' => 'sometimes|nullable|max:200',
            'name_en' => 'sometimes|nullable|max:200',
            'content_zh' => 'sometimes|nullable|max:200',
            'content_en' => 'sometimes|nullable|max:200',
            'standard_zh' => 'sometimes|nullable|max:200',
            'standard_en' => 'sometimes|nullable|max:200',
            'eye_zh' => 'sometimes|nullable|max:200',
            'eye_en' => 'sometimes|nullable|max:200',
            'number' => 'sometimes|nullable|integer',
            'special' => 'sometimes|nullable|integer',
            'gluing' => 'sometimes|nullable',
            'bolt_number' => 'sometimes|nullable',
            'bolt_model' => 'sometimes|nullable|integer',
            'bolt_type' => 'sometimes|nullable|integer',
            'bolt_status' => 'sometimes|nullable|integer',
            'blot_close' => 'sometimes|nullable|max:200',
            'lower_limit' => 'sometimes|nullable|numeric',
            'upper_limit' => 'sometimes|nullable|numeric',
            'unit' => 'sometimes|nullable',
            'is_scan' => 'sometimes|nullable|boolean',
            'is_camera' => 'sometimes|nullable|boolean',
            'part_number' => 'sometimes|nullable',
            'process' => 'sometimes|nullable',
            'type' => 'sometimes|nullable|integer',
            'sort_order' => 'sometimes|nullable|integer',
            'thumbnail' => 'sometimes|array|nullable',
            'thumbnail.*.uuid' => 'required',
            'thumbnail.*.url' => 'required',
            'thumbnail.*.name' => 'required'
        ];
        $messages = [
            'station.max' => '工位号最大支持200个字符',
            'name_zh.max' => '测量项(中文)最大支持200个字符',
            'name_en.max' => '测量项(英文)最大支持200个字符',
            'content_zh.max' => '内容说明(中文)最大支持200个字符',
            'content_en.max' => '内容说明(英文)最大支持200个字符',
            'standard_zh.max' => '检查标准(中文)最大支持200个字符',
            'standard_en.max' => '检查标准(英文)最大支持200个字符',
            'eye_zh.max' => '目视检测(中文)最大支持200个字符',
            'eye_en.max' => '目视检测(英文)最大支持200个字符',
            'blot_close.max' => '拧紧螺距要求最大支持200个字符',
            'number.integer' => '数量不正确',
            'special.integer' => '特殊特性不正确',
            'bolt_model.integer' => '螺栓分类1不正确',
            'bolt_type.integer' => '螺栓分类2不正确',
            'bolt_status.integer' => '方形状态不正确',
            'lower_limit.numeric' => '测量下限不正确',
            'upper_limit.numeric' => '测量上限不正确',
            'is_scan.boolean' => '是否扫码不正确',
            'is_camera.boolean' => '是否拍照不正确',
            'type.integer' => '考核项类型不正确',
            'sort_order.integer' => '序号不正确',
            'thumbnail.array' => '参数错误#1',
            'thumbnail.min' => '参数错误#2',
            'thumbnail.*.uuid.required' => '参数错误#3',
            'thumbnail.*.url.required' => '参数错误#4',
            'thumbnail.*.name.required' => '参数错误#5',
            'id.exists_plus' => '考核模板不存在',
            'item_id.exists_plus' => '考核项不存在'
        ];
        $validator = Validator::make(array_merge($request->post(),[
            'id' => $id,
            'item_id' => $item_id,
        ]), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'station',
            'sub_station',
            'name_zh',
            'name_en',
            'content_zh',
            'content_en',
            'standard_zh',
            'standard_en',
            'eye_zh',
            'eye_en',
            'number',
            'special',
            'gluing',
            'bolt_number',
            'bolt_model',
            'bolt_type',
            'bolt_status',
            'blot_close',
            'lower_limit',
            'upper_limit',
            'unit',
            'is_scan',
            'is_camera',
            'part_number',
            'process',
            'type',
            'sort_order',
            'thumbnail',
        ]);
        $commitItemService->updateByCommitId($item_id, $data);
        return $this->success();
    }

    /**
     * 上传考核项图示
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request           $request
     * @param  CommitItemService $commitItemService
     * @return JsonResponse
     */
    public function upload(Request $request, CommitItemService $commitItemService): JsonResponse
    {
        $data = $request->validate([
            'file' => 'required|image'
        ], [
            'file.required' => '文件不能为空',
            'file.image' => '图片文件不合规'
        ]);
        $result = $commitItemService->upload($data['file']);
        return response()->json([
            'errno' => 0,
            'data' => $result
        ]);
    }

    /**
     * 删除考核项图示
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string            $id
     * @param  string            $uuid
     * @param  CommitItemService $commitItemService
     * @return JsonResponse
     */
    public function uploadDelete(string $id, string $uuid, CommitItemService $commitItemService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commits,id',
        ];
        $messages = [
            'id.exists' => '当前模板无法编辑',
        ];
        $validator = Validator::make([
            'id' => $id
        ], $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $commitItemService->uploadDelete($uuid);
        return $this->success();
    }

    /**
     * 保存实际测量项排序
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string        $id
     * @param  string        $item_id
     * @param  CommitItemService $commitItemService
     * @return JsonResponse
     */
    public function order(string $id, Request $request, CommitItemService $commitItemService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commits,id',
            'list' => 'required|array|min:1',
            'list.*' => 'exists_plus:commit_items,id,' . $id . ',commit_id',
        ];
        $messages = [
            'id.exists' => '考核定义不存在',
            'list.required' => '实际测量项不存在',
            'list.array' => '实际测量项不存在',
            'list.min' => '实际测量项不存在',
            'list.*.exists_plus' => '实际测量项不存在',
        ];
        $validator = Validator::make(array_merge([
            'id' => $id,
        ],$request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'list'
        ]);
        $commitItemService->updateOrder($data['list']);
        return $this->success();
    }


    /**
     * 删除考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string        $id
     * @param  string        $item_id
     * @param  CommitItemService $commitItemService
     * @return JsonResponse
     */
    public function delete(string $id, string $item_id, CommitItemService $commitItemService): JsonResponse
    {
        $rules = [
            'id' => 'exists:commits,id',
            'item_id' => 'exists_plus:commit_items,id,' . $id . ',commit_id'
        ];
        $messages = [
            'id.exists' => '考核定义不存在',
            'item_id.exists_plus' => '考核项不存在',
        ];
        $validator = Validator::make([
            'id' => $id,
            'item_id' => $item_id,
        ], $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $commitItemService->delete($item_id);
        return $this->success();
    }

}
