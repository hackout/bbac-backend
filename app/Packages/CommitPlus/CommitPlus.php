<?php
namespace App\Packages\CommitPlus;

use App\Models\Commit;
use App\Services\Private\DictService;

class CommitPlus
{

    public function __construct(private Commit $commit)
    {
    }

    public function getLastCommit()
    {
        return $this->commit->parent;
    }

    /**
     * 获取考核变更项
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return array
     */
    public function getChanged(): array
    {
        $lastCommit = $this->getLastCommit();
        $lastCommitItems = optional($lastCommit)->items ?? collect([]);
        $items = $this->commit->items ?? collect([]);
        $deleted = [];
        $created = [];
        $updated = [];

        if ($lastCommit) {
            if ($lastCommit->name != $this->commit->name) {
                $updated[] = [
                    'id' => $this->commit->id,
                    'code' => 'name',
                    'before' => $lastCommit->name,
                    'content' => $this->commit->name
                ];
            }
            if ($lastCommit->description != $this->commit->description) {
                $updated[] = [
                    'id' => $this->commit->id,
                    'code' => 'description',
                    'before' => $lastCommit->description,
                    'content' => $this->commit->description
                ];
            }
            if ($lastCommit->engine != $this->commit->engine) {
                $updated[] = [
                    'id' => $this->commit->id,
                    'code' => 'engine',
                    'before' => (new DictService)->getNameByCode('engine_type', $lastCommit->engine),
                    'content' => (new DictService)->getNameByCode('engine_type', $this->commit->engine),
                ];
            }
            if ($lastCommit->period != $this->commit->period) {
                $updated[] = [
                    'id' => $this->commit->id,
                    'code' => 'period',
                    'before' => $lastCommit->period,
                    'content' => $this->commit->period
                ];
            }
        } else {

            if ($this->commit->name) {
                $created[] = [
                    'id' => $this->commit->id,
                    'code' => 'name',
                    'content' => $this->commit->name
                ];
            }
            if ($this->commit->description) {
                $created[] = [
                    'id' => $this->commit->id,
                    'code' => 'description',
                    'content' => $this->commit->description
                ];
            }
            if ($this->commit->engine) {
                $created[] = [
                    'id' => $this->commit->id,
                    'code' => 'engine',
                    'content' => (new DictService)->getNameByCode('engine_type', $this->commit->engine)
                ];
            }
            if ($this->commit->period) {
                $created[] = [
                    'id' => $this->commit->id,
                    'code' => 'period',
                    'content' => $this->commit->period
                ];
            }
        }

        $items->each(function ($item) use (&$created, &$updated, $lastCommitItems) {
            if ($item->content_zh) {
                $diffItem = $lastCommitItems->where('content_zh', $item->content_zh)->first();
                if (!$diffItem) {
                    $created[] = ['id' => $item->id, 'code' => 'items', 'content' => $item->content_zh];
                } else {
                    $diffItemArray = $diffItem->toArray();
                    $itemArray = $item->toArray();
                    unset ($diffItemArray['id'], $diffItemArray['created_at'], $diffItemArray['updated_at'], $itemArray['id'], $itemArray['created_at'], $itemArray['updated_at']);
                    if (json_encode($itemArray) != json_encode($diffItemArray)) {
                        $updated[] = [
                            'id' => $item->id,
                            'code' => 'items',
                            'content' => $item->content_zh
                        ];
                    }
                }
            }
        });
        if ($lastCommitItems) {
            $lastCommitItems->each(function ($item) use (&$deleted, $items) {
                if ($item->content_zh) {
                    if (!$items->filter(fn($n) => $n->content_zh != $item->content_zh)->first()) {
                        $deleted[] = [
                            'id' => $item->id,
                            'code' => 'items',
                            'content' => $item->content_zh
                        ];
                    }
                }
            });
        }

        return [
            'deleted' => $deleted,
            'created' => $created,
            'updated' => $updated,
        ];
    }
}