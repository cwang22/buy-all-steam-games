<?php

use App\Http\Controllers\Api\GetRecordsController;
use Illuminate\Support\Facades\Route;

Route::get('/records', GetRecordsController::class);
