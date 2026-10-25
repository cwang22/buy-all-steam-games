<?php

namespace App\Http\Controllers;

use App\Models\Record;
use App\Support\Sale;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class SteamController extends Controller
{
    public function index(): View
    {
        $latestId = Record::where(['cc' => 'US'])->max('id');

        if ($latestId === null) {
            return view('empty', ['locale' => app()->getLocale()]);
        }

        // Keyed on the latest id, like the records API, so a new fetch shows up straight away.
        $stats = Cache::remember("home.{$latestId}", now()->addDay(), function () {
            $records = Record::where(['cc' => 'US'])->orderBy('created_at')->get();

            return [
                'record' => $records->last(),
                'first' => $records->first(),
                'sales' => Sale::detect($records),
            ];
        });

        return view('home', ['locale' => app()->getLocale(), ...$stats]);
    }
}
