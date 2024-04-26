<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\CommitApproveService;
use App\Services\Backend\CommitService;
use App\Services\Backend\DictService;
use App\Services\Backend\ExamineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;

/**
 * 考核历史-送审控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class CommitApproveController extends Controller
{

    /**
     * 添加考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string          $id
     * @param  Request         $request
     * @param  CommitApproveService $commitApproveService
     * @return JsonResponse
     */
    public function create(string $id, Request $request, CommitApproveService $commitApproveService): JsonResponse
    {
        $rules = [
            'id' => 'exists_plus:commits,id,0,status',
            'content' => 'required|max:200',
            'influence' => 'required|max:200',
            'concerns' => 'required|max:200',
        ];
        $messages = [
            'content.required' => '变更内容不能为空',
            'influence.required' => '影响范围不能为空',
            'concerns.required' => '关注事项不能为空',
            'content.max' => '变更内容最大支持200个字符',
            'influence.max' => '影响范围最大支持200个字符',
            'concerns.max' => '关注事项最大支持200个字符',
            'id.exists_plus' => '考核模板不存在'
        ];
        $validator = Validator::make(array_merge($request->post(),[
            'id' => $id
        ]), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'content',
            'influence',
            'concerns',
        ]);
        $commitApproveService->createByCommitId($request->user(),$id,$data);
        return $this->success();
    }

}
