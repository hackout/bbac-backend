<?php

namespace App\Observers;

use App\Models\TaskItem;
use App\Services\Private\TaskService;

class TaskItemObserver
{
    /**
     * Handle the TaskItem "created" event.
     */
    public function created(TaskItem $taskItem): void
    {

    }

    /**
     * Handle the TaskItem "updated" event.
     */
    public function saved(TaskItem $taskItem): void
    {
        if($taskItem->getOriginal('content') == null && $taskItem->content !== null)
        {
            (new TaskService)->checkStartByItem($taskItem);
        }
    }

    /**
     * Handle the TaskItem "deleted" event.
     */
    public function deleted(TaskItem $taskItem): void
    {
        
    }

    /**
     * Handle the TaskItem "restored" event.
     */
    public function restored(TaskItem $taskItem): void
    {
        //
    }

    /**
     * Handle the TaskItem "force deleted" event.
     */
    public function forceDeleted(TaskItem $taskItem): void
    {
        //
    }
}
