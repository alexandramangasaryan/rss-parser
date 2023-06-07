<?php

namespace App\Services;

use App\Jobs\CreateRedmineIssue;
use App\Models\Log;
use App\Models\Rss;
use App\Models\Setting;
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
            $link = $item->get_link();
            $pubDate = $item->get_date();
            $guid = $item->get_id();
            $this->handleNewRecord($title, $link, $pubDate, $guid);
        }
    }

    protected function handleNewRecord($title, $link, $pubDate, $guid)
    {
        $substrTitle = strstr($title, '"');
        $programTitle = substr($substrTitle, 0, strrpos ($substrTitle, '"'));

        $existingRss = Rss::where('guid', $guid)->first();
        if (!$existingRss) {
            Rss::create([
                'title' => $programTitle,
                'link' => $link,
                'pub_date' => $pubDate,
                'guid' => $guid,
            ]);

            $settingExists = Setting::where('program_title', $programTitle)->first();
            if ($settingExists) {
                $data = [];

                $data['subject'] = $title;
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

