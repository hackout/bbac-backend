<?php

namespace App\Observers;

use App\Models\IssueProduct;
use App\Services\Private\IssueProductLogService;

class IssueProductObserver
{
    /**
     * Handle the IssueProduct "created" event.
     */
    public function created(IssueProduct $issueProduct): void
    {
        (new IssueProductLogService)->addLogByCreated($issueProduct);
    }

    /**
     * Handle the IssueProduct "updated" event.
     */
    public function updated(IssueProduct $issueProduct): void
    {
        
    }

    public function saved(IssueProduct $issueProduct): void
    {
        (new IssueProductLogService)->addLogByUpdated($issueProduct);
    }

    /**
     * Handle the IssueProduct "deleted" event.
     */
    public function deleted(IssueProduct $issueProduct): void
    {
        $issueProduct->logs && $issueProduct->logs->each(fn($item) => $item->delete());
    }

    /**
     * Handle the IssueProduct "restored" event.
     */
    public function restored(IssueProduct $issueProduct): void
    {
        //
    }

    /**
     * Handle the IssueProduct "force deleted" event.
     */
    public function forceDeleted(IssueProduct $issueProduct): void
    {
        //
    }
}
