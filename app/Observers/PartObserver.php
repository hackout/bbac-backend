<?php

namespace App\Observers;

use App\Events\PushPartToUser;
use App\Models\Part;
use App\Services\Private\PartService;

class PartObserver
{
    /**
     * Handle the Part "created" event.
     */
    public function created(Part $part): void
    {

    }

    /**
     * Handle the Part "saved" event.
     */
    public function saved(Part $part): void
    {
        
    }

    /**
     * Handle the Part "deleted" event.
     */
    public function deleted(Part $part): void
    {
        $part->items && $part->items->each(fn($item) => $item->delete());
    }

    /**
     * Handle the Part "restored" event.
     */
    public function restored(Part $part): void
    {
        //
    }

    /**
     * Handle the Part "force deleted" event.
     */
    public function forceDeleted(Part $part): void
    {
        //
    }
}
