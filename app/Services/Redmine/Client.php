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
        $this->url = $url;
        $this->apiKey = $apiKey;
        $this->client = new NativeCurlClient($this->url, $this->apiKey);
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

    public function setRedmineUrl(string $url): void
    {
        $this->url = $url;
        $this->client = new NativeCurlClient($this->url, $this->apiKey);
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
        $this->client = new NativeCurlClient($this->url, $this->apiKey);
    }
}
