<?php
namespace App\Services\Private;

use App\Models\Torque;
use App\Models\TorqueItem;
use App\Services\Service;

/**
 * 基础总成信息服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class TorqueItemService extends Service
{

    public ?string $className = TorqueItem::class;

    public function makeItemByTorque(Torque $torque)
    {
        $quantity = $torque->quantity;
        $sql = [];
        for ($i = 0; $i < $quantity; $i++) {
            $sql[] = [
                'number' => $torque->number . '_' . substr(1000 + $i + 1, 1),
                'is_issue' => false
            ];
        }
        if ($quantity && $sql) {
            $torque->torque_items()->createMany($sql);
        }
        parent::clearCache();
    }

    public function updateItemByTorque(Torque $torque)
    {
        $quantity = $torque->quantity;
        $sql = [];
        for ($i = 0; $i < $quantity; $i++) {
            $_sql = [
                'number' => $torque->number . '_' . substr(1000 + $i + 1, 1),
                'is_issue' => false
            ];
            if(!parent::find(['number' => $_sql['number']]))
            {
                $sql[] = $_sql;
            }
        }
        if ($quantity && $sql) {
            $torque->torque_items()->createMany($sql);
        }
        parent::clearCache();
    }
}