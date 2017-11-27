<?php

namespace App\Console\Commands;

use App\Record;
use Exception;
use Illuminate\Console\Command;
use Log;

class Fetch extends Command
{
    /**
     * Number of apps per requests.
     *
     * @var int
     */
    protected $fetch_size;

    /**
     * Country code of region to fetch.
     *
     * @var string
     */
    protected $cc;

    /** @var string */
    protected $language;

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

    public function __construct()
    {
        parent::__construct();
        $this->fetch_size = config('steam.fetch_size') | 125;
        $this->language = config('steam.language') | 'english';
        $this->cc = config('steam.cc') | 'US';
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $original = 0.0;
            $sale = 0.0;
            // fetch appid of all games
            $contents = self::fetch('http://api.steampowered.com/ISteamApps/GetAppList/v2');
            $appids = array_column($contents['applist']['apps'], 'appid');
            $appidLists = array_chunk($appids, $this->fetch_size);

            $count = count($appids);
            $minute = ceil($count / 60 / $this->fetch_size * 2);
            $this->info("Fetching in total of $count games/DLC, it may take $minute minutes.");
            $bar = $this->output->createProgressBar(count($appidLists));

            foreach ($appidLists as $list) {
                // fetch price of games in current list
                $appid = implode(',', $list);
                $results = self::fetch($this->apiUrl($appid));

                foreach ($results as $result) {
                    if (isset($result['data']['price_overview'])) {
                        $original += $result['data']['price_overview']['initial'];
                        $sale += $result['data']['price_overview']['final'];
                    }
                }

                $bar->advance();

                //sleep 1 seconds so it does not exceed access limit
                sleep(2);
            }
            $this->store($original, $sale);
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * Fetch given url and decode json result.
     *
     * @param $url
     *
     * @return mixed
     */
    private static function fetch($url)
    {
        $content = file_get_contents($url);

        return json_decode($content, true);
    }

    /**
     * Construct API URL for given appid.
     *
     * @param $appid
     *
     * @return string
     */
    private function apiUrl($appid)
    {
        return "http://store.steampowered.com/api/appdetails/?appids=$appid&cc=$this->cc&l=$this->language&v=1&filters=price_overview";
    }

    /**
     * Store fetched prices.
     *
     * @param $original number
     * @param $sale number
     */
    private function store($original, $sale)
    {
        Record::create([
            'original' => $original / 100,
            'sale'     => $sale / 100,
            'cc'       => $this->cc,
            'language' => $this->language,
        ]);
    }
}
