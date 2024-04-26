<?php

namespace App\Jobs;

use App\Services\Private\FileService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StorageChanged implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private string $path)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        (new FileService)->makeFiles($this->path);
    }
}
