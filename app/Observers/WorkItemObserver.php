<?php

namespace App\Observers;

use App\Models\WorkItem;
use App\Services\Private\TaskService;
use App\Services\Private\WorkService;

class WorkItemObserver
{
    /**
     * Handle the WorkItem "created" event.
     */
    public function created(WorkItem $workItem): void
    {
        (new WorkService)->addPeriodByItem($workItem);
        (new TaskService)->addUserByWorkItem($workItem);
    }

    /**
     * Handle the WorkItem "updated" event.
     */
    public function updated(WorkItem $workItem): void
    {
        //
    }
    
    /**
     * Handle the WorkItem "deleted" event.
     */
    public function deleted(WorkItem $workItem): void
    {
        
    }

    /**
     * Handle the WorkItem "restored" event.
     */
    public function restored(WorkItem $workItem): void
    {
        //
    }

    /**
     * Handle the WorkItem "force deleted" event.
     */
    public function forceDeleted(WorkItem $workItem): void
    {
        //
    }
}
