<?php

namespace App\Console\Commands;

use App\Services\RSSService;
use Illuminate\Console\Command;

class RssParse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rss:parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command parses rss feed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $feedUrl = config('app.rss_feed_url');
        $rssService = new RSSService($feedUrl);

        $rssService->processRSS();
    }
}
