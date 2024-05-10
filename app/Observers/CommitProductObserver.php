<?php

namespace App\Observers;

use App\Models\CommitProduct;
use App\Services\Private\CommitProductItemService;

class CommitProductObserver
{
    /**
     * Handle the CommitProduct "created" event.
     */
    public function created(CommitProduct $commitProduct): void
    {
        if ($commitProduct->parent_id) {
            (new CommitProductItemService)->copyItem($commitProduct);
        }
    }

    /**
     * Handle the CommitProduct "updated" event.
     */
    public function updated(CommitProduct $commitProduct): void
    {

    }

    public function saved(CommitProduct $commitProduct): void
    {

    }

    /**
     * Handle the CommitProduct "deleted" event.
     */
    public function deleted(CommitProduct $commitProduct): void
    {
        $commitProduct->items && $commitProduct->items->each(fn($item) => $item->delete());
        $commitProduct->approves && $commitProduct->approves->each(fn($item) => $item->delete());
    }

    /**
     * Handle the CommitProduct "restored" event.
     */
    public function restored(CommitProduct $commitProduct): void
    {
        //
    }

    /**
     * Handle the CommitProduct "force deleted" event.
     */
    public function forceDeleted(CommitProduct $commitProduct): void
    {
        //
    }
}