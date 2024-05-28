<?php

namespace App\Observers;

use DB;
use Str;
use App\Models\CommitInline;
use App\Services\Private\CommitInlineItemService;

class CommitInlineObserver
{
    /**
     * Handle the CommitInline "created" event.
     */
    public function created(CommitInline $commitInline): void
    {
        if ($commitInline->parent_id) {
            (new CommitInlineItemService)->copyItem($commitInline);
        }else{
            DB::table($commitInline->getTable())->where('id',$commitInline->id)->update(['unique_id' => Str::uuid()]);
        }
    }

    /**
     * Handle the CommitInline "updated" event.
     */
    public function updated(CommitInline $commitInline): void
    {

    }

    public function saved(CommitInline $commitInline): void
    {

    }

    /**
     * Handle the CommitInline "deleted" event.
     */
    public function deleted(CommitInline $commitInline): void
    {
        $commitInline->items && $commitInline->items->each(fn($item) => $item->delete());
        $commitInline->approves && $commitInline->approves->each(fn($item) => $item->delete());
    }

    /**
     * Handle the CommitInline "restored" event.
     */
    public function restored(CommitInline $commitInline): void
    {
        //
    }

    /**
     * Handle the CommitInline "force deleted" event.
     */
    public function forceDeleted(CommitInline $commitInline): void
    {
        //
    }
}
