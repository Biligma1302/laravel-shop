<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ProductSearchService;
use Illuminate\Console\Command;
use Throwable;

class IndexProductCommand extends Command
{
    protected $signature = 'products:index {id}';
    protected $description = 'Sync single product with Elasticsearch';

    public function handle(ProductSearchService $searchService): int
    {
        $id = (int) $this->argument('id');

        try {
            $searchService->sync($id);
            $this->info("Товар {$id} синхронизирован.");
            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error("Ошибка синхронизации: " . $e->getMessage());
            return self::FAILURE;
        }
    }
}
