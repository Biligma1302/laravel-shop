<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ProductSearchService;
use Illuminate\Console\Command;
use Throwable;

class ReindexProductsCommand extends Command
{
    protected $signature = 'products:reindex {--batch=500}';
    protected $description = 'Complete reindexing of products into Elasticsearch with alias switching';

    public function handle(ProductSearchService $searchService): int
    {
        $batchSize = (int) $this->option('batch');

        try {
            $count = $searchService->reindex($batchSize);
            $this->info("Проиндексировано товаров: {$count}. Поисковый alias переключён.");
            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error("Индексация не завершена: " . $e->getMessage());
            return self::FAILURE;
        }
    }
}
