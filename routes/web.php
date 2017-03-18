<?php

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

Route::get('/', function () {
    $prices = explode(' ',Storage::get('price.dat'));

    return view('home', ['init' => $prices[0] / 100, 'final' =>$prices[1] / 100]);
});
