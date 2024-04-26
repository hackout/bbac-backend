<?php

namespace App\Listeners;

use App\Events\TorqueChanged;
use App\Services\Private\NoticeService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TorqueChangedListener
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
    public function handle(TorqueChanged $event): void
    {
        (new NoticeService)->makeNoticeByTorqueChanged($event->torqueChangeRecord);
    }
}
