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
     * 考核定义-送审核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @param  CommitApproveService $commitApproveService
     * @return JsonResponse
     */
    public function create(Request $request, CommitApproveService $commitApproveService): JsonResponse
    {
        $rules = [
            'vehicle_id' => 'required_without_all:inline_id,product_id|exists:commit_vehicles,id,status,0',
            'inline_id' => 'required_without_all:vehicle_id,product_id|exists:commit_inlines,id,status,0',
            'product_id' => 'required_without_all:vehicle_id,inline_id|exists:commit_products,id,status,0',
            'content' => 'sometimes|nullable|max:250',
            'influence' => 'sometimes|nullable|max:250',
            'concerns' => 'sometimes|nullable|max:250',
        ];
        $messages = [
            'vehicle_id.required_without_all' => '考核模板ID不能为空',
            'inline_id.required_without_all' => '考核模板ID不能为空',
            'product_id.required_without_all' => '考核模板ID不能为空',
            'vehicle_id.exists' => '当前模板无法提交送审',
            'inline_id.exists' => '当前模板无法提交送审',
            'product_id.exists' => '当前模板无法提交送审',
            'content.max' => '变更内容最大支持250个字符',
            'influence.max' => '影响范围最大支持250个字符',
            'concerns.max' => '关注事项最大支持250个字符'
        ];
        $data = $request->validate($rules,$messages);
        $commitApproveService->createApprove($request->user(),$data);
        return $this->success();
    }

}
