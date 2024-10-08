<?php

use App\Http\Middleware\SanctumCustomMiddleware;
use App\mobile_v1\app\teaching\TeachingEditHandler;
use App\mobile_v1\app\teaching\TeachingHandlerClass;
use App\mobile_v1\app\teaching\TeachingHomeHandler;
use App\mobile_v1\app\teaching\TeachingPostHandler;
use Illuminate\Support\Facades\Route;
use Hacp0012\Quest\Quest;

Route::prefix('teaching')->group(function () {
  Quest::spawn(routes: [
    TeachingPostHandler::class,
    TeachingEditHandler::class,
    TeachingHandlerClass::class,
    TeachingHomeHandler::class,
  ])->middleware(SanctumCustomMiddleware::class);
});
