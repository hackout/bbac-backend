<?php
namespace App\Services\Frontend;

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
     * 批量获取选项列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $codeList
     * @return array
     */
    public function getOptions(array $codeList):array
    {
        $result = [];
        foreach($codeList as $code)
        {
            $result[$code] = $this->getOptionByCode($code);
        }
        return $result;
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