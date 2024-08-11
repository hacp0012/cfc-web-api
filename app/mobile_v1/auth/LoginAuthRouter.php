<?php

namespace App\mobile_v1\auth;

use Illuminate\Http\Request;

class LoginAuthRouter
{
  /**
   * Create a new class instance.
   */
  public function __construct()
  {
    //
  }

  public function checkState(Request $request)
  {
    $phoneCode = $request->string('phone_code');
    $phoneNumber = $request->string('phone_number');

    if ($phoneCode && $phoneNumber) {
      $log = new LoginAuth;

      $state = $log->checkState(
        phoneCode: $phoneCode,
        phoneNumber: $phoneNumber,
      );

      return $state;
    }

    return ['state'=> 'ERROR'];
  }

  public function login(Request $request)
  {
    $validateds = $request->validate([
      'phone_code'    => "required|numeric",
      'phone_number'  => "required|string",
      'infos'         => "required|string",
    ]);

    $log = new LoginAuth;

    $state = $log->login(
      phoneCode: $validateds['phone_code'],
      phoneNumber: $validateds['phone_number'],
      infos: $validateds['infos'],
    );

    return $state;
  }

  public function getUserDatas(Request $request)
  {
    $log = new LoginAuth;

    $data = $log->getUserData();

    return $data;
  }

  /** @return array [state: LOGED_OUT|FAILED] */
  public function logout(Request $request)
  {
    $log = new LoginAuth;

    $state = $log->logout();

    return ['state' => $state ? "LOGED_OUT" : "FAILED"];
  }
}
