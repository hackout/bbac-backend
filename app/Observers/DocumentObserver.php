<?php

namespace App\Observers;

use App\Models\Document;
use App\Services\Private\DocumentLogService;

class DocumentObserver
{
    /**
     * Handle the Document "created" event.
     */
    public function created(Document $document): void
    {
        (new DocumentLogService)->updateLogId($document);
    }

    /**
     * Handle the Document "deleted" event.
     */
    public function deleted(Document $document): void
    {
        $document->logs && $document->logs->each(fn($item) => $item->delete());
    }
}
