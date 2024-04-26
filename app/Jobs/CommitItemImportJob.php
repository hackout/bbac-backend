<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Services\Private\CommitItemService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CommitItemImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private string $md5Key, private int $type, private array $items)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $md5Key = $this->md5Key;
        $type = $this->type;
        $count = count($this->items);
        foreach ($this->items as $key => $item) {
            $item['type'] = $type;
            $item['sort_order'] = $count - $key;
            $this->items[$key] = $item;
        }
        (new CommitItemService)->importItem($md5Key, $this->items);
    }
}
