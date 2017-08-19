<?php

namespace App\Http\Controllers;

use App\Record;

class SteamController extends Controller
{
    public function index()
    {
        $prices = Record::latest()->get();

        if (!$prices) {
            return 'No price data available. Please run <code>php artisan fetch</code> to fetch data.';
        }

        return view('home', ['price' => $prices->first(), 'prices' => $prices]);
    }

    public function json()
    {
        $prices = Record::latest()->get();

        if (!$prices) {
            return response()->json([
                'success' => false,
                'message' => 'No price data available.'
            ]);
        }

        return $prices;
    }

}
