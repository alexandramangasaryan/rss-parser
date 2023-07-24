<?php

namespace App\Services;

use App\Jobs\CreateRedmineIssue;
use App\Models\Log;
use App\Models\Rss;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use SimplePie\Item;
use SimplePie\SimplePie;

class RSSService
{
    protected $feedUrl;

    public function __construct($feedUrl)
    {
        $this->feedUrl = $feedUrl;
    }

    /**
     *
     */
    public function processRSS()
    {
        $feed = new SimplePie();
        $feed->set_feed_url($this->feedUrl);
        $feed->enable_cache(false);
        $feed->init();

        foreach ($feed->get_items() as $item) {
            $title = $item->get_title();
            $link = htmlspecialchars_decode($item->get_link());
            $pubDate = $item->get_date();
            $guid = $item->get_id();
            $this->handleNewRecord($title, $link, $pubDate, $guid);
        }
    }

    protected function handleNewRecord($title, $link, $pubDate, $guid)
    {
        $substrTitle = strstr($title, '"');
        $programTitle = substr($substrTitle, 1, strrpos ($substrTitle, '"') - 1);

        $startPos = strpos($title, 'версия ') + strlen('версия ');
        $endPos = strpos($title, '"', $startPos);
        $version = substr($title, $startPos, $endPos - $startPos);

        $redmineProgramTitle = $programTitle . ' .' . $version;

        $existingRss = Rss::where('guid', $guid)->first();
        if (!$existingRss) {
            Rss::create([
                'title' => $programTitle,
                'link' => $link,
                'pub_date' => Carbon::parse($pubDate)->toDateTimeString(),
                'guid' => $guid,
            ]);

            $settingExists = Setting::where('program_title', $programTitle)->first();
            if ($settingExists) {
                $data = [];

                $data['subject'] = $redmineProgramTitle;
                $data['description'] = $redmineProgramTitle . "\n";
                $data['description'] .= $link . "\n";
                $data['description'] .= $pubDate . "\n";
                CreateRedmineIssue::dispatch(
                    $settingExists->redmine_url,
                    $settingExists->redmine_api_key,
                    $settingExists->redmine_project_id,
                    $settingExists->telegram_bot_token,
                    $settingExists->telegram_chat_id,
                    $data
                );
            }
        }

        return ['message' => 'Rss successfully parsed'];
    }

}

