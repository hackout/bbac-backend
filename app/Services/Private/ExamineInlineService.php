<?php
namespace App\Services\Private;

use App\Models\CommitInline;
use App\Models\ExamineInline;
use App\Services\Service;

/**
 * 考核模板服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class ExamineInlineService extends Service
{

    public ?string $className = ExamineInline::class;

    /**
     * 同步审核版本信息
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  CommitInline $commit
     * @return void
     */
    public function syncByCommit(CommitInline $commit)
    {
        $sql = [
            'author_id' => $commit->author_id,
            'user_id' => $commit->user_id,
            'commit_inline_id' => optional($commit->parent)->id,
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
            if (parent::update($commit->examine_inline_id, $sql)) {
                (new ExamineInlineItemService)->updateByCommit($this->item, $commit);
            }
        } else {
            if (parent::create($sql)) {
                (new CommitInlineService)->bindExamine($commit->id, $this->item->id);
                (new ExamineInlineItemService)->createByCommit($this->item, $commit);
            }
        }
    }
}