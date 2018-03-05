<?php

namespace App\Console\Commands;

use App\Record;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class Fetch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fetches amount of prices of all Steam games';

    /**
     * Execute the console command.
     *
     * @param Client $client
     * @return void
     */
    public function handle(Client $client)
    {
        $json = json_decode($client->get('http://api.steampowered.com/ISteamApps/GetAppList/v2')->getBody(), true);
        $lists = collect($json['applist']['apps'])->pluck('appid');
        $chunks = $lists->chunk(config('steam.chunk_size'));

        $this->info('fetching...');
        $progressBar = $this->output->createProgressBar($chunks->count());

        $original = 0;
        $sale = 0;

        /** @var Collection $chunk */
        foreach ($chunks as $chunk) {
            $appids = $chunk->implode(',');
            $json = json_decode($client->get('http://store.steampowered.com/api/appdetails/', [
                'query' => [
                    'appids'  => $appids,
                    'cc'      => config('steam.country'),
                    'l'       => config('steam.language'),
                    'v'       => 1,
                    'filters' => 'price_overview'
                ]
            ])->getBody(), true);

            $results = collect($json);

            foreach ($results as $result) {
                if ( ! isset($result['data']['price_overview'])) {
                    continue;
                }

                $original += $result['data']['price_overview']['initial'];
                $sale += $result['data']['price_overview']['final'];
            }

            $progressBar->advance();

            sleep(2);
        }

        $this->store($original, $sale);
    }

    /**
     * Store fetched prices.
     *
     * @param $original number
     * @param $sale number
     */
    public function store($original, $sale)
    {
        Record::create([
            'original' => $original / 100,
            'sale'     => $sale / 100,
            'cc'       => config('steam.country'),
            'language' => config('steam.language'),
        ]);
    }
}
