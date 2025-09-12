<?php

use App\Http\Controllers\SocialBotApiController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::post('/callExtractor', [SocialBotApiController::class, 'callExtractor']);
