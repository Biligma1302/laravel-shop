<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $smartphoneCategory = Category::where('slug', 'smartphones')->first();
        $laptopCategory = Category::where('slug', 'laptops')->first();

        Product::create([
            'name' => 'iPhone 16 Pro',
            'description' => 'Флагманский смартфон Apple',
            'price' => 120000,
            'category_id' => $smartphoneCategory?->id,
        ]);

        Product::create([
            'name' => 'MacBook Pro',
            'description' => 'Мощный ноутбук для разработчиков',
            'price' => 250000,
            'category_id' => $laptopCategory?->id,
        ]);

        Product::factory()->count(8)->create();
    }
}
