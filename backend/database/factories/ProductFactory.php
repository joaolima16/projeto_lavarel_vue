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
        $priceCost = fake()->randomFloat(2, 10, 500); 

        return [
            'name' => ucfirst(fake()->words(3, true)), 
            'description' => fake()->paragraph(), 
            'price_cost' => $priceCost,
            'price_sale' => $priceCost * 1.5, 
        ];
    }
}
