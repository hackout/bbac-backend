<?php
namespace App\Services\Backend;

use App\Packages\PPT\PPT;
use Str;
use Storage;
use App\Packages\Excel\CustomExcel;
use Illuminate\Validation\ValidationException;

/**
 * 自定义数据导出Excel服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class CustomExportService
{

    /**
     * 转换并导出数据
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $template
     * @param  string $type
     * @param  array  $data
     * @return string
     */
    public function makeExcel(string $template,string $type,array $data): string
    {
        $extension = $type == 'excel' ? 'xlsx' : 'pptx';
        $templatePath = resource_path('/templates/'.$template.'.'.$extension);
        if(!file_exists($templatePath))
        {
            throw ValidationException::withMessages(['template.incorrect' => '模板不存在或已删除']);
        }
        if(!is_dir(Storage::path('public/exports')))
        {
            Storage::makeDirectory('public/exports');
        }
        $fileName = 'public/exports/'.Str::uuid().'.'.$extension;
        $templateName = ucfirst(Str::camel($template));
        if($type == 'excel')
        {
            $excel = new CustomExcel(Storage::path($fileName));
            $excel->loadTemplate($templatePath);
            $excel->makeExcelByTemplate($templateName,$data);
        }
        if($type == 'ppt')
        {
            $ppt = new PPT(Storage::path($fileName));
            $ppt->loadTemplate($templatePath);
            $ppt->makeByTemplate($templateName,$data);
        }
        return url(Storage::url($fileName));
    }

}