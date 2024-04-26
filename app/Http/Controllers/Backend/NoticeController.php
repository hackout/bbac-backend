<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\AssemblyService;
use App\Services\Backend\DepartmentService;
use App\Services\Backend\DictService;
use App\Services\Backend\NoticeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;
use Illuminate\Http\JsonResponse;

/**
 * 消息中心控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class NoticeController extends Controller
{
    /**
     * 任务分配
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function index(Request $request, DictService $dictService): InertiaResponse
    {
        return Inertia::render('Notice/Index', [
            'notice_type' => $dictService->getOptionByCode('notice_type'),
            'examine_type' => $dictService->getOptionByCode('examine_type'),
            'departments' => (new DepartmentService)->getFullOptions($request->user())
        ]);
    }

    /**
     * 获取考核单列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request           $request
     * @param  NoticeService $noticeService
     * @return JsonResponse
     */
    public function list(Request $request, NoticeService $noticeService): JsonResponse
    {
        $rules = [
            'keyword' => 'sometimes|nullable',
            'type' => 'sometimes|nullable|integer',
            'is_valid' => 'sometimes|nullable|boolean',
        ];
        $messages = [
            'type.integer' => '消息类型不正确',
            'is_valid.integer' => '消息状态不正确',
        ];
        $data = $request->validate($rules, $messages);
        $result = $noticeService->getList($request->user(), $data);
        return $this->success($result);
    }

    /**
     * 添加消息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request      $request
     * @param  NoticeService  $workService
     * @return JsonResponse
     */
    public function create(Request $request, NoticeService $noticeService): JsonResponse
    {
        $rules = [
            'title' => 'required|max:200',
            'type' => 'required|integer',
            'content' => 'sometimes|nullable',
            'from' => 'sometimes|nullable',
            'is_valid' => 'sometimes|nullable|boolean',
            'extra' => 'required_if:type,3|nullable|array',
            'extra.version' => 'required_if:type,3',
            'extra.document_id' => 'required_if:type,3|exists:documents,id',
            'extra.change' => 'required_if:type,3|array|min:1',
            'extra.change.*.name' => 'required_if:type,3|max:50',
            'extra.change.*.before' => 'sometimes|nullable',
            'extra.change.*.content' => 'required_if:type,3|max:200'
        ];

        $messages = [
            'title.required' => '消息标题不能为空',
            'title.max' => '消息标题最大支持200个字符',
            'type.required' => '消息类型不能为空',
            'type.integer' => '消息类型不正确',
            'is_valid.boolean' => '提交类型不正确',
            'extra.array' => '变动参数不正确',
            'extra.required_if' => '版本及其他参数不能为空',
            'extra.version.required_if' => '变动参数不正确',
            'extra.document_id.required_if' => '相关文档不能为空',
            'extra.document_id.exists' => '相关文档不正确',
            'extra.change.required_if' => '变更信息不能为空',
            'extra.change.array' => '变更信息不正确',
            'extra.change.min' => '变更信息不能为空',
            'extra.change.*.name.required_if' => '变更项不能为空',
            'extra.change.*.content.required_if' => '变更内容不能为空',
            'extra.change.*.name.max' => '变更项最大支持50个字符',
            'extra.change.*.content.max' => '变更内容最大支持200个字符',
        ];

        $data = $request->validate($rules, $messages);
        $noticeService->createByUser($request->user(), $data);
        return $this->success();
    }

    /**
     * 批量删除消息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request       $request
     * @param  NoticeService $noticeService
     * @return JsonResponse
     */
    public function batchDelete(Request $request, NoticeService $noticeService): JsonResponse
    {
        $rules = [
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|exists:notices,id'
        ];
        $messages = [
            'ids.required' => '信息参数不能为空',
            'ids.array' => '信息参数不正确',
            'ids.min' => '请至少选择一条消息',
            'ids.*.required' => '消息参数不能为空',
            'ids.*.exists' => '消息参数不正确',
        ];
        $data = $request->validate($rules, $messages);

        $noticeService->batchDelete($request->user(), $data);
        return $this->success();
    }

    /**
     * 批量撤回消息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request       $request
     * @param  NoticeService $noticeService
     * @return JsonResponse
     */
    public function retract(Request $request, NoticeService $noticeService): JsonResponse
    {
        $rules = [
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|exists:notices,id'
        ];
        $messages = [
            'ids.required' => '信息参数不能为空',
            'ids.array' => '信息参数不正确',
            'ids.min' => '请至少选择一条消息',
            'ids.*.required' => '消息参数不能为空',
            'ids.*.exists' => '消息参数不正确',
        ];
        $data = $request->validate($rules, $messages);

        $noticeService->retract($data);
        return $this->success();
    }

    /**
     * 批量发布消息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request       $request
     * @param  NoticeService $noticeService
     * @return JsonResponse
     */
    public function push(Request $request, NoticeService $noticeService): JsonResponse
    {
        $rules = [
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|exists:notices,id'
        ];
        $messages = [
            'ids.required' => '信息参数不能为空',
            'ids.array' => '信息参数不正确',
            'ids.min' => '请至少选择一条消息',
            'ids.*.required' => '消息参数不能为空',
            'ids.*.exists' => '消息参数不正确',
        ];
        $data = $request->validate($rules, $messages);

        $noticeService->push($data);
        return $this->success();
    }

    /**
     * 编辑消息信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string        $id
     * @param  Request       $request
     * @param  NoticeService $noticeService
     * @return JsonResponse
     */
    public function update(string $id, Request $request, NoticeService $noticeService): JsonResponse
    {
        $rules = [
            'id' => 'exists:notices,id',
            'title' => 'required|max:200',
            'type' => 'required|integer',
            'content' => 'sometimes|nullable',
            'from' => 'sometimes|nullable',
            'is_valid' => 'sometimes|nullable|boolean',
            'extra' => 'required_if:type,3|nullable|array',
            'extra.version' => 'required_if:type,3',
            'extra.document_id' => 'required_if:type,3|exists:documents,id',
            'extra.change' => 'required_if:type,3|array|min:1',
            'extra.change.*.name' => 'required_if:type,3|max:50',
            'extra.change.*.before' => 'sometimes|nullable',
            'extra.change.*.content' => 'required_if:type,3|max:200'
        ];
        $messages = [
            'id.exists' => '消息内容不存在',
            'title.required' => '消息标题不能为空',
            'title.max' => '消息标题最大支持200个字符',
            'type.required' => '消息类型不能为空',
            'type.integer' => '消息类型不正确',
            'is_valid.boolean' => '提交类型不正确',
            'extra.array' => '变动参数不正确',
            'extra.required_if' => '版本及其他参数不能为空',
            'extra.version.required_if' => '变动参数不正确',
            'extra.document_id.required_if' => '相关文档不能为空',
            'extra.document_id.exists' => '相关文档不正确',
            'extra.change.required_if' => '变更信息不能为空',
            'extra.change.array' => '变更信息不正确',
            'extra.change.min' => '变更信息不能为空',
            'extra.change.*.name.required_if' => '变更项不能为空',
            'extra.change.*.content.required_if' => '变更内容不能为空',
            'extra.change.*.name.max' => '变更项最大支持50个字符',
            'extra.change.*.content.max' => '变更内容最大支持200个字符',
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->input()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'title',
            'type',
            'content',
            'from',
            'is_valid',
            'extra',
        ]);
        $noticeService->update($id, $data);
        return $this->success();
    }

    /**
     * 预览消息详情
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string                       $id
     * @param  Request                      $request
     * @param  NoticeService                $noticeService
     * @return JsonResponse|InertiaResponse
     */
    public function detail(string $id, Request $request, NoticeService $noticeService): JsonResponse|InertiaResponse
    {
        $rules = [
            'id' => 'exists:notices,id,user_id,' . $request->user()->id,
        ];
        $messages = [
            'id.exists' => '消息内容不存在',
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->input()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $result = $noticeService->detail($id);
        return $this->success($result);
    }
}
