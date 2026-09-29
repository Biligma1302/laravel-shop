<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\DTOs\ProductDto;
use App\Http\Requests\ProductStoreRequest;
use App\Models\Product;
use App\Services\ProductService;

class ProductAdminController
{
    protected ProductService $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }
    public function index()
    {
        $products = Product::orderByDesc('id')->get();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(ProductStoreRequest $request)
    {
        $dto = ProductDto::fromRequest($request);

        $this->service->create($dto);

        return redirect()->route('admin.products.index');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(ProductStoreRequest $request, Product $product)
    {
        $dto = ProductDto::fromRequest($request);
        $this->service->update($product, $dto);

        return redirect()->route('admin.products.index')
            ->with('success', 'Товар успешно обновлен!');
    }


    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')
            ->with('success', 'Товар успешно удален!');
    }
}
