<?php

use App\Http\Middleware\SanctumCustomMiddleware;
use App\mobile_v1\app\com\ComEditHandler;
use App\mobile_v1\app\com\ComPostHandler;
use App\Quest\Quest;
use Illuminate\Support\Facades\Route;

Route::prefix('com')->group(function () {
  Quest::spawn(routes: [
    ComPostHandler::class,
    ComEditHandler::class,
  ])->middleware(SanctumCustomMiddleware::class)->name('com.quest');
});
