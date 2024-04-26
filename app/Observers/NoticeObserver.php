<?php

namespace App\Observers;

use App\Models\Notice;
use App\Services\Private\NoticeService;

class NoticeObserver
{
    /**
     * Handle the Notice "created" event.
     */
    public function created(Notice $notice): void
    {

    }

    /**
     * Handle the Notice "updated" event.
     */
    public function updated(Notice $notice): void
    {

    }

    /**
     * Handle the Notice "deleted" event.
     */
    public function deleted(Notice $notice): void
    {
        $notice->users && $notice->users()->detach();
    }

    /**
     * Handle the Notice "restored" event.
     */
    public function restored(Notice $notice): void
    {
        //
    }

    /**
     * Handle the Notice "force deleted" event.
     */
    public function forceDeleted(Notice $notice): void
    {
        //
    }
}
