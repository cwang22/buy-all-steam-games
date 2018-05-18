<?php

namespace App\Http\Controllers;

use App\Record;
use Illuminate\Support\Facades\Cache;

class SteamController extends Controller
{
    /**
     * Show the homepage.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $view = Cache::remember('view', 1440, function () {
            $records = Record::latest()->get();

            if ($records->isEmpty()) {
                return view('empty')->render();
            }

            return view('home', ['record' => $records->first(), 'records' => $records])->render();
        });

        return $view;
    }

    /**
     * Show records json.
     */
    public function json()
    {
        return Record::latest()->get();
    }
}
