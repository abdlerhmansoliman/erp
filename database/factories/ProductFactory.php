<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'price' => $this->faker->randomFloat(2, 0, 100),
            'description' => $this->faker->sentence(), 
            'quantity' => $this->faker->numberBetween(1, 100),
            'sku' => $this->faker->unique()->word(),
            'category_id' => 1,
            'purchase_price' => $this->faker->randomFloat(2, 0, 100),
            'sale_price' => $this->faker->randomFloat(2, 0, 100),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
