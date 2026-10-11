<?php

namespace App\Jobs;

use App\Models\Record;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Sleep;
use RuntimeException;

class FetchRecords implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected string $country;

    public function __construct(string $country)
    {
        $this->country = $country;
    }

    public function handle(Client $client): void
    {
        $original = 0;
        $sale = 0;
        $priced = 0;
        $startedAt = microtime(true);

        Log::info("[fetch:{$this->country}] Started");

        $chunks = array_chunk($this->fetchAppIds($client), (int) config('steam.chunk_size'));
        $total = count($chunks);

        foreach ($chunks as $i => $chunk) {
            $json = json_decode($client->get('https://store.steampowered.com/api/appdetails/', [
                'query' => [
                    'appids'  => implode(',', $chunk),
                    'cc'      => $this->country,
                    'v'       => 1,
                    'filters' => 'price_overview',
                ],
            ])->getBody(), true);

            foreach ($json ?? [] as $result) {
                if (!isset($result['data']['price_overview'])) {
                    continue;
                }

                $original += $result['data']['price_overview']['initial'];
                $sale += $result['data']['price_overview']['final'];
                $priced++;
            }

            $done = $i + 1;
            if ($done % 50 === 0 || $done === $total) {
                Log::info(sprintf(
                    '[fetch:%s] Prices %d/%d batches (%d%%), %d priced apps so far, %ds elapsed',
                    $this->country,
                    $done,
                    $total,
                    intdiv($done * 100, $total),
                    $priced,
                    microtime(true) - $startedAt
                ));
            }

            // Rate limit of steam API is about 10 requests every 10 seconds
            // Sleep 2 seconds just to be safe
            Sleep::for(2)->seconds();
        }

        Log::info(sprintf(
            '[fetch:%s] Finished in %ds: %d priced apps, original %.2f, sale %.2f',
            $this->country,
            microtime(true) - $startedAt,
            $priced,
            $original / 100,
            $sale / 100
        ));

        $this->store($original, $sale);
    }

    /**
     * Page through IStoreService/GetAppList, which replaced ISteamApps/GetAppList.
     * All item types are included to match what the old endpoint returned.
     *
     * @return int[]
     */
    protected function fetchAppIds(Client $client): array
    {
        $key = config('steam.api_key');

        if (empty($key)) {
            throw new RuntimeException('STEAM_API_KEY is not set.');
        }

        $appIds = [];
        $lastAppId = 0;
        $page = 0;

        do {
            $json = json_decode($client->get('https://api.steampowered.com/IStoreService/GetAppList/v1/', [
                'query' => [
                    'key'              => $key,
                    'max_results'      => 50000,
                    'last_appid'       => $lastAppId,
                    'include_games'    => 'true',
                    'include_dlc'      => 'true',
                    'include_software' => 'true',
                    'include_videos'   => 'true',
                    'include_hardware' => 'true',
                ],
            ])->getBody(), true)['response'] ?? [];

            foreach ($json['apps'] ?? [] as $app) {
                $appIds[] = $app['appid'];
            }

            $lastAppId = $json['last_appid'] ?? $lastAppId;

            Log::info(sprintf(
                '[fetch:%s] App list page %d: %d apps so far, last appid %d',
                $this->country,
                ++$page,
                count($appIds),
                $lastAppId
            ));
        } while (!empty($json['have_more_results']));

        return $appIds;
    }

    public function store(int $original, int $sale)
    {
        if ($original === 0 || $sale === 0) {
            Log::warning("[fetch:{$this->country}] Totals are zero, no record saved");

            return;
        }

        Record::create([
            'original' => $original,
            'sale'     => $sale,
            'cc'       => $this->country,
        ]);

        Cache::pull('view');
    }
}
