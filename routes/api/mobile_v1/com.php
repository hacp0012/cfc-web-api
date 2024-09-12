<?php

use App\Http\Middleware\SanctumCustomMiddleware;
use App\mobile_v1\app\com\ComEditHandler;
use App\mobile_v1\app\com\ComPostHandler;
use Illuminate\Support\Facades\Route;
use Hacp0012\Quest\Quest;

Route::prefix('com')->group(function () {
  Quest::spawn(routes: [
    ComPostHandler::class,
    ComEditHandler::class,
  ])->middleware(SanctumCustomMiddleware::class)->name('com.quest');
});
