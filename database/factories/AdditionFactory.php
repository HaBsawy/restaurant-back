<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Addition>
 */
class AdditionFactory extends Factory
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
            'category_id' => $categoryIds ?
                $this->faker->randomElement($categoryIds) : Category::factory()->create(),
            'name_en'   => $this->faker->word(),
            'name_ar'   => $this->faker->word(),
            'price'     => $this->faker->randomNumber(6),
            'discount'  => $this->faker->randomElement([0,5,10,15,20]),
            'active'    => $this->faker->boolean,
        ];
    }
}
