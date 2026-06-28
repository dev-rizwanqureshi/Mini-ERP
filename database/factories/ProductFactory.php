<?php

namespace Database\Factories;

use App\Enums\ProductStatus;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $price = fake()->randomFloat(2, 20, 1500);

        return [
            'name' => fake()->words(3, true),
            'sku' => strtoupper(fake()->unique()->bothify('SKU-####??')),
            'description' => fake()->sentence(),
            'unit_price' => $price,
            'cost_price' => round($price * fake()->randomFloat(2, 0.45, 0.8), 2),
            'stock_quantity' => fake()->numberBetween(10, 80),
            'low_stock_threshold' => fake()->numberBetween(5, 15),
            'unit' => fake()->randomElement(['pcs', 'kg', 'hr', 'box']),
            'tax_rate' => fake()->randomElement([0, 5, 10, 15]),
            'status' => fake()->randomElement(ProductStatus::cases())->value,
        ];
    }
}
