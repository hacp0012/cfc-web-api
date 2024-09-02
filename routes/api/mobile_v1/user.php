<?php

use App\Http\Middleware\SanctumCustomMiddleware;
use App\mobile_v1\app\user\UserHandlerRouteClass;
use Illuminate\Support\Facades\Route;

Route::middleware(SanctumCustomMiddleware::class)->prefix("user")->group(function() {
  Route::prefix('phone')->group(function() {
    Route::post('update', [UserHandlerRouteClass::class, 'updatePhoneNumber']);
  });

  // User data:
  Route::post('update/infos', [UserHandlerRouteClass::class, 'updateUserInfos']);

  // Update photo:
  Route::post('update/photo', [UserHandlerRouteClass::class, 'uploadPhoto']);

  // Update PCN or send new validation request to admin:
  Route::post('update/pcn', [UserHandlerRouteClass::class, 'updatePcn']);

  // Update ROLE or send new validation request to admin:
  Route::post('update/role', [UserHandlerRouteClass::class, 'updateRole']);

  # ----------------------------------------------------------------------------------------- #
  # CHILD :
  Route::prefix('child')->group(function() {
    Route::get('parents/via/validable', [UserHandlerRouteClass::class, 'getChildParentCoupleViaValidable']);
    Route::get('parents', [UserHandlerRouteClass::class, 'getChildParents']);
  });

  # MISC :
  Route::get('minimum/info', [UserHandlerRouteClass::class, 'getSimpleUserData']);
  Route::get('medium/info', [UserHandlerRouteClass::class, 'getUserInfosOf']);
});
