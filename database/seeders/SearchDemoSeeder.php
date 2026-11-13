<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class SearchDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Создаем или обновляем категории со слагами из урока
        $laptops = Category::updateOrCreate(
            ['slug' => 'search-demo-laptops'],
            ['name' => 'Ноутбуки']
        );

        $phones = Category::updateOrCreate(
            ['slug' => 'search-demo-phones'],
            ['name' => 'Смартфоны']
        );

        // 2. Данные товаров (адаптированные под ваши колонки name, price, stock, category_id)
        $products = [
            [
                'name' => 'Ноутбук Lenovo ThinkPad',
                'price' => 79990.00,
                'stock' => 5,
                'category_id' => $laptops->id,
            ],
            [
                'name' => 'Ноутбук ASUS ZenBook',
                'price' => 89990.00,
                'stock' => 0,
                'category_id' => $laptops->id,
            ],
            [
                'name' => 'Ноутбук Apple MacBook Air',
                'price' => 119990.00,
                'stock' => 3,
                'category_id' => $laptops->id,
            ],
            [
                'name' => 'Смартфон Apple iPhone 15',
                'price' => 69990.00,
                'stock' => 10,
                'category_id' => $phones->id,
            ],
            [
                'name' => 'Смартфон Samsung Galaxy S24',
                'price' => 74990.00,
                'stock' => 4,
                'category_id' => $phones->id,
            ],
        ];

        // 3. Сохраняем товары, проверяя уникальность по имени (name)
        foreach ($products as $data) {
            Product::updateOrCreate(
                ['name' => $data['name']], // Условие уникальности вместо SKU
                [
                    'price' => $data['price'],
                    'stock' => $data['stock'],
                    'category_id' => $data['category_id'],
                    'description' => 'Демо-товар для тестирования поиска Elasticsearch',
                ]
            );
        }
    }
}

