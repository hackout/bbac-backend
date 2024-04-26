<?php
namespace App\Services\Backend;

use App\Models\TaskCron;
use App\Services\Service;

/**
 * 任务配置服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class TaskCronService extends Service
{

    public ?string $className = TaskCron::class;


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
            'is_valid' => 'eq',
            'examine_type' => ['eq', 'type']
        ];
        parent::listQuery($data, $conditions);
        return parent::list([
            'id',
            'user_id',
            'examine_id',
            'type',
            'name',
            'plant',
            'line',
            'engine',
            'status',
            'assembly_id',
            'days',
            'yield',
            'yield_unit',
            'is_valid',
            'period',
            'items_count',
        ]);
    }


}