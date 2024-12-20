<?php

use App\Http\Middleware\SanctumCustomMiddleware;
use App\mobile_v1\routes\NotificationRouteCtrl;
use Hacp0012\Quest\Quest;
use Illuminate\Support\Facades\Route;

Route::prefix('notification')->group(function() {
  Route::middleware('guest')->group(function() {});

  Quest::spawn(routes: NotificationRouteCtrl::class)->middleware(SanctumCustomMiddleware::class);

  Route::middleware(SanctumCustomMiddleware::class)->group(function() {
    Route::post('request/handler', [NotificationRouteCtrl::class, 'requestHandler']);
  });
});
