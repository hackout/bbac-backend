<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        //
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }
    
    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $user->accounts && $user->accounts()->delete();
        $user->profile && $user->profile()->delete();
        $user->roles && $user->roles()->detach();
        $user->logs && $user->logs()->delete();
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
