<?php

use App\mobile_v1\routes\OtpRouteCass;
use Illuminate\Support\Facades\Route;

Route::prefix('otp')->group(function() {
  Route::post('validate/{opt}', [OtpRouteCass::class, 'verify']);

  Route::post('send', [OtpRouteCass::class, 'sentOtp']);
});
