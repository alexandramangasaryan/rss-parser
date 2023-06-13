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
        $redmineUrl = 'https://redmine.anmarto.ru';
        $apiKey = '6a76ec36862d2fb050fd8b1f3187ea56972cdfe6';
        $projectId = '130';
        $botToken = '6287236197:AAGdw_CTw0jrHyWzJnx25ilPUzVojp91vjA';
        $chatId = '-838597685';
        $data = [
            'subject' => 'Test Issue',
            'description' => 'This is a test issue',
            'custom_fields' => [],
        ];

        $this->mock(Client::class, function (MockInterface $mock) use ($data, $projectId, $apiKey, $redmineUrl) {
            $mock->shouldReceive('__construct')->once()->with($redmineUrl, $apiKey);
            $mock->shouldReceive('getApi')->once()->with('issue')->andReturn(Mockery::mock());
            $mock->shouldReceive('getApi->create')->once()->with([
                'project_id' => $projectId,
                'subject' => $data['subject'],
                'description' => $data['description'],
                'custom_fields' => $data['custom_fields'],
            ])->andReturn((object) ['id' => 1]);
        });

        $this->mock(Api::class, function (MockInterface $mock) use ($redmineUrl, $chatId, $botToken) {
            $mock->shouldReceive('__construct')->once()->with($botToken);

            // Expect the Api sendMessage method to be called with the correct parameters
            $mock->shouldReceive('sendMessage')->once()->with([
                'chat_id' => $chatId,
                'text' => Mockery::on(function ($message) use ($redmineUrl) {
                    $expectedContent = "Test Issue\n\nНомер задачи: 1\nURL задачи: {$redmineUrl}/issues/1";
                    return strpos($message, $expectedContent) !== false;
                }),
            ])->andReturn((object) ['messageId' => 123, 'date' => time()]);
        });

        Storage::shouldReceive('put')->once();

        CreateRedmineIssue::dispatch($redmineUrl, $apiKey, $projectId, $botToken, $chatId, $data);
    }
}
