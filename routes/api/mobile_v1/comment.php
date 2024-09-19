<?php

use App\Http\Middleware\SanctumCustomMiddleware;
use App\mobile_v1\app\comment\CommentsHandler;
use Hacp0012\Quest\Quest;
use Illuminate\Support\Facades\Route;

Route::prefix('comment')->group(function() {
  Quest::spawn('handler', routes: [CommentsHandler::class])->middleware(SanctumCustomMiddleware::class);
});
