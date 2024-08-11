<?php

use App\mobile_v1\handlers\PhotoHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix("photo")->group(function () {
  Route::get('get/{scale}/{pid}/{default?}', fn (int $scale, string $pid, string $default = null) => PhotoHandler::getAsResponse(
    public_id: $pid,
    scale: $scale,
    default: $default,
  ))->name('get');

  Route::get('download/{pid}', fn (string $pid) => PhotoHandler::download(public_id: $pid))->name('download');

  Route::get('file/{pid}', fn (string $pid) => PhotoHandler::fileAsResponse(public_id: $pid))->name('file');

  // Route::post('upload', [])->name('upload');
});
