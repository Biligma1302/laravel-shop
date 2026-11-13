<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\DTOs\ProductFilterDto;
use App\Models\Product;
use App\Services\ProductSearchService;
use Illuminate\Console\Command;
use Throwable;

class SearchProductsCommand extends Command
{
    protected $signature = 'products:search
        {query : Поисковый запрос}
        {--category= : ID категории}
        {--min-price= : Минимальная цена}
        {--max-price= : Максимальная цена}
        {--in-stock : Только товары в наличии}
        {--sort=relevance : Сортировка (relevance, price_asc, price_desc, name_asc, name_desc, stock_asc, stock_desc, new)}
        {--per-page=10 : Количество на странице}
        {--page=1 : Номер страницы}';

    protected $description = 'Поиск товаров через Elasticsearch';

    public function handle(ProductSearchService $searchService): int
    {
        $queryText = (string) $this->argument('query');
        $categoryId = $this->option('category') !== null ? (int) $this->option('category') : null;
        $perPage = (int) $this->option('per-page');
        $page = (int) $this->option('page');

        try {
            $dto = ProductFilterDto::from([
                'q' => $queryText,
                'min_price' => $this->option('min-price') !== null ? (float) $this->option('min-price') : null,
                'max_price' => $this->option('max-price') !== null ? (float) $this->option('max-price') : null,
                'in_stock' => (bool) $this->option('in-stock'),
                'sort' => (string) $this->option('sort'),
                'per_page' => $perPage,
            ]);

            $paginator = $searchService->search($dto, $perPage, $categoryId, $page);

            $this->info("Найдено: {$paginator->total()}. Страница: {$paginator->currentPage()}.");

            $rows = collect($paginator->items())->map(static fn (Product $product): array => [
                $product->id,
                $product->name,
                $product->sku,
                $product->price,
                $product->stock,
            ])->all();

            $this->table(['ID', 'Название', 'Артикул', 'Цена', 'Остаток'], $rows);

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Поиск не выполнен: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
