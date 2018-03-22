<?php

namespace App\Http\Controllers;

use App\Record;

class SteamController extends Controller
{
    /**
     * Show the homepage.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $records = Record::latest()->get();

        if ($records->isEmpty()) {
            return view('empty');
        }

        return view('home', ['record' => $records->first(), 'records' => $records]);
    }

    /**
     * Show records json.
     */
    public function json()
    {
        return Record::latest()->get();
    }
}
