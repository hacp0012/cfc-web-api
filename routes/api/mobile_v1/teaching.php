<?php

use App\Http\Middleware\SanctumCustomMiddleware;
use App\mobile_v1\app\teaching\TeachingEditHandler;
use App\mobile_v1\app\teaching\TeachingPostHandler;
use Illuminate\Support\Facades\Route;
use Princ\Quest\Quest;

Route::prefix('teaching')->group(function () {
  Quest::spawn(routes: [
    TeachingPostHandler::class,
    TeachingEditHandler::class,
  ])->middleware(SanctumCustomMiddleware::class);
});
