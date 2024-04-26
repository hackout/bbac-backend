<?php
namespace App\Services\Backend;

use App\Models\User;
use App\Models\CommitItemOption;
use App\Services\Service;
use App\Traits\ExportTemplateTrait;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

/**
 * 实际测量项服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class CommitItemOptionService extends Service
{
    public ?string $className = CommitItemOption::class;

    /**
     * 获取实际测量项列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string     $commit_item_id
     * @return Collection
     */
    public function getList(string $commit_item_id): Collection
    {
        $condition = [
            'commit_item_id' => 'eq',
        ];
        parent::listQuery(['commit_item_id' => $commit_item_id], $condition);
        $this->orderKey = 'sort_order';
        $this->orderType = 'desc';
        return parent::getAll();
    }

    /**
     * 保存实际测量项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $commit_item_id
     * @param  array  $data
     * @return void
     */
    public function saveData(string $commit_item_id, array $data)
    {
        $commitItem = (new CommitItemService)->findById($commit_item_id);
        $newSql = [];
        $oldSql = [];
        $idList = [];
        $count = count($data);
        foreach ($data as $key=>$item) {
            $item['sort_order'] = $count - $key;
            if (array_key_exists('id', $item) && $item['id']) {
                if ($item['name_zh']) {
                    $oldSql[] = $item;
                    $idList[] = $item['id'];
                }
            } else {
                if ($item['name_zh']) {
                    $newSql[] = [
                        'name_zh' => $item['name_zh'],
                        'name_en' => $item['name_en'],
                        'sort_order' => $item['sort_order']
                    ];
                }
            }
        }

        //删除无关项
        if ($commitItem->options->count()) {
            if ($idList) {
                $commitItem->options()->whereNotIn('id', $idList)->delete();
            } else {
                $commitItem->options()->delete();
            }
        }

        //修改数据
        if ($oldSql) {
            foreach ($oldSql as $sql) {
                $commitItem->options()->where('id', $sql['id'])->update($sql);
            }
        }

        //保存新增数据
        if ($newSql) {
            $commitItem->options()->createMany($newSql);
        }

    }

}