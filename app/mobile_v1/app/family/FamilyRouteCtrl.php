<?php

namespace App\mobile_v1\app\family;

use Illuminate\Http\Request;

class FamilyRouteCtrl
{
  /**
   * Create a new class instance.
   */
  public function __construct()
  {
    //
  }

  /** section S| function F */
  public function requestHandler(Request $request)
  {
    $section = $request->input('s');
    $function = $request->input('f');

    if ($section == 'family')
      $response = match ($function) {
        'find_couple' => $this->findLeftCouple($request),
      };

    elseif ($section == 'child')
      $response = match ($function) {
        '' => '',
      };

    return $response;
  }

  // ---------------------------------------------------------------------- :

  function findLeftCouple(Request $request)
  {
    $couples = FamilyCouple::findLeftCoupleBy(
      civility: $request->string('civility', ''),
      name: $request->string('name', ''),
      where: $request->string('where', ''),
    );

    return $couples;
  }
}
