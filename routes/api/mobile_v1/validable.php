<?php

use App\Http\Middleware\SanctumCustomMiddleware;
use App\mobile_v1\routes\ValidableRouteCtrl;
use Illuminate\Support\Facades\Route;

Route::middleware(SanctumCustomMiddleware::class)->prefix('validable')->group(function () {
  Route::get('has_validable', [ValidableRouteCtrl::class, 'checkIfHas']);
  Route::get('list',          [ValidableRouteCtrl::class, 'getList']);
  Route::get('sents',         [ValidableRouteCtrl::class, 'getSents']);
  Route::post('validate',     [ValidableRouteCtrl::class, 'accept']);
  Route::post('reject',       [ValidableRouteCtrl::class, 'reject']);
});
