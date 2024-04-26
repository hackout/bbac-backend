<?php
namespace App\Services\Private;

use App\Models\Commit;
use App\Models\Examine;
use App\Services\Service;

/**
 * 考核模板服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class ExamineService extends Service
{

    public ?string $className = Examine::class;

    /**
     * 添加版本
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Commit $commit
     * @return void
     */
    public function createByCommit(Commit $commit)
    {
        $sql = [
            'author_id' => $commit->author_id,
            'user_id' => $commit->user_id,
            'commit_id' => optional($commit->parent)->id,
            'version' => $commit->version,
            'name' => $commit->name,
            'description' => $commit->description,
            'engine' => $commit->engine,
            'period' => $commit->period,
            'is_valid' => true,
            'status' => $commit->status,
            'type' => $commit->type,
            'sub_type' => $commit->sub_type,
        ];
        if(parent::create($sql))
        {
            (new CommitService)->bindExamine($commit->id,$this->item->id);
            (new ExamineItemService)->updateByCommit($this->item,$commit);
        }
    }

    /**
     * 更新未审核版本
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Commit $commit
     * @return void
     */
    public function updateByNonApprove(Commit $commit)
    {
        $sql = [
            'version' => $commit->version,
            'name' => $commit->name,
            'description' => $commit->description,
            'engine' => $commit->engine,
            'period' => $commit->period,
        ];
        parent::update($commit->examine_id,$sql);
    }
}