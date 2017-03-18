<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GetPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:get-price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Price of all Steam games';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $contents = $this->get_contents("http://api.steampowered.com/ISteamApps/GetAppList/v2");
        $app_ids = array_column($contents["applist"]["apps"], 'appid');
        $size = 125;
        $chunks = array_chunk($app_ids,$size);
        $initial_price = 0;
        $final_price = 0;
        foreach($chunks as $chunk) {
            $id = implode(",", $chunk);
            $results = $this->get_contents("http://store.steampowered.com/api/appdetails/?appids=$id&cc=US&l=english&v=1&filters=price_overview");
            foreach($results as $result) {
                if(isset($result["data"]["price_overview"])) {
                    $initial_price += $result["data"]["price_overview"]["initial"];
                    $final_price += $result["data"]["price_overview"]["final"];
                }
            }
            sleep(1);
        }

        Storage::put("price.dat","$initial_price $final_price");
    }

    private function get_contents($url) {
        $content = file_get_contents($url);
        return json_decode($content, true);
    }
}
