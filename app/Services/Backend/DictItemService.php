<?php
namespace App\Services\Backend;

use App\Models\DictItem;
use App\Services\Service;
use App\Traits\ExportTemplateTrait;
use Illuminate\Validation\ValidationException;

/**
 * 字典项服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class DictItemService extends Service
{
    use ExportTemplateTrait;

    public ?string $className = DictItem::class;


    /**
     * 查询数据列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $data
     * @return array
     */
    public function getList(array $data): array
    {
        $code = trim($data['code']);
        $this->setHas([
            'dict' => function ($query) use ($code) {
                $query->where('code', $code);
            }
        ]);
        return parent::list();
    }

    /**
     * 创建数据
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array<string,string|integer>   $data
     * @return boolean
     */
    public function create(array $data): bool
    {
        extract(array_merge([
            'code' => null,
            'name' => null,
            'content' => 0,
            'sort_order' => 0,
        ], $data));
        $dict = (new DictService)->find(['code' => $code]);
        $sql = [
            'dict_id' => $dict->id,
            'name' => $data['name'],
            'content' => intval($data['content']),
            'sort_order' => intval($data['sort_order'])
        ];
        $result = parent::create($sql);
        if ($result) {
            (new DictService)->clearCache();
        }
        return $result;
    }

    /**
     * 更新数据
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  integer|string $id
     * @param  array<string,string|integer>          $data
     * @return boolean
     * 
     * @throws ValidationException
     */
    public function update(int|string $id, array $data): bool
    {
        extract(array_merge([
            'code' => null,
            'name' => null,
            'content' => 0,
            'sort_order' => 0,
        ], $data));
        $dict = (new DictService)->find(['code' => $code]);
        if (!$dict->items->where('id', $id)->first()) {
            throw ValidationException::withMessages(['id.not_exists' => '字典项不正确']);
        }
        $sql = [
            'name' => $data['name'],
            'content' => intval($data['content']),
            'sort_order' => intval($data['sort_order'])
        ];
        $result = parent::update($id, $sql);
        if ($result) {
            (new DictService)->clearCache();
        }
        return $result;
    }

    /**
     * 批量删除数据
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array<string,string|array<integer>> $ids
     * @return void
     * 
     * @throws ValidationException
     */
    public function batch_delete(array $ids)
    {
        $code = trim($ids['code']);
        $ids = (array) $ids['ids'];
        $dict = (new DictService)->find(['code' => $code]);
        $item_count = $dict->items->filter(fn($item) => in_array($item->id, $ids))->count();
        if (count($ids) != $item_count) {
            throw ValidationException::withMessages(['ids.not_exists' => '字典项不正确']);
        }
        parent::batch_delete($ids);
        (new DictService)->clearCache();
    }
}