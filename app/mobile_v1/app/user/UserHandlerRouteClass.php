<?php

namespace App\mobile_v1\app\user;

use App\mobile_v1\admin\AdminMan;
use App\mobile_v1\app\family\FamilyChildren;
use App\mobile_v1\app\family\FamilyCouple;
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

    $brithDate = new Carbon(str_replace('/', '-',$validated['brith_date']));

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

  public function updatePcn(Request $request): array
  {
    $user = $request->user();

    $userHandler = new UserHandlerClass($user->id);

    $validated = $request->validate([
      'pool'      => "required|string",
      'com_loc'   => "required|string",
      'noyau_af'  => "required|string",
    ]);

    $updateState = $userHandler->updateOrSendPcnSubscription(
      pool: $validated['pool'],
      comLoc: $validated['com_loc'],
      noyauAf: $validated['noyau_af'],
    );

    return ['state' => $updateState ? 'UPDATED' : 'FAILED'];
  }

  public function updateRole(Request $request): array
  {
    $user = $request->user();

    $userHandler = new UserHandlerClass($user->id);

    $validated = $request->validate([
      'level'  => "required|string",
      'role'   => "required|string",
    ]);

    $updateState = $userHandler->updateRole(level: $validated['level'], role: $validated['role']);

    return ['state' => $updateState ? 'UPDATED' : 'FAILED'];
  }

  public function getChildParents(Request $request): array
  {
    $user = $request->user();

    $childHandler = new FamilyChildren(userId: $user->id);

    $parents = $childHandler->getParents();

    return $parents;
  }

  public function getSimpleUserData(Request $request)
  {
    $data = UserHandlerClass::getSimpleUserData(userId: $request->string('user_id', '---'));

    return ['state' => $data ? 'SUCCESS' : 'FAILED', 'data' => $data];
  }

  public function getUserInfosOf(Request $request)
  {
    $userId = $request->string('user_id');

    $user   = null;
    $couple = null;
    $admin  = null;

    if ($userId) {
      // Get Simple user data.
      $user = UserHandlerClass::getSimpleUserData(userId: $userId);

      // Get couple.
      $couple = (new FamilyCouple(userId: $userId ?? '---'))->getCoupleInfos();

      // Get if is an admin.
      $admin = (new AdminMan)->getOne(userId: $userId);
    }

    return ['user' => $user, 'couple' => $couple, 'admin' => $admin];
  }

  public function getChildParentCoupleViaValidable(Request $request)
  {
    $user = $request->user();

    $childHandler = new FamilyChildren(userId: $user->id);

    $infos = $childHandler->getParentsViaValidable();

    return $infos;
  }
}
