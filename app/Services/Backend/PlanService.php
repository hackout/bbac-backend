<?php
namespace App\Services\Backend;

use App\Models\Plan;
use App\Services\Service;
use App\Traits\ExportTemplateTrait;

/**
 * 排产计划服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class PlanService extends Service
{
    use ExportTemplateTrait;

    public ?string $className = Plan::class;

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
            'keyword' => ['search', ['name']],
            'plant' => 'eq',
            'line' => 'eq'
        ];
        parent::listQuery($data, $conditions);
        return parent::list();
    }

}