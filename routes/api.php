<?php

use Illuminate\Support\Facades\Route;

Route::name('api.')->prefix('v1')->group(function () {

  # API MOBILE V1 ********************************************
  require __DIR__ . '/api/mobile_v1.php';
});
