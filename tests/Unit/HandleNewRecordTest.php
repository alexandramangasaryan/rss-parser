<?php
namespace Tests\Unit;

use App\Services\RSSService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class HandleNewRecordTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_handle_new_record(): void
    {
        $rssMock = Mockery::mock('overload:Rss');
        $rssMock->shouldReceive('where')->once()->with('guid', '123')->andReturnSelf();
        $rssMock->shouldReceive('first')->once()->andReturn(null);
        $rssMock->shouldReceive('create')->once()->with([
            'title' => 'Program Title',
            'link' => 'https://example.com',
            'pub_date' => '2023-06-08',
            'guid' => '123',
        ]);

        $settingMock = Mockery::mock('overload:Setting');
        $settingMock->shouldReceive('where')->once()->with('program_title', 'Program Title')->andReturnSelf();
        $settingMock->shouldReceive('first')->once()->andReturn(true);

        $createRedmineIssueMock = Mockery::mock('overload:CreateRedmineIssue');
        $createRedmineIssueMock->shouldReceive('dispatch')->once();

        $rssServiceMock = Mockery::mock(RSSService::class.'[handleNewRecord]', ['https://example.xml'])
            ->shouldAllowMockingProtectedMethods();
        $rssServiceMock->shouldReceive('handleNewRecord')->once()->with('Test', 'https://example.com', '2023-06-08', '123')
            ->andReturn(['message' => 'Rss successfully parsed']);

        $result = $rssServiceMock->handleNewRecord('Test', 'https://example.com', '2023-06-08', '123');

        $this->assertSame(['message' => 'Rss successfully parsed'], $result);

        // Clean up the mocks
        Mockery::close();
    }
}
