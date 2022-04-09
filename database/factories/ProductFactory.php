<?php

namespace Database\Factories;

use App\Models\Category;
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
    public function definition()
    {
        $categoryIds = Category::all()->pluck('id')->toArray();
        return [
            'category_id'       => $this->faker->randomElement($categoryIds),
            'name_en'           => $this->faker->sentence(6),
            'name_ar'           => $this->faker->sentence(6),
            'description_en'    => $this->faker->realText(2000),
            'description_ar'    => $this->faker->realText(2000),
            'price'             => $this->faker->randomNumber(6),
            'discount'          => $this->faker->randomElement([0,5,10,15,20]),
            'active'            => $this->faker->boolean,
        ];
    }
}
