<?php

namespace App\Observers;

use App\Models\CommitApprove;
use App\Services\Private\CommitService;
use App\Services\Private\NoticeService;

class CommitApproveObserver
{
    /**
     * Handle the CommitApprove "created" event.
     */
    public function created(CommitApprove $commitApprove): void
    {
        (new NoticeService)->makeNoticeByCommitApprove($commitApprove);
        (new CommitService)->updateToPending($commitApprove->commit_id);
    }

    /**
     * Handle the CommitApprove "saved" event.
     */
    public function saved(CommitApprove $commitApprove): void
    {
        if ($commitApprove->getOriginal('status') == CommitApprove::STATUS_PENDING && $commitApprove->status == CommitApprove::STATUS_SUCCESS) {
            (new CommitService)->approveSuccess($commitApprove->commit);
        }
        if ($commitApprove->getOriginal('status') == CommitApprove::STATUS_PENDING && $commitApprove->status == CommitApprove::STATUS_REJECT) {
            (new CommitService)->approveReject($commitApprove->commit);
        }
    }

    /**
     * Handle the CommitApprove "deleted" event.
     */
    public function deleted(CommitApprove $commitApprove): void
    {
        $commitApprove->notice && $commitApprove->notice->delete();
    }

    /**
     * Handle the CommitApprove "restored" event.
     */
    public function restored(CommitApprove $commitApprove): void
    {
        //
    }

    /**
     * Handle the CommitApprove "force deleted" event.
     */
    public function forceDeleted(CommitApprove $commitApprove): void
    {
        //
    }
}
