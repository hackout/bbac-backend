<?php
namespace App\Packages\Excel;

use App\Services\Private\DictItemService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Html;

/**
 * Excel 模板操作类型
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class LoadExcel
{

    /**
     * 获取Json模板路径
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string      $type 目录
     * @param  string      $name 文件名
     * @return string|null
     */
    private function getFileName(string $type, string $name): ?string
    {
        $fileName = resource_path('json/template/'.$type.'/'.$name.'.json');
        return file_exists($fileName) ? $fileName : null;
    }

    /**
     * 依据发动机型号查询审计模板是否存在
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string  $type
     * @param  integer $engine
     * @return boolean
     */
    public function checkEngineViewTemplate(string $type, int $engine): bool
    {
        $engineName = (new DictItemService)->toName('engine_model', $engine);
        if (!$engineName)
            return false;
        $fileName = $this->getFileName($type, $engineName);
        return !empty($fileName);
    }

    /**
     * 依据发动机型号获取审计模板
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string     $type
     * @param  integer    $engine
     * @return array|null
     */
    public function getEngineViewTemplateFileName(string $type, int $engine):?array
    {
        $engineName = (new DictItemService)->toName('engine_model', $engine);
        $result = [];
        if (!$engineName)
            return $result;
        $fileName = $this->getFileName($type, $engineName);
        if (empty($fileName))
            return $result;
            $result = json_decode(file_get_contents($fileName),true);
        return $result;
    }

}