<?php
namespace App\Services\Private;

use App\Models\CommitItem;
use App\Models\CommitItemOption;
use App\Services\Service;
use Illuminate\Support\Collection;

/**
 * 版本历史服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class CommitItemOptionService extends Service
{

    public ?string $className = CommitItemOption::class;

    /**
     * 实际测量项增加
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  CommitItem $commitItem
     * @return void
     */
    public function createByCommitItem(CommitItem $commitItem)
    {
        $sql = [];
        for($i = 0;$i < $commitItem->number; $i++)
        {
            $sql[] = [
                'sort_order' => $commitItem->number - $i,
                'name_zh' => $commitItem->name_zh . substr(100 + $i + 1,1),
                'name_en' => $commitItem->name_en . substr(100 + $i + 1,1),
            ];
        }
        if($sql)
        {
            $commitItem->options()->createMany($sql);
            parent::clearCache();
        }
    }

    /**
     * 实际测量项-增量
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  CommitItem $commitItem
     * @return void
     */
    public function updateByCommitItem(CommitItem $commitItem)
    {
        $sql = [];
        $itemsCount = $commitItem->options->count();
        $endNumber = $commitItem->number - $itemsCount;
        for($i = 0;$i < $endNumber; $i++)
        {
            $sql[] = [
                'sort_order' => $commitItem->number - $i,
                'name_zh' => $commitItem->name_zh . substr(100 + $i + 1,1),
                'name_en' => $commitItem->name_en . substr(100 + $i + 1,1),
            ];
        }
        if($sql)
        {
            $commitItem->options()->createMany($sql);
            parent::clearCache();
        }
    }
}