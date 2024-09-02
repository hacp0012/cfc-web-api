<?php

namespace App\mobile_v1\routes;

use App\mobile_v1\handlers\ValidableHandler;
use Illuminate\Http\Request;

class ValidableRouteCtrl
{
  /**
   * Create a new class instance.
   */
  public function __construct() {}

  public function checkIfHas(Request $request): array
  {
    $user = $request->user();

    $validable = new ValidableHandler;

    $result = $validable->checkIfHas(receiverId: $user->id);

    return ['count' => $result];
  }

  public function getList(Request $request): array
  {
    $user = $request->user();

    $validable = new ValidableHandler;

    $result = $validable->getAllOut(receiver: $user->id);

    return $result;
  }

  public function getSents(Request $request): array
  {
    $user = $request->user();

    $validable = new ValidableHandler;

    $result = $validable->getAllSentOf(senderId: $user->id);

    return $result;
  }

  public function accept(Request $request): array
  {
    $validable = new ValidableHandler;

    $state = $validable->validate(validableId: $request->input('validable_id', '---'));

    return ['state' => $state ? 'VALIDATED' : 'FAILED'];
  }

  public function reject(Request $request): array
  {
    $validable = new ValidableHandler;

    $state = $validable->reject(validableId: $request->input('validable_id', '---'));

    return ['state' => $state ? 'VALIDATED' : 'FAILED'];
  }
}
