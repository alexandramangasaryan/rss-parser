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
    public function __construct()
    {
    }

    /**
     * @codeCoverageIgnore
     */
    public function getApi(string $name): mixed
    {
        $this->client = new NativeCurlClient($this->url, $this->apiKey);

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
    }

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }
}
