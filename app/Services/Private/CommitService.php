<?php
namespace App\Services\Private;

use App\Events\CommitApproveSuccess;
use App\Models\Commit;
use App\Models\User;
use App\Services\Service;
use Illuminate\Support\Collection;

/**
 * 版本历史服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class CommitService extends Service
{

    public ?string $className = Commit::class;

    /**
     * 绑定考核
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $id
     * @param  string $examine_id
     * @return void
     */
    public function bindExamine(string $id, string $examine_id)
    {
        parent::setValue($id, 'examine_id', $examine_id);
    }

    /**
     * 更新审核状态
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $commit_id
     * @return void
     */
    public function updateToPending(string $commit_id)
    {
        parent::setValue($commit_id, 'status', Commit::STATUS_PENDING);
    }

    public function approveSuccess(Commit $commit)
    {
        parent::setValue($commit->id, 'status', Commit::STATUS_SUCCESS);
        CommitApproveSuccess::dispatch($commit);
    }


    public function approveReject(Commit $commit)
    {
        parent::setValue($commit->id, 'status', Commit::STATUS_REJECT);
    }
}