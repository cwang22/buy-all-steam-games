<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Illuminate\Contracts\View\View;

class SteamController extends Controller
{
    public function index(): View
    {
        $record = Record::where(['cc' => 'US'])->latest()->first();

        if ($record === null) {
            return view('empty', ['locale' => app()->getLocale()]);
        }

        return view('home', [
            'locale' => app()->getLocale(),
            'record' => $record,
        ]);
    }
}
