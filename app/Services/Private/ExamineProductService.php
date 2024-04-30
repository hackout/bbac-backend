<?php
namespace App\Services\Private;

use App\Models\CommitProduct;
use App\Models\ExamineProduct;
use App\Services\Service;

/**
 * 考核模板服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class ExamineProductService extends Service
{

    public ?string $className = ExamineProduct::class;

    /**
     * 同步审核版本信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  CommitProduct $commit
     * @return void
     */
    public function syncByCommit(CommitProduct $commit)
    {
        $sql = [
            'author_id' => $commit->author_id,
            'user_id' => $commit->user_id,
            'commit_product_id' => optional($commit->parent)->id,
            'version' => $commit->version,
            'name' => $commit->name,
            'description' => $commit->description,
            'engine' => $commit->engine,
            'period' => $commit->period,
            'is_valid' => true,
            'status' => $commit->status,
            'type' => $commit->type,
        ];
        if ($commit->examine) {
            if (parent::update($commit->examine_product_id, $sql)) {
                (new ExamineProductItemService)->updateByCommit($this->item, $commit);
            }
        } else {
            if (parent::create($sql)) {
                (new CommitProductService)->bindExamine($commit->id, $this->item->id);
                (new ExamineProductItemService)->createByCommit($this->item, $commit);
            }
        }
    }
}