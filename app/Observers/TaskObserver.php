<?php

namespace App\Observers;

use App\Events\PushTaskToUser;
use App\Models\Task;
use App\Services\Private\TaskService;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {

    }

    /**
     * Handle the Task "updated" event.
     */
    public function saved(Task $task): void
    {
        if($task->getOriginal('user_id') != $task->user_id && $task->user_id)
        {
            PushTaskToUser::dispatch($task);
        }
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        $task->work_items && $task->work_items()->update(['work_id' => null]);
        $task->items && $task->items->each(fn($item) => $item->delete());
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        //
    }
}
