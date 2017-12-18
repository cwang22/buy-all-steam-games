<?php

namespace App\Http\Controllers;

use App\Record;

class SteamController extends Controller
{
    public function index()
    {
        $records = Record::latest()->get();

        if ($records->isEmpty()) {
            return view('empty');
        }

        return view('home', ['record' => $records->first(), 'records' => $records]);
    }

    public function json()
    {
        return Record::latest()->get();
    }
}
