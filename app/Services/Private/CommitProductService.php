<?php
namespace App\Services\Private;

use App\Services\Service;
use App\Models\CommitProduct;
use App\Events\CommitApproveSuccess;

/**
 * 产品考核-考核项(历史)服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property CommitProduct $item
 */
class CommitProductService extends Service
{

    public ?string $className = CommitProduct::class;

    /**
     * 更新审核状态
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $commit_id
     * @return void
     */
    public function updateToPending(string $commit_id)
    {
        parent::setValue($commit_id, 'status', CommitProduct::STATUS_PENDING);
        $this->clearCache();
    }

    /**
     * 审核通过
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  CommitProduct $commit
     * @return void
     */
    public function approveSuccess(CommitProduct $commit)
    {
        parent::setValue($commit->id, 'status', CommitProduct::STATUS_SUCCESS);
        $this->clearCache();
        $commit->status = CommitProduct::STATUS_SUCCESS;
        CommitApproveSuccess::dispatch($commit);
    }

    /**
     * 审核拒绝
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  CommitProduct $commit
     * @return void
     */
    public function approveReject(CommitProduct $commit)
    {
        parent::setValue($commit->id, 'status', CommitProduct::STATUS_REJECT);
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
        parent::setValue($id, 'examine_product_id', $examine_id);
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
            $commitProductItemService = new CommitProductItemService;
            foreach ($items as $item) {
                $thumbnail = $item['medias'];
                unset($item['medias']);
                $item['commit_product_id'] = $this->item->id;
                $commitProductItemService->addItemByCommit($item, $thumbnail ?? []);
            }
        }
    }
}
