<?php

use App\mobile_v1\routes\Loader;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->prefix('test')->group(function() {
  Route::get('', function () {
    return ["Aliquip amet exercitation incididunt incididunt adipisicing et mollit Lorem esse consectetur."];
  })->name('test');
  Route::get('data', [Loader::class, 'load']);
});
