<?php

use App\Http\Middleware\SanctumCustomMiddleware;
use App\mobile_v1\app\user\UserHandlerRouteClass;
use Illuminate\Support\Facades\Route;

Route::middleware(SanctumCustomMiddleware::class)->prefix("user")->group(function() {
  Route::prefix('phone')->group(function() {
    Route::post('update', [UserHandlerRouteClass::class, 'updatePhoneNumber']);
  });

  Route::post('update/infos', [UserHandlerRouteClass::class, 'updateUserInfos']);
});
