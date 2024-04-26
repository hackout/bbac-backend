<?php
namespace App\Services\Private;

use App\Models\Commit;
use App\Models\CommitItem;
use App\Services\Service;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * 版本考核项服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class CommitItemService extends Service
{

    public ?string $className = CommitItem::class;

    /**
     * 导入考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $md5Key
     * @param  array  $data
     * @return void
     */
    public function importItem(string $md5Key,array $data)
    {
        $commit_id = Cache::get('import_' . $md5Key);
        if($commit_id)
        {
            $commit = (new CommitService)->findById($commit_id);
            $commit->items()->createMany($data);
            parent::clearCache();
        }
    }

    /**
     * 复制上一个版本的考核项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Commit $commit
     * @return void
     */
    public function copyDataToCommit(Commit $commit)
    {
        if(optional($commit->parent)->items->count())
        {
            $commit->parent->items->each(function($item) use($commit){
                $newItem = $item->replicate();
                $newItem->commit_id = $commit->id;
                if($newItem->save())
                {
                    if($item->options->count())
                    {
                        $options = [];
                        foreach($item->options as $option)
                        {
                            $options[] = [
                                'name_zh' => $option->name_zh,
                                'name_en' => $option->name_en,
                                'sort_order' => $option->sort_order
                            ];
                        }
                        if($options)
                        {
                            $newItem->options()->createMany($options);
                        }
                    }
                }
            });
        }
    }
}
