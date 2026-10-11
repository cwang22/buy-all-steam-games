<?php

namespace Tests\Feature;

use App\Jobs\FetchRecords;
use App\Models\Record;
use App\Providers\AppServiceProvider;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

class FetchRecordsJobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sleep::fake();
        Http::preventStrayRequests();
        config(['steam.api_key' => 'test-key', 'steam.chunk_size' => 2]);
    }

    #[Test]
    public function it_pages_through_the_app_list_and_sums_prices()
    {
        Http::fake([
            'api.steampowered.com/*' => Http::sequence()
                ->push(['response' => ['apps' => [['appid' => 10], ['appid' => 20]], 'have_more_results' => true, 'last_appid' => 20]])
                ->push(['response' => ['apps' => [['appid' => 30]]]]),
            'store.steampowered.com/*' => Http::sequence()
                ->push([
                    '10' => ['success' => true, 'data' => ['price_overview' => ['initial' => 999, 'final' => 499]]],
                    '20' => ['success' => true, 'data' => []],
                ])
                ->push(['30' => ['success' => true, 'data' => ['price_overview' => ['initial' => 1000, 'final' => 1000]]]]),
        ]);

        (new FetchRecords('US'))->handle();

        $requests = Http::recorded()->map(fn ($pair) => $pair[0]);
        $query = fn (Request $request) => $request->data();

        $this->assertCount(4, $requests);
        $this->assertSame(0, $query($requests[0])['last_appid']);
        $this->assertSame('test-key', $query($requests[0])['key']);
        $this->assertSame('true', $query($requests[0])['include_dlc']);
        $this->assertSame(20, $query($requests[1])['last_appid']);
        $this->assertSame('10,20', $query($requests[2])['appids']);
        $this->assertSame('30', $query($requests[3])['appids']);
        $this->assertSame('US', $query($requests[3])['cc']);

        $record = Record::sole();
        $this->assertSame(1999, $record->original);
        $this->assertSame(1499, $record->sale);
        $this->assertSame('US', $record->cc);
    }

    #[Test]
    public function it_fails_when_steam_returns_an_error()
    {
        Http::fake(['api.steampowered.com/*' => Http::response('Forbidden', 403)]);

        $this->expectException(\Illuminate\Http\Client\RequestException::class);

        (new FetchRecords('US'))->handle();
    }

    #[Test]
    public function it_requires_an_api_key()
    {
        config(['steam.api_key' => null]);

        $this->expectException(RuntimeException::class);

        (new FetchRecords('US'))->handle();
    }

    #[Test]
    public function it_redacts_the_steam_key_for_nightwatch()
    {
        $this->assertSame(
            'https://api.steampowered.com/IStoreService/GetAppList/v1/?key=[redacted]&last_appid=0',
            AppServiceProvider::redactSteamKey('https://api.steampowered.com/IStoreService/GetAppList/v1/?key=ABC123&last_appid=0')
        );
        $this->assertSame(
            'cURL error 60 for https://x/?max_results=1&key=[redacted] failed',
            AppServiceProvider::redactSteamKey('cURL error 60 for https://x/?max_results=1&key=ABC123 failed')
        );
    }
}
