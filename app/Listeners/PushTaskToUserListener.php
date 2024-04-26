<?php

namespace App\Listeners;

use App\Events\PushTaskToUser;
use App\Services\Private\NoticeService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PushTaskToUserListener
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
    public function handle(PushTaskToUser $event): void
    {
        (new NoticeService)->makeNoticeByTask($event->task);
    }
}
