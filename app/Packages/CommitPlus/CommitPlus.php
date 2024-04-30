<?php
namespace App\Packages\CommitPlus;

use App\Models\CommitInline;
use App\Models\CommitVehicle;
use App\Models\CommitProduct;

class CommitPlus
{
    private string $model;

    public function __construct(private CommitVehicle|CommitInline|CommitProduct $commit)
    {
        $this->model = get_class($this->commit);
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
        $fillable = collect((new $this->model)->getFillable())->filter(fn($n) => !strpos($n, '_id') && !in_array($n, ['id', 'is_valid', 'status','version']))->values()->toArray();
        if ($lastCommit) {
            foreach ($fillable as $keyName) {
                if ($lastCommit->$keyName != $this->commit->$keyName) {
                    $updated[] = [
                        'id' => $this->commit->id,
                        'code' => $keyName,
                        'before' => $lastCommit->$keyName,
                        'content' => $this->commit->$keyName
                    ];
                }
            }
        } else {
            foreach ($fillable as $keyName) {
                $created[] = [
                    'id' => $this->commit->id,
                    'code' => $keyName,
                    'before' => null,
                    'content' => $this->commit->$keyName
                ];
            }
        }

        $items->each(function ($item) use (&$created, &$updated, $lastCommitItems) {
            $fillable = collect($item->getFillable())->filter(fn($n) => !strpos($n, '_id') && $n != 'id')->values()->toArray();
            $beforeItem = $lastCommitItems->where('unique_id', $item->unique_id)->first();
            if ($beforeItem) {
                $now = json_encode($item->pluck($fillable)->toArray());
                $before = json_encode($beforeItem->pluck($fillable)->toArray());
                if ($now != $before) {
                    $updated[] = [
                        'id' => $item->id,
                        'code' => 'items',
                        'before' => null,
                        'content' => $item->content
                    ];
                }
            } else {
                $created[] = ['id' => $item->id, 'code' => 'items', 'before' => null, 'content' => $item->content];
            }
        });
        if ($lastCommitItems) {
            $lastCommitItems->each(function ($item) use (&$deleted, $items) {
                if (!$items->where('unique_id', $item->unique_id)->first()) {
                    $deleted[] = [
                        'id' => $item->id,
                        'code' => 'items',
                        'content' => $item->content
                    ];
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