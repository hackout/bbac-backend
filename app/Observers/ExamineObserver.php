<?php

namespace App\Observers;

use App\Models\Examine;
use App\Services\Private\ExamineService;

class ExamineObserver
{
    /**
     * Handle the Examine "created" event.
     */
    public function created(Examine $examine): void
    {

    }

    /**
     * Handle the Examine "updated" event.
     */
    public function updated(Examine $examine): void
    {

    }

    /**
     * Handle the Examine "deleted" event.
     */
    public function deleted(Examine $examine): void
    {
        $examine->commits && $examine->commits->each(fn($item) => $item->delete());
        $examine->items && $examine->items->each(fn($item) => $item->delete());
    }

    /**
     * Handle the Examine "restored" event.
     */
    public function restored(Examine $examine): void
    {
        //
    }

    /**
     * Handle the Examine "force deleted" event.
     */
    public function forceDeleted(Examine $examine): void
    {
        //
    }
}
