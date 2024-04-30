<?php
namespace App\Services\Private;

use App\Models\IssueVehicle;
use App\Models\IssueVehicleLog;
use App\Services\Service;

/**
 * 整车服务-考核项(历史)服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property IssueVehicleLog $item
 */
class IssueVehicleLogService extends Service
{

    public ?string $className = IssueVehicleLog::class;

    private $changeFill = [
        'shift',
        'plant',
        'eb_type',
        'product_number',
        'sensor',
        'eb_number',
        'car_line',
        'car_type',
        'is_block',
        'description',
        'initial_analysis',
        'initial_action',
        'block_status',
        'block_content',
        'status',
        'type',
        'defect_level',
        'soma',
        'lama',
        'eight_disciplines',
        'ira',
        'is_confirm',
        'is_ppm',
        'is_pre_highlight',
        'detect_area',
        'quantity',
        'cause',
        'relate_parts',
        'cause_type',
        'delivery_at',
    ];

    /**
     * 添加创建日志
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  IssueVehicle $issueVehicle
     * @return void
     */
    public function addLogByCreated(IssueVehicle $issueVehicle)
    {
        $sql = [
            'issue_vehicle_id' => $issueVehicle->id,
            'user_id' => $issueVehicle->author_id,
            'code' => 'created',
            'extra' => [],
        ];
        foreach($this->changeFill as $field)
        {
            if($issueVehicle->$field !== null)
            {
                $sql['extra'][] = [
                    'field' => $field,
                    'action' => 'add',
                    'content' => $issueVehicle->$field
                ];
            }
        }

        parent::create($sql);
    }

    /**
     * 添加更新日志
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  IssueVehicle $issueVehicle
     * @return void
     */
    public function addLogByUpdated(IssueVehicle $issueVehicle)
    {
        $sql = [
            'issue_vehicle_id' => $issueVehicle->id,
            'user_id' => $issueVehicle->user_id,
            'code' => 'updated',
            'extra' => [],
        ];
        foreach($this->changeFill as $field)
        {
            if($issueVehicle->$field !== null && $issueVehicle->getOriginal($field) === null)
            {
                $sql['extra'][] = [
                    'field' => $field,
                    'action' => 'add',
                    'content' => $issueVehicle->$field
                ];
                continue;
            }
            if($issueVehicle->$field === null && $issueVehicle->getOriginal($field) !== null)
            {
                $sql['extra'][] = [
                    'field' => $field,
                    'action' => 'remove',
                    'content' => $issueVehicle->getOriginal($field)
                ];
                continue;
            }
            if($issueVehicle->$field != $issueVehicle->getOriginal($field))
            {
                $sql['extra'][] = [
                    'field' => $field,
                    'action' => 'change',
                    'content' => $issueVehicle->$field
                ];
            }
        }

        parent::create($sql);
    }

}
