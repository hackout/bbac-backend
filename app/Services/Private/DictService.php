<?php
namespace App\Services\Private;

use App\Models\Dict;
use App\Services\Service;
use Illuminate\Support\Collection;

/**
 * 字典服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class DictService extends Service
{

    public ?string $className = Dict::class;

    /**
     * 通过字典标识获取字典项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string     $code
     * @return ?Collection
     */
    public function getOptionByCode(string $code): Collection
    {
        if (!$item = parent::find(['code' => $code])) {
            return collect([]);
        }
        return $item->items->map(fn($option) => ['value' => $option->content, 'name' => $option->name])->values();
    }

    /**
     * 通过字典标识及键名获取键值
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string  $code
     * @param  string  $name
     * @return integer
     */
    public function getValueByCode(string $code,string $name): int
    {
        if(!$item = parent::find(['code'=>$code]))
        {
            return 0;
        }
        return $item->items->where('name',$name)->value('content') ?? 0;
    }

    /**
     * 通过字典标识及键值获取键名
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string  $code
     * @param  integer  $value
     * @return ?string
     */
    public function getNameByCode(string $code,int $value): ?string
    {
        if(!$item = parent::find(['code'=>$code]))
        {
            return null;
        }
        return $item->items->where('content',$value)->value('name') ?? null;
    }
}