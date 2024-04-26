<?php
namespace App\Services\Backend;

use App\Models\Dict;
use App\Services\Service;
use App\Traits\ExportTemplateTrait;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

/**
 * 字典服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class DictService extends Service
{
    use ExportTemplateTrait;

    public ?string $className = Dict::class;

    /**
     * 通过字典标识获取字典项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string     $code
     * @return Collection
     * 
     * @throws ValidationException
     */
    public function getOptionByCode(string $code): Collection
    {
        if (!$item = parent::find(['code' => $code])) {
            throw ValidationException::withMessages(['code.incorrect' => '字典信息错误']);
        }
        return $item->items->map(fn($option) => ['value' => $option->content, 'name' => $option->name])->values();
    }

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
            'keyword' => ['search', ['name', 'code', 'description']]
        ];
        parent::listQuery($data, $conditions);
        return parent::list();
    }


    /**
     * 通过字典标识及键值获取键名
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string  $code
     * @param  integer  $value
     * @return ?string
     */
    public function getNameByCode(string $code, int $value): ?string
    {
        if (!$item = parent::find(['code' => $code])) {
            return null;
        }
        return $item->items->where('content', $value)->value('name') ?? null;
    }
}