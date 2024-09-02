<?php

use App\Http\Middleware\SanctumCustomMiddleware;
use App\mobile_v1\routes\NotificationRouteCtrl;
use Illuminate\Support\Facades\Route;

Route::prefix('notification')->group(function() {
  Route::middleware('guest')->group(function() {});

  Route::middleware(SanctumCustomMiddleware::class)->group(function() {
    Route::post('request/handler', [NotificationRouteCtrl::class, 'requestHandler']);
  });
});
