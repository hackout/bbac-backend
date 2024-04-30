<?php

namespace App\Listeners;

use App\Models\CommitInline;
use App\Models\CommitVehicle;
use App\Models\CommitProduct;
use App\Events\CommitApproveSuccess;
use App\Services\Private\ExamineVehicleService;
use App\Services\Private\ExamineInlineService;
use App\Services\Private\ExamineProductService;
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
        if ($event->commit instanceof CommitInline) {
            (new ExamineInlineService)->syncByCommit($event->commit);
        }
        if ($event->commit instanceof CommitProduct) {
            (new ExamineProductService)->syncByCommit($event->commit);
        }
        if ($event->commit instanceof CommitVehicle) {
            (new ExamineVehicleService)->syncByCommit($event->commit);
        }
    }
}
