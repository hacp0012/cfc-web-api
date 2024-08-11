<?php

use App\mobile_v1\app\family\FamilyRouteCtrl;
use App\mobile_v1\classes\SysdataHandlerClass;
use App\mobile_v1\routes\Loader;
use Illuminate\Support\Facades\Route;

Route::name('misc.')->prefix('misc')->group(function() {
  Route::name('initial.')->middleware('guest')->prefix('initial')->group(function() {
    // Get initial base datas.
    Route::get('pcn', [Loader::class, 'load'])->name('data');

    // Get initali base misc datas.
    Route::get('misc', [SysdataHandlerClass::class, 'miscDatas'])->name('misc');
  });

  // params : string:civility, string:name.
  Route::get('find_couple', [FamilyRouteCtrl::class, 'findLeftCouple']);
});
