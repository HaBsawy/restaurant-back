<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Remove>
 */
class RemoveFactory extends Factory
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
            'active'    => $this->faker->boolean,
        ];
    }
}
