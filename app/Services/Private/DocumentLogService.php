<?php
namespace App\Services\Private;

use App\Models\Document;
use App\Models\DocumentLog;
use App\Services\Service;

/**
 * 指导书记录服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class DocumentLogService extends Service
{

    public ?string $className = DocumentLog::class;

    /**
     * 新增指导书同步记录
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  Document $document
     * @return void
     */
    public function updateLogId(Document $document)
    {
        parent::updateV2([
            'engine' => $document->engine,
            'type' => $document->type
        ], ['document_id' => $document->id]);
    }

}