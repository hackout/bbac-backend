<?php

namespace App\Listeners;

use App\Events\CommitApproveSuccess;
use App\Services\Private\ExamineService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CommitApproveSuccessListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CommitApproveSuccess $event): void
    {
        (new ExamineService)->createByCommit($event->commit);
    }
}
