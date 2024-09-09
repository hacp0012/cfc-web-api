<?php

use App\Http\Middleware\SanctumCustomMiddleware;
use App\mobile_v1\app\teaching\TeachingEditHandler;
use App\mobile_v1\app\teaching\TeachingPostHandler;
use App\Quest\Quest;
use Illuminate\Support\Facades\Route;

Route::prefix('teaching')->group(function () {
  Quest::spawn(routes: [
    TeachingPostHandler::class,
    TeachingEditHandler::class,
  ])->middleware(SanctumCustomMiddleware::class);
});
