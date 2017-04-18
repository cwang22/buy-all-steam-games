<?php

namespace App\Console\Commands;

use App\Core\Steam;
use Illuminate\Console\Command;

class Fetch extends Command
{
    protected $fetchSize = 125;

    protected $storage = 'file';

    protected $cc = 'US';

    protected $language = 'english';

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
     * @return void
     */
    public function handle()
    {
        $original = 0.0;
        $sale = 0.0;

        // fetch appid of all games
        $contents = self::fetch("http://api.steampowered.com/ISteamApps/GetAppList/v2");
        $appids = array_column($contents["applist"]["apps"], 'appid');
        $appidLists = array_chunk($appids, $this->fetchSize);

        $bar = $this->output->createProgressBar(count($appidLists));

        foreach ($appidLists as $list) {
            // fetch price of games in current list
            $appid = implode(",", $list);
            $results = self::fetch($this->apiUrl($appid));

            foreach ($results as $result) {
                if (isset($result["data"]["price_overview"])) {
                    $original += $result["data"]["price_overview"]["initial"];
                    $sale += $result["data"]["price_overview"]["final"];
                }
            }

            $bar->advance();

            //sleep 1 seconds so it does not exceed access limit
            sleep(1);
        }
        $this->store($original, $sale);
    }

    /**
     * fetch given url and decode json result.
     * @param $url
     * @return mixed
     */
    private static function fetch($url)
    {
        $content = file_get_contents($url);
        return json_decode($content, true);
    }

    /**
     * construct API URL for given appid.
     * @param $appid
     * @return string
     */
    private function apiUrl($appid)
    {
        return "http://store.steampowered.com/api/appdetails/?appids=$appid&cc=$this->cc&l=$this->language&v=1&filters=price_overview";
    }

    /**
     * store fetched prices.
     * @param $original number
     * @param $sale number
     */
    private function store($original, $sale)
    {
        if ($this->storage == 'file') Storage::put("price.dat", "$original,$sale");
    }
}
