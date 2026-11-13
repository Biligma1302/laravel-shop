<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\ProductDto;
use App\DTOs\ProductFilterDto;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    private const array PER_PAGE_OPTIONS = [10, 25, 50, 100];

    public function __construct(
        private readonly ProductSearchService $search
    ) {}

    public function getProductsByCategoryId(int $categoryId, ProductFilterDto $dto): LengthAwarePaginator
    {
        $perPage = in_array($dto->per_page, self::PER_PAGE_OPTIONS, true) ? $dto->per_page : 10;

        return Cache::remember(
            $this->catalogCacheKey("cat-{$categoryId}", $this->filterCacheParameters($dto, $perPage)),
            $this->catalogCacheTtl(),
            function () use ($categoryId, $dto, $perPage): LengthAwarePaginator {
                if ($dto->q !== null && trim($dto->q) !== '') {
                    return $this->search->search($dto, $perPage, $categoryId);
                }

                $query = Product::query()
                    ->where('category_id', $categoryId)
                    ->with('category');

                return $this->applyFiltersAndPaginate($query, $dto, $perPage);
            }
        );
    }

    public function getProducts(ProductFilterDto $dto): LengthAwarePaginator
    {
        $perPage = in_array($dto->per_page, self::PER_PAGE_OPTIONS, true) ? $dto->per_page : 10;

        return Cache::remember(
            $this->catalogCacheKey('list', $this->filterCacheParameters($dto, $perPage)),
            $this->catalogCacheTtl(),
            function () use ($dto, $perPage): LengthAwarePaginator {
                if ($dto->q !== null && trim($dto->q) !== '') {
                    return $this->search->search($dto, $perPage);
                }

                $query = Product::query()
                    ->with('category');

                return $this->applyFiltersAndPaginate($query, $dto, $perPage);
            }
        );
    }

    private function applyFiltersAndPaginate($query, ProductFilterDto $dto, int $perPage): LengthAwarePaginator
    {
        if ($dto->min_price !== null) {
            $query->where('price', '>=', $dto->min_price);
        }

        if ($dto->max_price !== null) {
            $query->where('price', '<=', $dto->max_price);
        }

        if ($dto->in_stock) {
            $query->where('stock', '>', 0);
        }

        switch ($dto->sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'stock_asc':
                $query->orderBy('stock', 'asc');
                break;
            case 'stock_desc':
                $query->orderBy('stock', 'desc');
                break;
            case 'new':
            default:
                $query->orderByDesc('created_at');
                break;
        }

        $query->orderByDesc('id');

        return $query->paginate($perPage)->withQueryString();
    }

    public function invalidateCatalogCache(): void
    {
        Cache::increment('products:catalog:version');
    }

    private function catalogCacheVersion(): int
    {
        return (int) Cache::get('products:catalog:version', 2);
    }

    private function catalogCacheTtl(): int
    {
        return (int) env('CATALOG_CACHE_TTL', 600);
    }

    private function catalogCacheKey(string $scope, array $parameters): string
    {
        return sprintf(
            'products:catalog:es-v1:v%d:%s:%s',
            $this->catalogCacheVersion(),
            $scope,
            hash('sha256', json_encode($parameters, JSON_THROW_ON_ERROR)),
        );
    }

    private function filterCacheParameters(ProductFilterDto $dto, int $perPage): array
    {
        return [
            'q' => $dto->q,
            'min_price' => $dto->min_price,
            'max_price' => $dto->max_price,
            'in_stock' => $dto->in_stock,
            'sort' => $dto->sort,
            'per_page' => $perPage,
            'page' => request()->get('page', 1),
        ];
    }

    public function getProductPageData(Product $product): Product
    {
        return $product->load('category');
    }

    public function getMaxProductPrice(): int
    {
        return (int) (Product::query()->max('price') ?? 0);
    }

    public function create(ProductDto $dto): Product
    {
        $data = $dto->toProductData();

        if ($dto->image) {
            $data['image'] = $dto->image->store('products', 'public');
        }

        $product = Product::create($data);
        $this->invalidateCatalogCache();

        return $product;
    }

    public function update(Product $product, ProductDto $dto): Product
    {
        $data = $dto->toProductData();

        if ($dto->image) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $data['image'] = $dto->image->store('products', 'public');
        }

        $product->update($data);
        $this->invalidateCatalogCache();

        return $product;
    }

    public function delete(Product $product): void
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        $this->invalidateCatalogCache();
    }
}
