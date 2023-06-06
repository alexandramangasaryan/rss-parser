<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Redmine\Client\NativeCurlClient;
use Tests\TestCase;
use Redmine\Client\Client;

class RedmineConnectionTest extends TestCase
{
    /** @test
     * A basic test example.
     */
    public function testRedmineConnection(): void
    {
        $redmineUrl = env('REDMINE_URL');
        $apiKey = env('REDMINE_API_KEY');

        $redmine = new NativeCurlClient($redmineUrl, 'gfxhgfxhgdhxgch');

        try {
            $currentUser = $redmine->requestGet('/users/current.json');
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->fail('Failed to connect to Redmine: ' . $e->getMessage());
        }
    }
}
