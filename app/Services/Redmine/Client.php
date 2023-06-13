<?php

namespace App\Services\Redmine;

use Redmine\Client\NativeCurlClient;

class Client
{
    private NativeCurlClient $client;
    private $url;
    private $apiKey;

    /**
     * @codeCoverageIgnore
     */
    public function __construct($url, $apiKey)
    {
        $this->client = new NativeCurlClient($url, $apiKey);
        $this->url = $url;
        $this->apiKey = $apiKey;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getApi(string $name): mixed
    {
        return $this->client->getApi($name);
    }

    /**
     * @codeCoverageIgnore
     */
    public function getLastResponseBody(): mixed
    {
        return $this->client->getLastResponseBody();
    }
}
