<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class SteamController extends Controller
{
    public function index()
    {
        if (!Storage::exists('price.json')) {
            return 'No price data available. Please run <code>php artisan fetch</code> to fetch data.';
        }
        $prices = json_decode(Storage::get('price.json'));
        $prices->original /= 100;
        $prices->sale /= 100;
        $prices->updated_at = (new Carbon($prices->updated_at))->diffForHumans();
        return view('home', ['prices' => $prices]);
    }

    public function json()
    {
        if (!Storage::exists('price.json')) {
            return response()->json([
                'success' => false,
                'message' => 'No price data available.'
            ]);
        }
        $prices = json_decode(Storage::get('price.json'));
        $prices->original /= 100;
        $prices->sale /= 100;
        return response()->json($prices);
    }

    private function getPrice()
    {

    }

}
