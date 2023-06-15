<?php


use App\Jobs\CreateRedmineIssue;
use App\Services\Redmine\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use Tests\TestCase;

class CreateRedmineIssueTest extends TestCase
{
    use RefreshDatabase;

    public function testHandleCreatesRedmineIssueAndSendsTelegramMessage()
    {
        $redmineUrl = 'https://example.com';
        $apiKey = 'shdghjsgshg5s1d5f1s35d';
        $projectId = '130';
        $botToken = '6287236197:sdfsdfsfd';
        $chatId = '597685';
        $data = [
            'subject' => 'Test Issue',
            'description' => 'This is a test issue',
            'custom_fields' => [],
        ];

        $this->mock(Client::class, function (MockInterface $mock) use ($projectId, $apiKey, $redmineUrl) {
            $mock->shouldReceive('setRedmineUrl')->once()->with($redmineUrl);
            $mock->shouldReceive('setApiKey')->once()->with($apiKey);
            $mock->shouldReceive('getApi')->once()->with('issue')->andReturn(Mockery::mock([
                'create' => (object) [
                    'id' => 1,
                    'created_on' => '2023-06-14 11:22:34',
                ],
            ]));
            $mock->shouldReceive('getLastResponseBody')->andReturn('mocked-response');

        });

        $subject = Str::replace('[[SKIP]]', 'Не указано', (isset($data['subject']) ? $data['subject'] : null));
        $message = $subject."\n\n";
        $message .= "Номер задачи: " . 1 . "\n";
        $message .= "URL задачи: " . $redmineUrl . '/issues/1';


        $this->mock(\App\Services\Telegram\Telegram::class, function (MockInterface $mock) use ($botToken, $chatId, $redmineUrl, $message) {
           $mock->shouldReceive('setAccessToken')->once()->with($botToken);

           $mock->shouldReceive('sendMessage')->once()->with([
               'chat_id' => $chatId,
               'text' => $message,
           ])->andReturn((object)['messageId' => 123, 'date' => date('Y-m-d')]);
        });

        $logData = \App\Models\Log::factory()->create([
            'redmine_task_url' => $redmineUrl . '/issues/1',
            'telegram_message_id' => 123,
            'create_date' => date('Y-m-d'),
            'sent_date' => date('Y-m-d'),
        ]);
        $this->assertDatabaseHas('logs', $logData->toArray());

        CreateRedmineIssue::dispatch($redmineUrl, $apiKey, $projectId, $botToken, $chatId, $data);
    }
}
