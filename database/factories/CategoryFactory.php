<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str; // ОБЯЗАТЕЛЬНО для работы Str::slug

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Генерируем случайное слово для названия категории
        $name = fake()->unique()->word();

        return [
            'name' => ucfirst($name), // Делаем первую букву заглавной
            'slug' => Str::slug($name), // Автоматически делаем безопасный URL из имени
        ];
    }
}
