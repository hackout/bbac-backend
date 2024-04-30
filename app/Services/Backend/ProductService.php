<?php
namespace App\Services\Backend;

use App\Models\Product;
use App\Services\Service;
use App\Traits\ExportTemplateTrait;
use App\Traits\ImportTemplateTrait;

/**
 * 发动机清单
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class ProductService extends Service
{
    use ExportTemplateTrait, ImportTemplateTrait;

    public ?string $className = Product::class;

    /**
     * 查询数据列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @return array
     */
    public function getList(array $data): array
    {
        $condition = [
            'keyword' => ['search', ['number']],
            'engine' => 'eq',
            'assembly_id' => 'eq'
        ];
        parent::listQuery($data, $condition);
        return parent::list();
    }

}