<?php

use App\Http\Middleware\SanctumCustomMiddleware;
use App\mobile_v1\app\family\FamilyRouteCtrl;
use Illuminate\Support\Facades\Route;

Route::middleware(SanctumCustomMiddleware::class)->prefix('family')->group(function() {
  // Request handler.
  Route::post('request/handler', [FamilyRouteCtrl::class, 'requestHandler']);
});
