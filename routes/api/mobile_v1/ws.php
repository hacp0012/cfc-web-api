<?php

use App\mobile_v1\ws\WSocket;
use Illuminate\Support\Facades\Route;

Route::get('ws', function () {
  // create new server instance
  new WSocket;
});
