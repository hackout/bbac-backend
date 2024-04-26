<?php
namespace App\Services\Private;

use App\Models\Document;
use App\Models\DocumentLog;
use App\Services\Service;

/**
 * 指导书服务
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
class DocumentService extends Service
{

    public ?string $className = Document::class;

    /**
     * 新增指导书同步记录
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  DocumentLog $documentLog
     * @return void
     */
    public function updateByLog(DocumentLog $documentLog)
    {
        if ($item = parent::find(['type' => $documentLog->type, 'engine' => $documentLog->engine])) {
            $sql = [
                'user_id' => $documentLog->user_id,
                'name' => $documentLog->name,
                'is_valid' => $documentLog->is_valid
            ];
            parent::update($item->id, $sql);
        } else {
            $sql = [
                'user_id' => $documentLog->user_id,
                'name' => $documentLog->name,
                'engine' => $documentLog->engine,
                'is_valid' => $documentLog->is_valid,
                'type' => $documentLog->type,
            ];
            parent::create($sql);
        }
    }

    public function syncDocumentMedia(DocumentLog $documentLog)
    {
        $item = $documentLog->document;
        if ($item) {
            $item->media && $item->media()->each(fn($media) => $media->delete());
            $file = $documentLog->getFirstMediaPath(DocumentLog::MEDIA_FILE);
            if ($file) {
                $item->addMedia($file)->toMediaCollection(Document::MEDIA_FILE);
            }
            $this->clearCache();
        }
    }
}