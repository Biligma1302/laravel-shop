<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\ProductFilterDto;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    private const array PER_PAGE_OPTIONS = [10, 25, 50, 100];

    public function getProductsByCategoryId(int $categoryId, ProductFilterDto $dto): LengthAwarePaginator
    {
        $query = Product::query()
            ->where('category_id', $categoryId)
            ->with('category');

        return $this->getFilteredProducts($query, $dto);
    }



    public function getProducts(ProductFilterDto $dto): LengthAwarePaginator
    {
        $query = Product::query()
            ->with('category');


        return $this->getFilteredProducts($query, $dto);
    }

    public function getProductPageData(Product $product): Product
    {
        return $product->load('category');
    }

    public function getMaxProductPrice(): int
    {
        return (int) (Product::query()->max('price') ?? 0);
    }

    private function getFilteredProducts($query, ProductFilterDto $dto): LengthAwarePaginator
    {
        if ($dto->q) {
            $q = $dto->q;

            $query->where(function ($subQuery) use ($q) {
                $subQuery
                    ->where('name', 'like', '%' . $q . '%')
                    ->orWhere('sku', 'like', '%' . $q . '%');
            });
        }

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

        $per_page = in_array($dto->per_page, self::PER_PAGE_OPTIONS, true)
            ? $dto->per_page
            : 10;

        return $query
            ->paginate($per_page)
            ->withQueryString();
    }
}
