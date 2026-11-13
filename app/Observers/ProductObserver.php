<?php

declare(strict_types=1);

namespace App\Observers;

use App\Jobs\SyncProductSearchIndexJob;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class ProductObserver implements ShouldHandleEventsAfterCommit
{
    public function saved(Product $product): void
    {
        $this->synchronize($product);
    }

    public function deleted(Product $product): void
    {
        $this->synchronize($product);
    }

    private function synchronize(Product $product): void
    {
        app(ProductService::class)->invalidateCatalogCache();
        SyncProductSearchIndexJob::dispatch($product->id);
    }
}
