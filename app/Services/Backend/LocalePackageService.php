<?php
namespace App\Services\Backend;

use App\Models\LocalePackage;
use App\Services\Service;
use App\Traits\ExportTemplateTrait;
use App\Traits\ImportTemplateTrait;

/**
 * 语言包服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class LocalePackageService extends Service
{
    use ImportTemplateTrait, ExportTemplateTrait;

    public ?string $className = LocalePackage::class;

    /**
     * 查询数据列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @return array
     */
    public function getList(array $data): array
    {
        $conditions = [
            'keyword' => ['search', ['code','content_zh','content_en']]
        ];
        parent::listQuery($data, $conditions);
        return parent::list();
    }
}