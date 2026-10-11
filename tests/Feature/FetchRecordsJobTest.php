<?php

namespace Tests\Feature;

use App\Jobs\FetchRecords;
use App\Models\Record;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Sleep;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

class FetchRecordsJobTest extends TestCase
{
    use RefreshDatabase;

    private array $history = [];

    protected function setUp(): void
    {
        parent::setUp();

        Sleep::fake();
        config(['steam.api_key' => 'test-key', 'steam.chunk_size' => 2]);
    }

    private function client(array $responses): Client
    {
        $stack = HandlerStack::create(new MockHandler(array_map(
            fn ($body) => new Response(200, [], json_encode($body)),
            $responses
        )));
        $stack->push(Middleware::history($this->history));

        return new Client(['handler' => $stack]);
    }

    private function query(int $request): array
    {
        parse_str($this->history[$request]['request']->getUri()->getQuery(), $query);

        return $query;
    }

    #[Test]
    public function it_pages_through_the_app_list_and_sums_prices()
    {
        $client = $this->client([
            ['response' => ['apps' => [['appid' => 10], ['appid' => 20]], 'have_more_results' => true, 'last_appid' => 20]],
            ['response' => ['apps' => [['appid' => 30]]]],
            [
                '10' => ['success' => true, 'data' => ['price_overview' => ['initial' => 999, 'final' => 499]]],
                '20' => ['success' => true, 'data' => []],
            ],
            ['30' => ['success' => true, 'data' => ['price_overview' => ['initial' => 1000, 'final' => 1000]]]],
        ]);

        (new FetchRecords('US'))->handle($client);

        $this->assertSame('0', $this->query(0)['last_appid']);
        $this->assertSame('test-key', $this->query(0)['key']);
        $this->assertSame('true', $this->query(0)['include_dlc']);
        $this->assertSame('20', $this->query(1)['last_appid']);
        $this->assertSame('10,20', $this->query(2)['appids']);
        $this->assertSame('30', $this->query(3)['appids']);
        $this->assertSame('US', $this->query(3)['cc']);

        $record = Record::sole();
        $this->assertSame(1999, $record->original);
        $this->assertSame(1499, $record->sale);
        $this->assertSame('US', $record->cc);
    }

    #[Test]
    public function it_requires_an_api_key()
    {
        config(['steam.api_key' => null]);

        $this->expectException(RuntimeException::class);

        (new FetchRecords('US'))->handle($this->client([]));
    }
}
