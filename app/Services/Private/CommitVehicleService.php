<?php
namespace App\Services\Private;

use App\Events\CommitApproveSuccess;
use App\Models\CommitVehicle;
use App\Services\Service;

/**
 * 整车服务-考核项(历史)服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property CommitVehicle $item
 */
class CommitVehicleService extends Service
{

    public ?string $className = CommitVehicle::class;

    /**
     * 更新审核状态
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $commit_id
     * @return void
     */
    public function updateToPending(string $commit_id)
    {
        parent::setValue($commit_id, 'status', CommitVehicle::STATUS_PENDING);
        $this->clearCache();
    }

    /**
     * 审核通过
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  CommitVehicle $commit
     * @return void
     */
    public function approveSuccess(CommitVehicle $commit)
    {
        parent::setValue($commit->id, 'status', CommitVehicle::STATUS_SUCCESS);
        $this->clearCache();
        $commit->status = CommitVehicle::STATUS_SUCCESS;
        CommitApproveSuccess::dispatch($commit);
    }

    /**
     * 审核拒绝
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  CommitVehicle $commit
     * @return void
     */
    public function approveReject(CommitVehicle $commit)
    {
        parent::setValue($commit->id, 'status', CommitVehicle::STATUS_REJECT);
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
        parent::setValue($id, 'examine_vehicle_id', $examine_id);
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
            $commitVehicleItemService = new CommitVehicleItemService;
            foreach ($items as $item) {
                $thumbnail = $item['medias'];
                unset($item['medias']);
                $item['commit_vehicle_id'] = $this->item->id;
                $commitVehicleItemService->addItemByCommit($item, $thumbnail ?? []);
            }
        }
    }
}
