<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\Backend\AssemblyService;
use App\Services\Backend\DictService;
use App\Services\Backend\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Response as InertiaResponse;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * 发动机清单控制器
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class ProductController extends Controller
{

    /**
     * 发动机清单
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @return InertiaResponse
     */
    public function index(Request $request, DictService $dictService): InertiaResponse
    {
        return Inertia::render('Product/Index', [
            'plant' => $dictService->getOptionByCode('plant'),
            'line' => $dictService->getOptionByCode('assembly_line'),
            'engine_type' => $dictService->getOptionByCode('engine_type'),
            'status' => $dictService->getOptionByCode('assembly_status'),
            'assemblies' => (new AssemblyService)->getOptions()
        ]);
    }

    /**
     * 获取扭矩数据列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request           $request
     * @param  ProductService $productService
     * @return JsonResponse
     */
    public function list(Request $request, ProductService $productService): JsonResponse
    {
        $rules = [
            'keyword' => 'sometimes|nullable',
            'engine' => 'sometimes|nullable|integer',
            'assembly_id' => 'sometimes|nullable|exists:assemblies,id',
        ];
        $messages = [
            'engine.integer' => '机型不正确',
            'assembly_id.exists' => '总成号不正确',
        ];
        $data = $request->validate($rules, $messages);
        $result = $productService->getList($data);
        return $this->success($result);
    }

    /**
     * 创建发动机信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request        $request
     * @param  ProductService $productService
     * @return JsonResponse
     */
    public function create(Request $request, ProductService $productService): JsonResponse
    {
        $rules = [
            'plant' => 'required|integer',
            'line' => 'required|integer',
            'engine' => 'required|integer',
            'status' => 'required|integer',
            'assembly_id' => 'required|exists:assemblies,id',
            'number' => 'required|unique:products,number',
            'beginning_at' => 'sometimes|nullable|date',
            'examine_at' => 'sometimes|nullable|date',
            'qc_at' => 'sometimes|nullable|date',
            'assembled_at' => 'sometimes|nullable|date',
        ];
        $messages = [
            'plant.required' => '工厂不能为空',
            'line.required' => '产线不能为空',
            'engine.required' => '机型不能为空',
            'status.required' => '项目阶段不能为空',
            'plant.integer' => '工厂参数不正确',
            'line.integer' => '产线参数不正确',
            'engine.integer' => '机型参数不正确',
            'status.integer' => '项目阶段参数不正确',
            'assembly_id.required' => '总成号不能为空',
            'assembly_id.exists' => '总成信息不正确',
            'beginning_at.date' => '接机时间不正确',
            'examine_at.date' => '考核时间不正确',
            'qc_at.date' => '试热时间不正确',
            'assembled_at.date' => '装配时间不正确',
        ];
        $data = $request->validate($rules, $messages);
        $productService->create($data);
        return $this->success();
    }

    /**
     * 更新发动机信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string            $id
     * @param  Request           $request
     * @param  ProductService $productService
     * @return JsonResponse
     */
    public function update(string $id, Request $request, ProductService $productService): JsonResponse
    {
        $rules = [
            'id' => 'exists:products,id',
            'plant' => 'required|integer',
            'line' => 'required|integer',
            'engine' => 'required|integer',
            'status' => 'required|integer',
            'assembly_id' => 'required|exists:assemblies,id',
            'number' => 'required|unique:products,number,' . $id,
            'beginning_at' => 'sometimes|nullable|date',
            'examine_at' => 'sometimes|nullable|date',
            'qc_at' => 'sometimes|nullable|date',
            'assembled_at' => 'sometimes|nullable|date',
        ];
        $messages = [
            'plant.required' => '工厂不能为空',
            'line.required' => '产线不能为空',
            'engine.required' => '机型不能为空',
            'status.required' => '项目阶段不能为空',
            'plant.integer' => '工厂参数不正确',
            'line.integer' => '产线参数不正确',
            'engine.integer' => '机型参数不正确',
            'status.integer' => '项目阶段参数不正确',
            'assembly_id.required' => '总成号不能为空',
            'assembly_id.exists' => '总成信息不正确',
            'beginning_at.date' => '接机时间不正确',
            'examine_at.date' => '考核时间不正确',
            'qc_at.date' => '试热时间不正确',
            'assembled_at.date' => '装配时间不正确',
            'id.exists' => '产品信息不正确',
        ];
        $validator = Validator::make(array_merge([
            'id' => $id
        ], $request->post()), $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $data = $validator->safe()->only([
            'plant',
            'line',
            'engine',
            'status',
            'assembly_id',
            'number',
            'beginning_at',
            'examine_at',
            'qc_at',
            'assembled_at',
        ]);
        $productService->update($id, $data);
        return $this->success();
    }


    /**
     * 获取导入模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  ProductService    $productService
     * @return BinaryFileResponse
     */
    public function template(ProductService $productService): BinaryFileResponse
    {
        return $productService->downloadImportTemplate();
    }

    /**
     * 导入发动机信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Request         $request
     * @param  ProductService $productService
     * @return JsonResponse
     */
    public function import(Request $request, ProductService $productService): JsonResponse
    {
        $rules = [
            'file' => 'required|file|mimes:xls,xlsx',
        ];
        $messages = [
            'file.required' => '上传文件不能为空',
            'file.file' => '上传文件错误',
            'file.mimes' => '上传文件格式错误'
        ];
        $data = $request->validate($rules, $messages);
        $productService->import($data['file']);
        return $this->success();
    }


    /**
     * 删除发动机信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string       $id
     * @param  Request      $request
     * @param  ProductService  $productService
     * @return JsonResponse
     */
    public function delete(string $id, Request $request, ProductService $productService): JsonResponse
    {
        $rules = [
            'id' => 'exists:products,id'
        ];
        $messages = [
            'id.exists' => '发动机信息不存在',
        ];
        $validator = Validator::make([
            'id' => $id
        ], $rules, $messages);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $productService->delete($id);
        return $this->success();
    }
}
