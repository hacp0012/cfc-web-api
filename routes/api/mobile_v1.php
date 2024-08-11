<?php

use Illuminate\Support\Facades\Route;

require __DIR__ . '/mobile_v1/test.php';
require __DIR__ . '/mobile_v1/photo.php';
require __DIR__ . '/mobile_v1/auth.php';
require __DIR__ . '/mobile_v1/misc.php';
require __DIR__ . '/mobile_v1/user.php';

// * -- FEATURES -- *
Route::prefix('feature')->group(function() {
  require __DIR__ . '/mobile_v1/otp.php';
});
