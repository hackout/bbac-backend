<?php
namespace App\Services\Private;

use App\Models\IssueProduct;
use App\Models\IssueProductLog;
use App\Services\Service;

/**
 * 产品考核-考核项(历史)服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property IssueProductLog $item
 */
class IssueProductLogService extends Service
{

    public ?string $className = IssueProductLog::class;

    private $changeFill = [
        'plant',
        'line',
        'engine',
        'stage',
        'assembly_id',
        'product_id',
        'part_id',
        'defect_description',
        'defect_level',
        'defect_part',
        'defect_position',
        'defect_cause',
        'soma',
        'lama',
        'note',
        'eight_disciplines',
        'status',
        'type',
        'is_ok',
    ];


    /**
     * 添加创建日志
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  IssueProduct $issueProduct
     * @return void
     */
    public function addLogByCreated(IssueProduct $issueProduct)
    {
        $sql = [
            'issue_product_id' => $issueProduct->id,
            'user_id' => $issueProduct->author_id,
            'code' => 'created',
            'extra' => [],
        ];
        foreach($this->changeFill as $field)
        {
            if($issueProduct->$field !== null)
            {
                $sql['extra'][] = [
                    'field' => $field,
                    'action' => 'add',
                    'content' => $issueProduct->$field
                ];
            }
        }

        parent::create($sql);
    }

    /**
     * 添加更新日志
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  IssueProduct $issueProduct
     * @return void
     */
    public function addLogByUpdated(IssueProduct $issueProduct)
    {
        $sql = [
            'issue_vehicle_id' => $issueProduct->id,
            'user_id' => $issueProduct->user_id,
            'code' => 'updated',
            'extra' => [],
        ];
        foreach($this->changeFill as $field)
        {
            if($issueProduct->$field !== null && $issueProduct->getOriginal($field) === null)
            {
                $sql['extra'][] = [
                    'field' => $field,
                    'action' => 'add',
                    'content' => $issueProduct->$field
                ];
                continue;
            }
            if($issueProduct->$field === null && $issueProduct->getOriginal($field) !== null)
            {
                $sql['extra'][] = [
                    'field' => $field,
                    'action' => 'remove',
                    'content' => $issueProduct->getOriginal($field)
                ];
                continue;
            }
            if($issueProduct->$field != $issueProduct->getOriginal($field))
            {
                $sql['extra'][] = [
                    'field' => $field,
                    'action' => 'change',
                    'content' => $issueProduct->$field
                ];
            }
        }

        parent::create($sql);
    }

}
