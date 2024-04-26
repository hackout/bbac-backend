<?php

namespace App\Observers;

use App\Models\BirthdayCard;

class BirthdayCardObserver
{
    /**
     * Handle the BirthdayCard "created" event.
     */
    public function created(BirthdayCard $birthdayCard): void
    {
        //
    }

    /**
     * Handle the BirthdayCard "updated" event.
     */
    public function updated(BirthdayCard $birthdayCard): void
    {
        //
    }

    /**
     * Handle the BirthdayCard "deleted" event.
     */
    public function deleted(BirthdayCard $birthdayCard): void
    {
        $birthdayCard->profiles && $birthdayCard->profiles()->update(['birthday_card_id'=>null]);
    }

    /**
     * Handle the BirthdayCard "restored" event.
     */
    public function restored(BirthdayCard $birthdayCard): void
    {
        //
    }

    /**
     * Handle the BirthdayCard "force deleted" event.
     */
    public function forceDeleted(BirthdayCard $birthdayCard): void
    {
        //
    }
}
