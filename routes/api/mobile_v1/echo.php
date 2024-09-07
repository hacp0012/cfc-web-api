<?php

use App\Http\Middleware\SanctumCustomMiddleware;
use App\Quest\Quest;
use App\Quest\QuestRouter;
use Illuminate\Support\Facades\Route;

Route::prefix('echo')->group(function () {
  Quest::spawn(middleware: SanctumCustomMiddleware::class);
});
