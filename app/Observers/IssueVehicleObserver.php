<?php

namespace App\Observers;

use App\Models\IssueVehicle;
use App\Services\Private\IssueVehicleLogService;
use App\Services\Private\ProductService;

class IssueVehicleObserver
{
    /**
     * Handle the IssueVehicle "created" event.
     */
    public function created(IssueVehicle $issueVehicle): void
    {
        (new IssueVehicleLogService)->addLogByCreated($issueVehicle);
        (new ProductService)->createOrUpdateByNumber($issueVehicle->eb_number);
    }

    /**
     * Handle the IssueVehicle "updated" event.
     */
    public function updated(IssueVehicle $issueVehicle): void
    {
        
    }

    public function saved(IssueVehicle $issueVehicle): void
    {
        (new IssueVehicleLogService)->addLogByUpdated($issueVehicle);
    }

    /**
     * Handle the IssueVehicle "deleted" event.
     */
    public function deleted(IssueVehicle $issueVehicle): void
    {
        $issueVehicle->logs && $issueVehicle->logs->each(fn($item) => $item->delete());
    }

    /**
     * Handle the IssueVehicle "restored" event.
     */
    public function restored(IssueVehicle $issueVehicle): void
    {
        //
    }

    /**
     * Handle the IssueVehicle "force deleted" event.
     */
    public function forceDeleted(IssueVehicle $issueVehicle): void
    {
        //
    }
}
