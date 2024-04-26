<?php

namespace App\Jobs;

use App\Models\DocumentLog;
use App\Services\Private\DocumentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncDocumentMedia implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private DocumentLog $documentLog)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        (new DocumentService)->syncDocumentMedia($this->documentLog);
    }
}
