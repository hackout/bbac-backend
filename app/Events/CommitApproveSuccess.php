<?php

namespace App\Events;

use App\Models\CommitInline;
use App\Models\CommitVehicle;
use App\Models\CommitProduct;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class CommitApproveSuccess
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public CommitVehicle|CommitInline|CommitProduct $commit)
    {
    }

}
