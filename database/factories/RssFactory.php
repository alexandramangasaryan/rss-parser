<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rss>
 */
class RssFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->numberBetween(1, 1000),
            'link' => fake()->numberBetween(1, 1000),
            'pub_date' => fake()->date(),
            'guid' => fake()->numberBetween(1, 1000),
        ];
    }
}
