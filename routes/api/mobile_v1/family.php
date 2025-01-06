<?php

use App\Http\Middleware\SanctumCustomMiddleware;
use App\mobile_v1\app\family\FamilyRouteCtrl;
use Hacp0012\Quest\Quest;
use Illuminate\Support\Facades\Route;

Route::middleware(SanctumCustomMiddleware::class)->prefix('family')->group(function() {
  // Request handler.
  Route::post('request/handler', [FamilyRouteCtrl::class, 'requestHandler']);

  Quest::spawn('request', FamilyRouteCtrl::class);
});
