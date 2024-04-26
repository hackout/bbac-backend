<?php
namespace App\Services\Backend;

use App\Models\Assembly;
use App\Services\Service;
use App\Traits\ExportTemplateTrait;
use App\Traits\ImportTemplateTrait;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

/**
 * 基础总成信息服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class AssemblyService extends Service
{
    use ExportTemplateTrait,ImportTemplateTrait;

    public ?string $className = Assembly::class;


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
            'type' => 'eq',
            'status' => 'eq'
        ];
        parent::listQuery($data, $conditions);
        return parent::list();
    }
    
    /**
     * 获取总成选项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param ?array $data
     * @return array
     */
    public function getOptions(array $data = []): Collection
    {
        $conditions = [
            'keyword' => ['search', ['name']],
            'plant' => 'eq',
            'line' => 'eq',
            'type' => 'eq'
        ];
        parent::listQuery($data,$conditions);
        return parent::getAll()->map(function($item){
            return [
                'value' => $item->id,
                'name' => $item->number
            ];
        });
    }

    /**
     * 机型获取总成ID列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  integer $type
     * @return array
     */
    public function getIdListByEngine(int $type):array
    {
        parent::setQuery(['type'=>$type]);
        return parent::getAll(['id'])->pluck('id')->toArray();
    }
}