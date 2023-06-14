<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Setting>
 */
class SettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'program_title' => fake()->numberBetween(100, 10000),
            'redmine_url' => fake()->url(),
            'redmine_api_key' => fake()->numberBetween(100, 10000000),
            'redmine_project_id' => fake()->numberBetween(1, 100),
            'telegram_chat_id' => fake()->numberBetween(1, 100000),
            'telegram_bot_token' => fake()->numberBetween(100000, 1000000000),
        ];
    }
}
