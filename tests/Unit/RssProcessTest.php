<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use SimplePie\SimplePie;
use Tests\TestCase;

class RssProcessTest extends TestCase
{
    /**@test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_example(): void
    {
        $feedMock = Mockery::mock('overload:SimplePie');
        $feedMock->shouldReceive('set_feed_url')->once();
        $feedMock->shouldReceive('enable_cache')->once()->with(false);
        $feedMock->shouldReceive('init')->once();

        $itemMock = Mockery::mock('overload:SimplePie_Item');

        $itemMock->shouldReceive('get_title')->andReturn('1С:Управление производственным предприятием');
        $itemMock->shouldReceive('get_link')->andReturn('https://releasess.1c.ru/version_files?nick=Enterprise13&amp;ver=1.3.205.1');
        $itemMock->shouldReceive('get_date')->andReturn('31 May 2023, 1:47 pm');
        $itemMock->shouldReceive('get_id')->andReturn("/news/48446");

        $feedMock->shouldReceive('get_items')->once()->andReturn([$itemMock]);

        Mockery::close();
    }
}
