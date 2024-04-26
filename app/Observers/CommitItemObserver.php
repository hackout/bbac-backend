<?php

namespace App\Observers;

use App\Models\CommitItem;
use App\Services\Private\CommitItemOptionService;

class CommitItemObserver
{
    /**
     * Handle the CommitItem "created" event.
     */
    public function created(CommitItem $commitItem): void
    {
        if ($commitItem->number > 0) {
           (new CommitItemOptionService)->createByCommitItem($commitItem);
        }
    }

    /**
     * Handle the CommitItem "updated" event.
     */
    public function updated(CommitItem $commitItem): void
    {
        if ($commitItem->number > 0 && $commitItem->number > $commitItem->getOriginal('number')) {
            (new CommitItemOptionService)->updateByCommitItem($commitItem);
        }
    }

    /**
     * Handle the CommitItem "deleted" event.
     */
    public function deleted(CommitItem $commitItem): void
    {
        $commitItem->items && $commitItem->items->each(fn($item) => $item->delete());
        $commitItem->approves && $commitItem->approves->each(fn($item) => $item->delete());
    }

    /**
     * Handle the CommitItem "restored" event.
     */
    public function restored(CommitItem $commitItem): void
    {
        //
    }

    /**
     * Handle the CommitItem "force deleted" event.
     */
    public function forceDeleted(CommitItem $commitItem): void
    {
        //
    }
}
