<?php

namespace Tests\Feature;

use App\Models\Record;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PurgeCloudflareCacheTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();
    }

    #[Test]
    public function it_purges_cloudflare_when_a_record_is_created()
    {
        config(['services.cloudflare.zone_id' => 'zone', 'services.cloudflare.api_token' => 'token']);
        Http::fake(['api.cloudflare.com/*' => Http::response(['success' => true])]);

        Record::factory()->create();

        Http::assertSent(fn (Request $request) => $request->url() === 'https://api.cloudflare.com/client/v4/zones/zone/purge_cache'
            && $request->hasHeader('Authorization', 'Bearer token')
            && $request['purge_everything'] === true
        );
    }

    #[Test]
    public function a_failed_purge_does_not_stop_the_record_being_saved()
    {
        config(['services.cloudflare.zone_id' => 'zone', 'services.cloudflare.api_token' => 'token']);
        Http::fake(['api.cloudflare.com/*' => Http::response('Internal Server Error', 500)]);

        Record::factory()->create();

        $this->assertSame(1, Record::count());
    }

    #[Test]
    public function it_skips_the_purge_when_cloudflare_is_not_configured()
    {
        Http::fake();

        Record::factory()->create();

        Http::assertNothingSent();
    }

    #[Test]
    public function the_command_purges_cloudflare()
    {
        config(['services.cloudflare.zone_id' => 'zone', 'services.cloudflare.api_token' => 'token']);
        Http::fake(['api.cloudflare.com/*' => Http::response(['success' => true])]);

        $this->artisan('cloudflare:purge')->assertSuccessful();

        Http::assertSent(fn (Request $request) => $request->url() === 'https://api.cloudflare.com/client/v4/zones/zone/purge_cache'
            && $request['purge_everything'] === true
        );
    }

    #[Test]
    public function the_command_fails_when_the_purge_fails()
    {
        config(['services.cloudflare.zone_id' => 'zone', 'services.cloudflare.api_token' => 'token']);
        Http::fake(['api.cloudflare.com/*' => Http::response('Forbidden', 403)]);

        $this->artisan('cloudflare:purge')->assertFailed();
    }

    #[Test]
    public function the_command_does_nothing_when_cloudflare_is_not_configured()
    {
        Http::fake();

        $this->artisan('cloudflare:purge')->assertSuccessful();

        Http::assertNothingSent();
    }
}
