<?php
namespace Tests\Unit;

use App\Jobs\CreateRedmineIssue;
use App\Models\Rss;
use App\Models\Setting;
use App\Services\RSSService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Mockery;
use Tests\TestCase;

class HandleNewRecordTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function testHandleNewRecord(): void
    {
        Bus::fake();

        $rssData = [
            'title' => 'Program Title',
            'link' => 'https://example.com',
            'pub_date' => '2023-06-08',
            'guid' => '785',
        ];
        Rss::factory()->create($rssData);

        $this->assertDatabaseMissing('rsses', ['guid' => '54141']);
        $rssCreatedData = Rss::factory()->create();
        $this->assertDatabaseHas('rsses', $rssCreatedData->toArray());

        Setting::factory()->create();
        CreateRedmineIssue::dispatch('https://test.com', 'sdhfsjj', '12', 'sdjfhsjf51435afd', '54854545', ['sjdhf' => 'sdhvf']);
        Bus::assertDispatched(CreateRedmineIssue::class);

        $rssServiceMock = Mockery::mock(RSSService::class.'[handleNewRecord]', ['https://example.xml'])
            ->shouldAllowMockingProtectedMethods();
        $rssServiceMock->shouldReceive('handleNewRecord')->once()->with('Test', 'https://example.com', '2023-06-08', '123')
            ->andReturn(['message' => 'Rss successfully parsed']);

        $result = $rssServiceMock->handleNewRecord('Test', 'https://example.com', '2023-06-08', '123');

        $this->assertSame(['message' => 'Rss successfully parsed'], $result);
    }
}
