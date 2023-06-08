<?php


use App\Jobs\CreateRedmineIssue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Redmine\Client\NativeCurlClient;
use Telegram\Bot\Api;
use Tests\TestCase;

// Create an interface for the Redmine client
interface RedmineClientInterface
{
    public function createIssue(array $issueData);
}

// Implement the interface using NativeCurlClient
class NativeCurlRedmineClient implements RedmineClientInterface
{
    protected $redmine;

    public function __construct($redmineUrl, $apiKey)
    {
        $this->redmine = new NativeCurlClient($redmineUrl, $apiKey);
    }

    public function createIssue(array $issueData)
    {
        return $this->redmine->getApi('issue')->create($issueData);
    }
}

class CreateRedmineIssueTest extends TestCase
{
    public function testHandleCreatesRedmineIssueAndSendsTelegramMessage()
    {
        // Mock the necessary dependencies
        $redmineClientMock = Mockery::mock(NativeCurlRedmineClient::class);
        $telegramApiMock = Mockery::mock(Api::class);
        $storageMock = Mockery::mock(Storage::class);
        $logMock = Mockery::mock(Log::class);

        // Set up the expectations
        $redmineUrl = 'https://redmine.example.com';
        $apiKey = 'your_redmine_api_key';
        $projectId = 123;
        $botToken = 'your_telegram_bot_token';
        $chatId = 'your_chat_id';
        $data = [
            'subject' => 'Test Issue',
            'description' => 'This is a test issue',
            'custom_fields' => [],
        ];

        // Expect the NativeCurlClient constructor to be called with the provided parameters
        $redmineClientMock->shouldReceive('__construct')->once()->with($redmineUrl, $apiKey);

        // Expect the NativeCurlClient to return the Redmine\Api\Issue mock
        $redmineClientMock->shouldReceive('getApi')->once()->with('issue')->andReturn(Mockery::mock());

        // Expect the Redmine\Api\Issue create method to be called with the correct parameters
        $redmineClientMock->shouldReceive('getApi->create')->once()->with([
            'project_id' => $projectId,
            'subject' => $data['subject'],
            'description' => $data['description'],
            'custom_fields' => $data['custom_fields'],
        ])->andReturn((object) ['id' => 1]);

        // Expect the Api constructor to be called with the provided bot token
        $telegramApiMock->shouldReceive('__construct')->once()->with($botToken);

        // Expect the Api sendMessage method to be called with the correct parameters
        $telegramApiMock->shouldReceive('sendMessage')->once()->with([
            'chat_id' => $chatId,
            'text' => Mockery::on(function ($message) use ($redmineUrl) {
                // Assert that the message contains the expected content
                $expectedContent = "Test Issue\n\nНомер задачи: 1\nURL задачи: {$redmineUrl}/issues/1";
                return strpos($message, $expectedContent) !== false;
            }),
        ])->andReturn((object) ['messageId' => 123, 'date' => time()]);

        // Expect the Storage put method to be called with the correct parameters
        $storageMock->shouldReceive('put')->once();

        // Create an instance of the job and manually inject the mocked dependencies
        $job = new CreateRedmineIssue(
            $redmineUrl,
            $apiKey,
            $projectId,
            $botToken,
            $chatId,
            $data
        );
        $job->handle($redmineClientMock, $telegramApiMock, $storageMock, $logMock);
    }
}
