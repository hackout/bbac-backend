<?php

namespace App\Observers;

use App\Models\DocumentLog;
use App\Services\Private\DocumentService;

class DocumentLogObserver
{
    /**
     * Handle the DocumentLog "created" event.
     */
    public function created(DocumentLog $documentLog): void
    {
        (new DocumentService)->updateByLog($documentLog);
    }

}
