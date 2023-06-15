<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Log>
 */
class LogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'redmine_task_url' => fake()->numberBetween(1, 1000),
            'telegram_message_id' => fake()->numberBetween(1, 1000),
            'create_date' => fake()->date(),
            'sent_date' => fake()->date(),
        ];
    }
}
