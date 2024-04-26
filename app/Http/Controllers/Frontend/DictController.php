<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\DictService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class DictController extends Controller
{
    /**
     * 获取参数选项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $code
     * @param  DictService  $dictService
     * @return JsonResponse
     */
    public function option(Request $request, DictService $dictService): JsonResponse
    {
        $rules = [
            'code' => 'required|array|min:1'
        ];
        $messages = [
            'code.required' => __('dict.option.code.required'),
            'code.array' => __('dict.option.code.array'),
            'code.min' => __('dict.option.code.min')
        ];
        $data = $request->validate($rules, $messages);
        $result = $dictService->getOptions($data['code']);
        return $this->success($result);
    }

}
