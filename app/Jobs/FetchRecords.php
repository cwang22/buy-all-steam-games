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

class FetchRecords implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $country;

    public function __construct(string $country)
    {
        $this->country = $country;
    }

    public function handle(Client $client)
    {
        $json = json_decode($client->get('http://api.steampowered.com/ISteamApps/GetAppList/v2')->getBody(), true);
        $lists = collect($json['applist']['apps'])->pluck('appid');
        $chunks = $lists->chunk(config('steam.chunk_size'));

        $original = 0;
        $sale = 0;

        foreach ($chunks as $chunk) {
            $appids = $chunk->implode(',');
            $json = json_decode($client->get('http://store.steampowered.com/api/appdetails/', [
                'query' => [
                    'appids' => $appids,
                    'cc' => $this->country,
                    'v' => 1,
                    'filters' => 'price_overview',
                ],
            ])->getBody(), true);

            $results = collect($json);

            foreach ($results as $result) {
                if (!isset($result['data']['price_overview'])) {
                    continue;
                }

                $original += $result['data']['price_overview']['initial'];
                $sale += $result['data']['price_overview']['final'];
            }

            // Rate limit of steam API is about 10 requests every 10 seconds
            // Sleep 2 seconds just to be safe
            sleep(2);
        }

        $this->store($original, $sale);
    }


    public function store(int $original, int $sale)
    {
        Record::create([
            'original' => $original,
            'sale' => $sale,
            'cc' => $this->country
        ]);

        Cache::pull('view');
    }
}
