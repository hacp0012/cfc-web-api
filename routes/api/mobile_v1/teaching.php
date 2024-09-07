<?php

use App\Http\Middleware\SanctumCustomMiddleware;
use App\Quest\Quest;
use Illuminate\Support\Facades\Route;

Route::prefix('teaching')->group(function () {
  Quest::spawn(middleware: SanctumCustomMiddleware::class);
});
