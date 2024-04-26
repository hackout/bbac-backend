<?php

namespace App\Observers;

use App\Models\ExamineItem;

class ExamineItemObserver
{
    /**
     * Handle the ExamineItem "created" event.
     */
    public function created(ExamineItem $examine): void
    {

    }

    /**
     * Handle the ExamineItem "updated" event.
     */
    public function updated(ExamineItem $examine): void
    {

    }

    /**
     * Handle the ExamineItem "deleted" event.
     */
    public function deleted(ExamineItem $examine): void
    {
        $examine->options && $examine->options->each(fn($item) => $item->delete());
    }

    /**
     * Handle the ExamineItem "restored" event.
     */
    public function restored(ExamineItem $examine): void
    {
        //
    }

    /**
     * Handle the ExamineItem "force deleted" event.
     */
    public function forceDeleted(ExamineItem $examine): void
    {
        //
    }
}
