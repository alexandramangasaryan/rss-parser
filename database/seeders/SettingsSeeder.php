<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'program_title' => 'Зарплата и управление персоналом КОРП',
                'redmine_url' => 'https://redmine.anmarto.ru/',
                'redmine_api_key' => '6a76ec36862d2fb050fd8b1f3187ea56972cdfe6',
                'redmine_project_id' => '1',
                'telegram_chat_id' => '842936147',
                'telegram_bot_token' => '6287236197:AAGdw_CTw0jrHyWzJnx25ilPUzVojp91vjA',
            ],
            [
                'program_title' => 'Бухгалтерия некоммерческой организации КОРП',
                'redmine_url' => 'https://redmine.anmarto.ru/',
                'redmine_api_key' => '6a76ec36862d2fb050fd8b1f3187ea56972cdfe6',
                'redmine_project_id' => '1',
                'telegram_chat_id' => '842936147',
                'telegram_bot_token' => '6287236197:AAGdw_CTw0jrHyWzJnx25ilPUzVojp91vjA',
            ]
        ];

        Setting::insert($settings);
    }
}
