<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\ProductSearchService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncProductSearchIndexJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;
    public array $backoff = [5, 15, 30, 60];

    public function __construct(public readonly int $productId)
    {
        $this->onConnection((string) config('elasticsearch.queue_connection'));
        $this->onQueue((string) config('elasticsearch.queue'));
        $this->afterCommit();
    }

    public function handle(ProductSearchService $search): void
    {
        $search->sync($this->productId);
    }
}
