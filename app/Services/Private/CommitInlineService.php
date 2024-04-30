<?php
namespace App\Services\Private;

use App\Events\CommitApproveSuccess;
use App\Models\CommitInline;
use App\Services\Service;

/**
 * 在线考核-考核项(历史)服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property CommitInline $item
 */
class CommitInlineService extends Service
{

    public ?string $className = CommitInline::class;

    /**
     * 更新审核状态
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $commit_id
     * @return void
     */
    public function updateToPending(string $commit_id)
    {
        parent::setValue($commit_id, 'status', CommitInline::STATUS_PENDING);
        $this->clearCache();
    }

    /**
     * 审核通过
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  CommitInline $commit
     * @return void
     */
    public function approveSuccess(CommitInline $commit)
    {
        parent::setValue($commit->id, 'status', CommitInline::STATUS_SUCCESS);
        $this->clearCache();
        $commit->status = CommitInline::STATUS_SUCCESS;
        CommitApproveSuccess::dispatch($commit);
    }

    /**
     * 审核拒绝
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  CommitInline $commit
     * @return void
     */
    public function approveReject(CommitInline $commit)
    {
        parent::setValue($commit->id, 'status', CommitInline::STATUS_REJECT);
        $this->clearCache();
    }

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
        parent::setValue($id, 'examine_inline_id', $examine_id);
    }

    /**
     * 导入数据并创建
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  array $sql
     * @param  array $items
     * @return void
     */
    public function importData(array $sql, array $items)
    {
        if (parent::create($sql)) {
            $commitInlineItemService = new CommitInlineItemService;
            foreach ($items as $item) {
                $thumbnail = $item['medias'];
                unset($item['medias']);
                $item['commit_inline_id'] = $this->item->id;
                $commitInlineItemService->addItemByCommit($item, $thumbnail ?? []);
            }
        }
    }
}
