<?php

use App\Http\Controllers\SteamController;
use App\Http\Middleware\EncryptCookies;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// The homepage only changes when a fetch finishes, so Cloudflare caches it for a
// day and PurgeCloudflareCache clears it. Cloudflare won't cache a response that
// sets a cookie, so the session and CSRF middleware are skipped.
Route::get('/', [SteamController::class, 'index'])
    ->withoutMiddleware([
        EncryptCookies::class,
        AddQueuedCookiesToResponse::class,
        StartSession::class,
        ShareErrorsFromSession::class,
        VerifyCsrfToken::class,
    ])
    ->middleware(SetCacheHeaders::class.':public;max_age=300;s_maxage=86400;etag');
