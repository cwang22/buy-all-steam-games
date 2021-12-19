<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Carbon\Carbon;
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
        return $this->getView();
    }

    /**
     * Show Chinese homepage.
     *
     * @return \Illuminate\View\View
     */
    public function zh()
    {
        app()->setLocale('zh');
        Carbon::setLocale('zh');

        return $this->getView('zh');
    }

    /**
     * Get cached view for given locale.
     *
     * @param string $lang
     *
     * @return mixed
     */
    public function getView(string $lang = 'en')
    {
        return Cache::remember("view.$lang", 1440, function () use ($lang) {
            $records = Record::latest()->where('cc', 'US')->get();

            if ($records->isEmpty()) {
                return view('empty')->render();
            }

            return view("home.$lang", ['record' => $records->first(), 'records' => $records])->render();
        });
    }

    /**
     * Show json for all records.
     */
    public function json()
    {
        return Record::latest()->where('cc', 'US')->get();
    }
}
