<?php

namespace App\Observers;

use App\Events\TorqueChanged;
use App\Models\TorqueChangeRecord;
use App\Services\Private\TorqueService;

class TorqueChangeRecordObserver
{
    /**
     * Handle the TorqueChangeRecord "created" event.
     */
    public function created(TorqueChangeRecord $torqueChangeRecord): void
    {
        if (
            $torqueChangeRecord->status == TorqueChangeRecord::STATUS_SUCCESS &&
            $torqueChangeRecord->extra
        ) {
            (new TorqueService)->updateDataByRecord($torqueChangeRecord);
        } elseif ($torqueChangeRecord->status == TorqueChangeRecord::STATUS_PENDING) {
            TorqueChanged::dispatch($torqueChangeRecord);
        }
    }

    /**
     * Handle the TorqueChangeRecord "saved" event.
     */
    public function saved(TorqueChangeRecord $torqueChangeRecord): void
    {
        if (
            $torqueChangeRecord->getOriginal('status') == TorqueChangeRecord::STATUS_PENDING &&
            $torqueChangeRecord->status == TorqueChangeRecord::STATUS_SUCCESS &&
            $torqueChangeRecord->extra
        ) {
            (new TorqueService)->updateDataByRecord($torqueChangeRecord);
        }
    }

}
