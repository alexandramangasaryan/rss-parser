<?php


use App\Jobs\CreateRedmineIssue;
use App\Services\Redmine\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use Telegram\Bot\Api;
use Tests\TestCase;

class CreateRedmineIssueTest extends TestCase
{
    public function testHandleCreatesRedmineIssueAndSendsTelegramMessage()
    {
        $redmineUrl = 'https://example.com';
        $apiKey = 'shdghjsgshg5s1d5f1s35d';
        $projectId = '130';
        $botToken = '6287236197d31f43s5d4f53';
        $chatId = '321452452';
        $data = [
            'subject' => 'Test Issue',
            'description' => 'This is a test issue',
            'custom_fields' => [],
        ];

        $this->mock(Client::class, function (MockInterface $mock) use ($data, $projectId, $apiKey, $redmineUrl) {
            $mock->shouldReceive('setRedmineUrl')->once()->with($redmineUrl);
            $mock->shouldReceive('setApiKey')->once()->with($apiKey);
//            $mock->shouldReceive('__construct')->once()->with($redmineUrl, $apiKey);
            $mock->shouldReceive('getApi')->once()->with('issue')->andReturn(Mockery::mock([
                'create' => (object) [
                    'id' => 1,
                    'created_on' => '2023-06-14 11:22:34',
                ],
            ]));
            $mock->shouldReceive('getLastResponseBody')->andReturn('mocked-response');

        });

        $telegramMock = Mockery::mock(Api::class);
        $telegramMock->shouldReceive('sendMessage')->once()->with([
            'chat_id' => $chatId,
            'text' => Mockery::on(function ($message) use ($redmineUrl) {
                return strpos($message, $redmineUrl) !== false;
            }),
        ])->andReturn((object) ['messageId' => 123, 'date' => time()]);

        $this->app->instance(Api::class, $telegramMock);


        Storage::shouldReceive('put')->once();

        CreateRedmineIssue::dispatch($redmineUrl, $apiKey, $projectId, $botToken, $chatId, $data);
    }
}
