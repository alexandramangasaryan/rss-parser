<?php

namespace Tests\Unit;

use App\Services\RSSService;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use SimplePie\SimplePie;

class RssProcessLogRealDataTest extends TestCase
{
    /** @test
     * A basic test example.
     */
    public function test_example(): void
    {
        $feedUrl = 'https://its.1c.ru/news/news/rss/1c.xml'; // Replace with the actual RSS feed URL
        $rssService = new RSSService($feedUrl);
        $feed = new SimplePie();

        // Disable SimplePie caching for the test
        $feed->enable_cache(false);

        // Load the RSS feed from the provided URL
        $feed->set_feed_url($feedUrl);
        $feed->init();

        // Retrieve the items from the feed and process them
        foreach ($feed->get_items() as $item) {
            $title = $item->get_title();
            $link = $item->get_link();
            $pubDate = $item->get_date();
            $guid = $item->get_id();

            // Log the results
            Log::info('RSS Item:', [
                'Title' => $title,
                'Link' => $link,
                'PubDate' => $pubDate,
                'GUID' => $guid,
            ]);
        }
    }
}
