<?php
namespace App\Services\Private;

use App\Models\Torque;
use App\Models\TorqueChangeRecord;
use App\Services\Service;
use Illuminate\Support\Collection;

/**
 * 扭矩服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class TorqueService extends Service
{

    public ?string $className = Torque::class;

    /**
     * 根据审核通过订单进行更新
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  TorqueChangeRecord $torqueChangeRecord
     * @return void
     */
    public function updateDataByRecord(TorqueChangeRecord $torqueChangeRecord)
    {
        $sql = [
            'user_id' => $torqueChangeRecord->user_id
        ];
        foreach($torqueChangeRecord->extra as $rs)
        {
            $sql[$rs['field']] = $rs['content'];
        }
        parent::update($torqueChangeRecord->torque_id,$sql);
        parent::clearCache();
    }

    /**
     * 获取扭矩列表
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @return Collection
     */
    public function getOption():Collection
    {
        return parent::getAll([
            'id as value',
            'number as name'
        ]);
    }
}