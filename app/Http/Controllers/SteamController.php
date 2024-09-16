<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Illuminate\Contracts\View\View;

class SteamController extends Controller
{
    public function index(): View
    {
        return view('home', [
            'locale' => app()->getLocale(),
            'record' => Record::where(['cc' => 'US'])->latest()->first(),
        ]);
    }
}
