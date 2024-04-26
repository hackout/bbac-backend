<?php

namespace App\Observers;

use App\Models\Commit;
use App\Services\Private\CommitItemService;
use App\Services\Private\ExamineService;

class CommitObserver
{
    /**
     * Handle the Commit "created" event.
     */
    public function created(Commit $commit): void
    {
        if ($commit->parent_id) {
            (new CommitItemService)->copyDataToCommit($commit);
        }
    }

    /**
     * Handle the Commit "updated" event.
     */
    public function updated(Commit $commit): void
    {
        
    }

    public function saved(Commit $commit): void
    {
        
    }

    /**
     * Handle the Commit "deleted" event.
     */
    public function deleted(Commit $commit): void
    {
        $commit->items && $commit->items->each(fn($item) => $item->delete());
        $commit->approves && $commit->approves->each(fn($item) => $item->delete());
    }

    /**
     * Handle the Commit "restored" event.
     */
    public function restored(Commit $commit): void
    {
        //
    }

    /**
     * Handle the Commit "force deleted" event.
     */
    public function forceDeleted(Commit $commit): void
    {
        //
    }
}
