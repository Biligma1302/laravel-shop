<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'name' => 'iPhone 16 Pro',
            'description' => 'Флагманский смартфон Apple',
            'price' => 120000,
        ]);

        Product::create([
            'name' => 'MacBook Pro',
            'description' => 'Мощный ноутбук для разработчиков',
            'price' => 250000,
        ]);

        Product::factory()->count(8)->create();
    }
}
