<?php

use App\Http\Middleware\SanctumCustomMiddleware;
use App\mobile_v1\auth\LoginAuthRouter;
use App\mobile_v1\auth\RegisterAuthRouter;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
  Route::prefix('register')->group(function () {
    // check if is rergisted before.
    Route::post('checkstate', [RegisterAuthRouter::class, 'checkState']);
    // validate.
    Route::post('validate', [RegisterAuthRouter::class, 'validateAccount']);
    // register.
    Route::post('', [RegisterAuthRouter::class, 'register']);
  });

  Route::middleware(SanctumCustomMiddleware::class)->prefix('register')->group(function () {
    // unregister.
    Route::delete('unregister', [RegisterAuthRouter::class, 'unregister']);
  });

  # LOGIN ------------------------------------------------------------------------------------:>
  Route::prefix('login')->group(function () {
    // check registration state.
    Route::post('checkstate', [LoginAuthRouter::class, 'checkState']);
    // login.
    Route::post('', [LoginAuthRouter::class, 'login']);
  });

  Route::middleware(SanctumCustomMiddleware::class)->prefix('login')->group(function () {
    // get user data.
    Route::post('userdata', [LoginAuthRouter::class, 'getUserDatas']);
    // logout.
    Route::delete('logout', [LoginAuthRouter::class, 'logout']);
  });
});
