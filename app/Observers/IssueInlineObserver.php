<?php

namespace App\Observers;

use App\Models\IssueInline;
use App\Services\Private\IssueInlineLogService;

class IssueInlineObserver
{
    /**
     * Handle the IssueInline "created" event.
     */
    public function created(IssueInline $issueInline): void
    {
        (new IssueInlineLogService)->addLogByCreated($issueInline);
    }

    /**
     * Handle the IssueInline "updated" event.
     */
    public function updated(IssueInline $issueInline): void
    {
        
    }

    public function saved(IssueInline $issueInline): void
    {
        (new IssueInlineLogService)->addLogByUpdated($issueInline);
    }

    /**
     * Handle the IssueInline "deleted" event.
     */
    public function deleted(IssueInline $issueInline): void
    {
        $issueInline->logs && $issueInline->logs->each(fn($item) => $item->delete());
    }

    /**
     * Handle the IssueInline "restored" event.
     */
    public function restored(IssueInline $issueInline): void
    {
        //
    }

    /**
     * Handle the IssueInline "force deleted" event.
     */
    public function forceDeleted(IssueInline $issueInline): void
    {
        //
    }
}
