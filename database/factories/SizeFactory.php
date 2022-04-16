<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Size>
 */
class SizeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $productIds = Product::all()->pluck('id')->toArray();
        return [
            'product_id' => $productIds ?
                $this->faker->randomElement($productIds) : Product::factory()->create(),
            'name_en'   => $this->faker->word(),
            'name_ar'   => $this->faker->word(),
            'price'     => $this->faker->randomNumber(6),
            'discount'  => $this->faker->randomElement([0,5,10,15,20]),
            'active'    => $this->faker->boolean,
        ];
    }
}
