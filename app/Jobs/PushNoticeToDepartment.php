<?php

namespace App\Jobs;

use App\Models\Notice;
use App\Services\Private\NoticeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PushNoticeToDepartment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private Notice $notice)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        (new NoticeService)->push($this->notice);
    }
}
