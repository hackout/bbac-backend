<?php
namespace App\Services\Private;

use App\Models\IssueInline;
use App\Models\IssueInlineLog;
use App\Services\Service;

/**
 * 在线考核-考核项(历史)服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 * @property IssueInlineLog $item
 */
class IssueInlineLogService extends Service
{

    public ?string $className = IssueInlineLog::class;

    private $changeFill = [
        'plant',
        'line',
        'engine',
        'stage',
        'station',
        'assembly_id',
        'product_id',
        'affect_scope',
        'ira',
        'issue_description',
        'defect_level',
        'reason',
        'cause',
        'category',
        'soma',
        'soma_due',
        'lama',
        'lama_due',
        'note',
        'eight_disciplines',
        'status',
        'type',
    ];

    /**
     * 添加创建日志
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  IssueInline $issueInline
     * @return void
     */
    public function addLogByCreated(IssueInline $issueInline)
    {
        $sql = [
            'issue_inline_id' => $issueInline->id,
            'user_id' => $issueInline->author_id,
            'extra' => [],
        ];
        foreach($this->changeFill as $field)
        {
            if($issueInline->$field !== null)
            {
                $sql['extra'][] = [
                    'field' => $field,
                    'action' => 'add',
                    'content' => $issueInline->$field
                ];
            }
        }
    }

    /**
     * 添加更新日志
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  IssueInline $issueInline
     * @return void
     */
    public function addLogByUpdated(IssueInline $issueInline)
    {
        $sql = [
            'issue_vehicle_id' => $issueInline->id,
            'user_id' => $issueInline->user_id,
            'code' => 'updated',
            'extra' => [],
        ];
        foreach($this->changeFill as $field)
        {
            if($issueInline->$field !== null && $issueInline->getOriginal($field) === null)
            {
                $sql['extra'][] = [
                    'field' => $field,
                    'action' => 'add',
                    'content' => $issueInline->$field
                ];
                continue;
            }
            if($issueInline->$field === null && $issueInline->getOriginal($field) !== null)
            {
                $sql['extra'][] = [
                    'field' => $field,
                    'action' => 'remove',
                    'content' => $issueInline->getOriginal($field)
                ];
                continue;
            }
            if($issueInline->$field != $issueInline->getOriginal($field))
            {
                $sql['extra'][] = [
                    'field' => $field,
                    'action' => 'change',
                    'content' => $issueInline->$field
                ];
            }
        }

        parent::create($sql);
    }

}
