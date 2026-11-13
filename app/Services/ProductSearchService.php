<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\ProductFilterDto;
use App\Models\Product;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class ProductSearchService
{
    public function __construct(
        private Client $client
    ) {
    }

    public function mapping(): array
    {
        return [
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
                'analysis' => [
                    'normalizer' => [
                        'lowercase' => [
                            'type' => 'custom',
                            'filter' => ['lowercase'],
                        ],
                    ],
                ],
            ],
            'mappings' => [
                'dynamic' => 'strict',
                'properties' => [
                    'id' => ['type' => 'long'],
                    'name' => [
                        'type' => 'text',
                        'fields' => [
                            'sort' => ['type' => 'keyword', 'normalizer' => 'lowercase'],
                        ],
                    ],
                    'name_fragment' => ['type' => 'wildcard'],
                    'sku' => ['type' => 'keyword'],
                    'sku_fragment' => ['type' => 'wildcard'],
                    'price' => ['type' => 'scaled_float', 'scaling_factor' => 100],
                    'stock' => ['type' => 'integer'],
                    'category_id' => ['type' => 'long'],
                    'created_at' => ['type' => 'date'],
                ],
            ],
        ];
    }

    private function document(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'name_fragment' => mb_strtolower($product->name),
            'sku' => mb_strtolower($product->sku),
            'sku_fragment' => mb_strtolower($product->sku),
            'price' => (string) $product->price,
            'stock' => $product->stock,
            'category_id' => $product->category_id,
            'created_at' => $product->created_at?->toIso8601String(),
        ];
    }


    public function reindex(int $batchSize = 500): int
    {
        if ($batchSize < 1 || $batchSize > 5000) {
            throw new RuntimeException('Размер пакета должен быть от 1 до 5000.');
        }

        return Cache::lock($this->lockKey(), 3600)->block(5, function () use ($batchSize): int {
            $index = $this->alias() . '-' . Str::lower((string) Str::ulid());
            $this->client->indices()->create(['index' => $index, 'body' => $this->mapping()]);
            $count = 0;

            Product::query()->chunkById($batchSize, function ($products) use ($index, &$count): void {
                $body = [];
                foreach ($products as $product) {
                    $body[] = ['index' => ['_index' => $index, '_id' => (string) $product->id]];
                    $body[] = $this->document($product);
                }
                $response = $this->client->bulk(['body' => $body])->asArray();
                if ($response['errors']) {
                    throw new RuntimeException('Elasticsearch отклонил документы. Рабочий индекс не переключён.');
                }
                $count += $products->count();
            });

            $this->client->indices()->refresh(['index' => $index]);
            if ((int) $this->client->count(['index' => $index])->asArray()['count'] !== $count) {
                throw new RuntimeException('Количество документов в новом индексе не совпало.');
            }

            $actions = [];
            if ($this->client->indices()->existsAlias(['name' => $this->alias()])->asBool()) {
                foreach (array_keys($this->client->indices()->getAlias(['name' => $this->alias()])->asArray()) as $oldIndex) {
                    $actions[] = ['remove' => ['index' => $oldIndex, 'alias' => $this->alias()]];
                }
            }
            $actions[] = ['add' => ['index' => $index, 'alias' => $this->alias(), 'is_write_index' => true]];
            $this->client->indices()->updateAliases(['body' => ['actions' => $actions]]);
            app(ProductService::class)->invalidateCatalogCache();

            return $count;
        });
    }


    public function search(ProductFilterDto $dto, int $perPage, ?int $categoryId = null, ?int $page = null): LengthAwarePaginator
    {
        $page ??= Paginator::resolveCurrentPage();
        abort_if($page > intdiv(10000, $perPage), 422, 'Поиск поддерживает первые 10 000 результатов. Уточните запрос.');

        try {
            $response = $this->client->search([
                'index' => $this->alias(),
                'body' => $this->searchBody($dto, $perPage, $page, $categoryId),
            ])->asArray();
        } catch (Throwable $exception) {
            report($exception);
            abort(503, 'Поиск временно недоступен. Попробуйте позже.');
        }

        $ids = array_map(static fn (array $hit): int => (int) $hit['_id'], $response['hits']['hits']);
        $products = Product::query()->with('category')->whereIn('id', $ids)->get()->keyBy('id');
        $ordered = collect($ids)->map(static fn (int $id): ?Product => $products->get($id))->filter()->values();

        return new LengthAwarePaginator($ordered, (int) $response['hits']['total']['value'], $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
        ]);
    }

    private function searchBody(ProductFilterDto $dto, int $perPage, int $page, ?int $categoryId): array
    {
        $query = mb_strtolower(trim((string) $dto->q));
        $fragment = '*' . str_replace(['\\', '*', '?'], ['\\\\', '\\*', '\\?'], $query) . '*';
        $bool = [
            'should' => [
                ['term' => ['sku' => ['value' => $query, 'boost' => 10]]],
                ['wildcard' => ['sku_fragment' => ['value' => $fragment, 'boost' => 4]]],
                ['wildcard' => ['name_fragment' => ['value' => $fragment, 'boost' => 3]]],
                ['match' => ['name' => ['query' => $query, 'operator' => 'and', 'fuzziness' => 'AUTO']]],
            ],
            'minimum_should_match' => 1,
        ];

        if ($categoryId !== null) {
            $bool['filter'][] = ['term' => ['category_id' => $categoryId]];
        }
        $price = [];
        if ($dto->min_price !== null) {
            $price['gte'] = $dto->min_price;
        }
        if ($dto->max_price !== null) {
            $price['lte'] = $dto->max_price;
        }
        if ($price !== []) {
            $bool['filter'][] = ['range' => ['price' => $price]];
        }
        if ($dto->in_stock) {
            $bool['filter'][] = ['range' => ['stock' => ['gt' => 0]]];
        }

        $sort = match ($dto->sort) {
            'relevance' => [['_score' => 'desc']],
            'price_asc' => [['price' => 'asc']],
            'price_desc' => [['price' => 'desc']],
            'name_asc' => [['name.sort' => 'asc']],
            'name_desc' => [['name.sort' => 'desc']],
            'stock_asc' => [['stock' => 'asc']],
            'stock_desc' => [['stock' => 'desc']],
            default => [['created_at' => 'desc']],
        };
        $sort[] = ['id' => 'desc'];

        return [
            'query' => ['bool' => $bool],
            'sort' => $sort,
            'from' => ($page - 1) * $perPage,
            'size' => $perPage,
            'track_total_hits' => true,
            '_source' => false,
        ];
    }

    public function sync(int $productId): void
    {
        Cache::lock($this->lockKey(), 120)->block(5, function () use ($productId): void {
            $product = Product::query()->find($productId);
            if ($product !== null) {
                $this->client->index([
                    'index' => $this->alias(),
                    'id' => (string) $productId,
                    'require_alias' => true,
                    'body' => $this->document($product),
                    'refresh' => 'wait_for',
                ]);
            } else {
                try {
                    $this->client->delete([
                        'index' => $this->alias(),
                        'id' => (string) $productId,
                        'refresh' => 'wait_for',
                    ]);
                } catch (ClientResponseException $exception) {
                    $body = json_decode((string) $exception->getResponse()->getBody(), true);
                    if ($exception->getResponse()->getStatusCode() !== 404 || ($body['result'] ?? null) !== 'not_found') {
                        throw $exception;
                    }
                }
            }

            app(ProductService::class)->invalidateCatalogCache();
        });
    }

    private function alias(): string
    {
        return (string) config('elasticsearch.index');
    }

    private function lockKey(): string
    {
        return 'products:search:write-lock:' . $this->alias();
    }
}
