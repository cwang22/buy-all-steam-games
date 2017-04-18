<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class SteamController extends Controller
{
    public function index() {
        $prices = json_decode(Storage::get('price.json'));
        $prices->original /= 100;
        $prices->sale /= 100;
        $prices->updated_at = Carbon::createFromTimestamp($prices->updated_at)->diffForHumans();
        return view('home',['prices' => $prices]);
    }

    public function json() {
        $prices = json_decode(Storage::get('price.json'));
        $prices->original /= 100;
        $prices->sale /= 100;
        $prices->updated_at = Carbon::createFromTimestamp($prices->updated_at)->toDateTimeString();
        return response()->json($prices);
    }

}
