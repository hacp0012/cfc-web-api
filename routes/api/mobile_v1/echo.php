<?php

use App\Http\Middleware\SanctumCustomMiddleware;
use App\mobile_v1\app\echo\EchoEditHandler;
use App\mobile_v1\app\echo\EchoPostHandler;
use App\Quest\Quest;
use Illuminate\Support\Facades\Route;

Route::prefix('echo')->group(function () {
  Quest::spawn(routes: [
    EchoPostHandler::class,
    EchoEditHandler::class,
  ])->middleware(SanctumCustomMiddleware::class);
});
