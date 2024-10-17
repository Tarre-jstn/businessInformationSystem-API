<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'business_id' => 1, // Adjust based on your data
            'on_sale' => $this->faker->randomElement(['yes', 'no']),
            'on_sale_price' => $this->faker->randomFloat(2, 1, 100),
            'featured' => $this->faker->randomElement(['true', 'false']),
            'seniorPWD_discountable' => $this->faker->randomElement(['yes', 'no']),
            'name' => $this->faker->word,
            'brand' => $this->faker->word,
            'price' => $this->faker->randomFloat(2, 1, 100),
            'category' => $this->faker->word,
            'stock' => $this->faker->numberBetween(0, 100),
            'sold' => $this->faker->numberBetween(0, 100),
            'status' => $this->faker->randomElement(['Listed', 'Unlisted', 'Out of Stock']),
            'description' => $this->faker->sentence,
            'expDate' => $this->faker->date,
            'image' => 'https://picsum.photos/640/480?random=' . $this->faker->numberBetween(1, 1000)
        ];
    }
}
