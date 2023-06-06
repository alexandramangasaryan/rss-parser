<?php

namespace App\Http\Controllers;

use App\Services\RSSService;
use Illuminate\Http\Request;

class RSSController extends Controller
{
    /**
     * @param Request $request
     */
    public function parse(Request $request) {
        $feedUrl = $request->feed_url;

        $rssService = new RSSService($feedUrl);

        $rssService->processRSS();
    }
}
