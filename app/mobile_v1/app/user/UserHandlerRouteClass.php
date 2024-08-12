<?php

namespace App\mobile_v1\app\user;

use App\mobile_v1\app\user\UserHandlerClass;
use App\mobile_v1\classes\FileHanderClass;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class UserHandlerRouteClass
{
  /**
   * Create a new class instance.
   */
  public function __construct()
  {
    //
  }

  public function updateUserInfos(Request $request): array
  {
    $user = $request->user();

    $validated = $request->validate([
      'name'        => "required|string",
      'fullname'    => "required|string",
      'brith_date'  => "required|date",
      'civility'    => "required|string|max:1",
    ]);

    $brithDate = new Carbon($validated['brith_date']);

    $state = (new UserHandlerClass(userId: $user->id))->updateInfos(
      name: $validated['name'],
      fullname: $validated['fullname'],
      brithData: $brithDate->toDateString(),
      civility: $validated['civility'],
    );

    return ['state' => $state ? 'SUCCESS' : 'FAILED'];
  }

  public function updatePhoneNumber(Request $request): array
  {
    $user = $request->user();

    $validated = $request->validate([
      'phone_code'        => "required|numeric",
      'phone_number'      => "required|string",
      'old_phone_number'  => "required|string",
    ]);

    $state = (new UserHandlerClass(userId: $user->id))->updatePhoneNumber(
      phoneCode: $validated['phone_code'],
      newPhoneNumber: $validated['phone_number'],
      oldPhoneNumber: $validated['old_phone_number'],
    );

    return ['state' => $state ? 'SUCCESS' : 'FAILED'];
  }

  public function uploadPhoto(Request $request): array
  {
    $user = $request->user();

    $userHandler = new UserHandlerClass($user->id);

    $storePhotoPid = $userHandler->updateOrStorePhoto($request);

    return [
      'state' => $storePhotoPid ? 'STORED' : 'FAILED',
      'pid' => $storePhotoPid,
    ];
  }
}
