<?php

use App\Http\Controllers\Api\GetRecordsController;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Support\Facades\Route;

Route::get('/records', GetRecordsController::class)
    ->middleware(SetCacheHeaders::class.':public;max_age=300;s_maxage=86400;etag');
