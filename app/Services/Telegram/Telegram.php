<?php

namespace App\Services\Telegram;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Message;

class Telegram
{
    private Api $telegramBot;
    protected $accessToken;

    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function __construct()
    {
    }

    public function sendMessage(array $params)
    {
        $this->telegramBot = new Api($this->accessToken);
        $response = $this->telegramBot->post('sendMessage', $params);

        return new Message($response->getDecodedBody());
    }

    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }
}
