<?php

use App\Http\Controllers\Classes\PhotoHandlerClass;
use App\mobile_v1\handlers\PhotoHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::name('photo.')->prefix('photo/request')->group(function () {
  /** PARAMS
   * string: {scale}          scale parcentage %
   * int:    {pid}            public_id.
   * string: defautl          default image.
   * string: mask             user this specified mask instead.
   * bool:   paid_mask        (true) add a paid mask above cache mask.
   * string  licence          (null) licence key.
   */
  Route::get('get/{scale}/{pid}', function (Request $request, int $scale, string $pid) {
    return PhotoHandler::getAsResponse(
      public_id: $pid,
      scale: $scale,
      default: $request->input('default'),
      useThisMask: $request->input('mask'),
      addUnpaidMask: $request->boolean('paid_mask', true),
      licence_key: $request->input('licence'),
    );
  })->name('get');

  /** DOWNLOAD FILE (PHOTO).
   * string:      {pid}       public_id
   * string:      {size?}     (null) the picture quality size: low | small | medium | complet
   */
  Route::get('download/{pid}/{size?}', function (Request $request, string $pid, ?string $size = null) {
    //
  })->name('download');
});
