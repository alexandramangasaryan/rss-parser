<?php

namespace Database\Seeders;

use App\Models\Rss;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RssesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rsses = [
            [
                'title' => 'Program Title',
                'link' => 'https://example.com',
                'pub_date' => '2023-06-08',
                'guid' => '123',
            ],
            [
                'title' => 'Program Titlesdfs',
                'link' => 'https://example.comm',
                'pub_date' => '2023-06-08',
                'guid' => '456',
            ]
        ];

        foreach ($rsses as $rss) {
            Rss::insert($rss);
        }
    }
}
