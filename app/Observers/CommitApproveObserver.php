<?php

namespace App\Observers;

use App\Models\CommitApprove;
use App\Models\CommitProduct;
use App\Models\CommitInline;
use App\Models\CommitVehicle;
use App\Services\Private\CommitProductService;
use App\Services\Private\CommitInlineService;
use App\Services\Private\CommitVehicleService;
use App\Services\Private\NoticeService;

class CommitApproveObserver
{
    /**
     * Handle the CommitApprove "created" event.
     */
    public function created(CommitApprove $commitApprove): void
    {
        (new NoticeService)->makeNoticeByCommitApprove($commitApprove);
        if ($commitApprove->commit instanceof CommitProduct) {
            (new CommitProductService)->updateToPending($commitApprove->commit_id);
        }
        if ($commitApprove->commit instanceof CommitInline) {
            (new CommitInlineService)->updateToPending($commitApprove->commit_id);
        }
        if ($commitApprove->commit instanceof CommitVehicle) {
            (new CommitVehicleService)->updateToPending($commitApprove->commit_id);
        }
    }

    /**
     * Handle the CommitApprove "updated" event.
     */
    public function updated(CommitApprove $commitApprove): void
    {
        if ($commitApprove->getOriginal('status') == CommitApprove::STATUS_PENDING && $commitApprove->status == CommitApprove::STATUS_SUCCESS) {
            if ($commitApprove->commit instanceof CommitProduct) {
                (new CommitProductService)->approveSuccess($commitApprove->commit);
            }
            if ($commitApprove->commit instanceof CommitInline) {
                (new CommitInlineService)->approveSuccess($commitApprove->commit);
            }
            if ($commitApprove->commit instanceof CommitVehicle) {
                (new CommitVehicleService)->approveSuccess($commitApprove->commit);
            }
        }
        if ($commitApprove->getOriginal('status') == CommitApprove::STATUS_PENDING && $commitApprove->status == CommitApprove::STATUS_REJECT) {
            if ($commitApprove->commit instanceof CommitProduct) {
                (new CommitProductService)->approveReject($commitApprove->commit);
            }
            if ($commitApprove->commit instanceof CommitInline) {
                (new CommitInlineService)->approveReject($commitApprove->commit);
            }
            if ($commitApprove->commit instanceof CommitVehicle) {
                (new CommitVehicleService)->approveReject($commitApprove->commit);
            }
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
