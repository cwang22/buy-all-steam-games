<?php

use App\Http\Controllers\SteamController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [SteamController::class, 'index']);
Route::get('/zh', [SteamController::class, 'zh']);
Route::get('/api', [SteamController::class, 'json']);
