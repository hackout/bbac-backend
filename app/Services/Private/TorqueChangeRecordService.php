<?php
namespace App\Services\Private;

use App\Models\Torque;
use App\Models\TorqueChangeRecord;
use App\Models\User;
use App\Services\Service;

/**
 * 基础总成信息服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class TorqueChangeRecordService extends Service
{

    public ?string $className = TorqueChangeRecord::class;

    /**
     * 写入变更记录并对扭矩数据进行变更
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Torque $torque
     * @param  User   $user
     * @param  array  $data
     * @return void
     */
    public function makeRecordByTorque(Torque $torque, User $user, array $data)
    {
        $sql = [
            'user_id' => $user->id,
            'torque_id' => $torque->id,
            'extra' => [],
            'is_io' => false,
            'status' => TorqueChangeRecord::STATUS_SUCCESS,
        ];
        $torqueData = $torque->toArray();
        foreach ($data as $key => $value) {
            if ($value != $torqueData[$value]) {
                $sql['extra'][] = [
                    'field' => $key,
                    'before' => $torqueData[$value],
                    'content' => $value
                ];
            }
        }
        parent::create($sql);
    }

}