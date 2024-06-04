<?php

namespace App\Observers;

use DB;
use Str;
use App\Models\CommitVehicle;
use App\Services\Private\CommitVehicleItemService;

class CommitVehicleObserver
{
    /**
     * Handle the CommitVehicle "created" event.
     */
    public function created(CommitVehicle $commitVehicle): void
    {
        if ($commitVehicle->parent_id) {
            (new CommitVehicleItemService)->copyItem($commitVehicle);
        } else {
            if ($commitVehicle->items->count()) {
                $first = $commitVehicle->items->first();
                DB::table($first->getTable())->where('commit_vehicle_id', $commitVehicle->id)->whereIsNull('unique_id')->update(['unique_id' => Str::uuid()]);
            }
        }
    }

    /**
     * Handle the CommitVehicle "updated" event.
     */
    public function updated(CommitVehicle $commitVehicle): void
    {

    }

    public function saved(CommitVehicle $commitVehicle): void
    {

    }

    /**
     * Handle the CommitVehicle "deleted" event.
     */
    public function deleted(CommitVehicle $commitVehicle): void
    {
        $commitVehicle->items && $commitVehicle->items->each(fn($item) => $item->delete());
        $commitVehicle->approves && $commitVehicle->approves->each(fn($item) => $item->delete());
    }

    /**
     * Handle the CommitVehicle "restored" event.
     */
    public function restored(CommitVehicle $commitVehicle): void
    {
        //
    }

    /**
     * Handle the CommitVehicle "force deleted" event.
     */
    public function forceDeleted(CommitVehicle $commitVehicle): void
    {
        //
    }
}
