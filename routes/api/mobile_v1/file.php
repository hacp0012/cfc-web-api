<?php

use App\mobile_v1\handlers\PhotoHandler;
use Illuminate\Support\Facades\Route;

Route::prefix('file')->group(function() {
  Route::get('get/{pid}', fn(string $pid) => PhotoHandler::fileAsResponse(public_id: $pid))->name('file');

  Route::get('download/{pid}', fn(string $pid) => PhotoHandler::download(public_id: $pid))->name('download');
});
