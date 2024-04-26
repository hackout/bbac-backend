<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\BirthdayCardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * 生日祝福模板控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class BirthdayCardController extends Controller
{


    /**
     * 获取组织结构树型列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request           $request
     * @param  BirthdayCardService $birthdayCardService
     * @return JsonResponse
     */
    public function list(Request $request, BirthdayCardService $birthdayCardService): JsonResponse
    {
        $rules = [
            'keyword' => 'sometimes|nullable'
        ];
        $data = $request->validate($rules);
        $result = $birthdayCardService->getList($data);
        return $this->success($result);
    }

    /**
     * 创建祝福卡模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request           $request
     * @param  BirthdayCardService $birthdayCardService
     * @return JsonResponse
     */
    public function create(Request $request, BirthdayCardService $birthdayCardService): JsonResponse
    {
        $rules = [
            'name' => 'required',
            'design' => 'required|array',
            'file' => 'required'
        ];
        $messages = [
            'name.required' => '样式名称不能为空',
            'design.required' => '设计参数不能为空',
            'design.array' => '设计参数错误',
            'file.required' => '底图不能为空'
        ];
        $data = $request->validate($rules, $messages);
        $birthdayCardService->create(array_merge(['user'=>$request->user()],$data));
        return $this->success();
    }

    /**
     * 更新祝福卡模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string            $id
     * @param  Request           $request
     * @param  BirthdayCardService $birthdayCardService
     * @return JsonResponse
     */
    public function update(string $id, Request $request, BirthdayCardService $birthdayCardService): JsonResponse
    {
        $rules = [
            'id' => 'exists:birthday_cards,id',
            'name' => 'required',
            'design' => 'required|array',
            'file' => 'required'
        ];
        $messages = [
            'id.exists' => '样式不存在或已删除',
            'name.required' => '样式名称不能为空',
            'design.required' => '设计参数不能为空',
            'design.array' => '设计参数错误',
            'file.required' => '底图不能为空'
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->input()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'name',
            'design',
            'file'
        ]);
        $birthdayCardService->update($id, array_merge(['user' => $request->user()], $data));
        return $this->success();
    }

    /**
     * 删除祝福卡模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string              $id
     * @param  BirthdayCardService $birthdayCardService
     * @return JsonResponse
     */
    public function delete(string $id, BirthdayCardService $birthdayCardService): JsonResponse
    {
        $birthdayCardService->delete($id);
        return $this->success();
    }
}
