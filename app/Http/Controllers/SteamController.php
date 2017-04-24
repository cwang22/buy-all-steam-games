<?php

namespace App\Http\Controllers;

use App\Record;

class SteamController extends Controller
{
    public function index()
    {
        $prices = Record::latest()->first();

        if (!$prices) {
            return 'No price data available. Please run <code>php artisan fetch</code> to fetch data.';
        }

        return view('home', compact('prices'));
    }

    public function json()
    {
        $prices = Record::latest()->first();

        if (!$prices) {
            return response()->json([
                'success' => false,
                'message' => 'No price data available.'
            ]);
        }

        return $prices;
    }

}
