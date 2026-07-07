<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
        ]);
        Category::factory()->create(['name' => 'Смартфоны', 'slug' => 'smartphones']);
        Category::factory()->create(['name' => 'Ноутбуки', 'slug' => 'laptops']);
        Category::factory()->count(7)->create();

        $this->call(ProductSeeder::class);
    }
}
