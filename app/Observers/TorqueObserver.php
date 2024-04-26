<?php

namespace App\Observers;

use App\Models\Torque;
use App\Services\Private\TorqueItemService;

class TorqueObserver
{
    /**
     * Handle the Torque "created" event.
     */
    public function created(Torque $torque): void
    {
        if ($torque->quantity > 0) {
            (new TorqueItemService)->makeItemByTorque($torque);
        }
    }

    /**
     * Handle the Torque "updated" event.
     */
    public function updated(Torque $torque): void
    {
        if ($torque->getOriginal('quantity') < $torque->quantity) {
            (new TorqueItemService)->updateItemByTorque($torque);
        }
    }

    /**
     * Handle the Torque "deleted" event.
     */
    public function deleted(Torque $torque): void
    {
        $torque->torque_items && $torque->torque_items->each(fn($item) => $item->delete());
        $torque->torque_item_details && $torque->torque_item_details->each(fn($item) => $item->delete());
        $torque->torque_item_fixtures && $torque->torque_item_fixtures->each(fn($item) => $item->delete());
        $torque->torque_item_monitors && $torque->torque_item_monitors->each(fn($item) => $item->delete());
    }

    /**
     * Handle the Torque "restored" event.
     */
    public function restored(Torque $torque): void
    {
        //
    }

    /**
     * Handle the Torque "force deleted" event.
     */
    public function forceDeleted(Torque $torque): void
    {
        //
    }
}
