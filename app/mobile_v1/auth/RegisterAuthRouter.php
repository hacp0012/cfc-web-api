<?php

namespace App\mobile_v1\auth;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class RegisterAuthRouter
{
  /**
   * Create a new class instance.
   */
  public function __construct()
  {
    //
  }

  /** @return array [state: UNREGISTRED|REGISTRED] */
  public function checkState(Request $request): array
  {
    $reg = new RegisterAuth;
    $state = 'UNREGISTRED';

    if ($request->has('phone_code') && $request->has('phone_number')) {
      $resultState = $reg->checkIfAlreadyRegistred(
        phoneCode: $request->input('phone_code'),
        phoneNumber: $request->input('phone_number'),
      );

      $state = $resultState ? 'REGISTERED' : 'UNREGISTERED';
    }

    return ['state' => $state];
  }

  public function register(Request $request)
  {
    $validateds = $request->validate([
      'name'            => "required|string",
      'fullname'        => "required|string",
      'civility'        => "required|string",
      'd_brith'         => "required|date",
      'phone_code'      => "required|numeric",
      'phone_number'    => "required|string",
      'is_parent'       => "required|bool",
      'family_name'       => "nullable|string",
      'family_id'       => "nullable|string",
      'already_member'  => "required|boolean",
      'pool'            => "nullable|uuid",
      'cl'              => "nullable|uuid",
      'na'              => "nullable|uuid",
    ]);

    $reg = new RegisterAuth;

    $brithDate = new Carbon($validateds['d_brith']);

    $result = $reg->register(
      nom: $validateds['name'],
      nomComplet: $validateds['fullname'],
      civility: $validateds['civility'],
      dBrith: $brithDate->toDateString(),
      phoneCode: $validateds['phone_code'],
      phoneNumber: $validateds['phone_number'],
      isParent: $validateds['is_parent'],
      familyName: $validateds['family_name'],
      familyId: $validateds['family_id'],
      alreadyMember: boolval($validateds['already_member']),
      pool: $validateds['pool'],
      cl: $validateds['cl'],
      na: $validateds['na'],
    );

    return $result;
  }

  /** @return array [state: VALIDATED|FAILED] */
  public function validateAccount(Request $request)
  {
    $validateds = $request->validate([
      'phone_code' => 'required|numeric',
      'phone_number' => 'required|string',
    ]);

    $reg = new RegisterAuth;

    $state = $reg->validateAfterOtpCheck(
      phoneCode: $validateds['phone_code'],
      phoneNumber: $validateds['phone_number'],
    );

    return ['state' => $state ? 'VALIDATED' : 'FAILED'];
  }

  public function unregister(Request $request)
  {
    $reg = new RegisterAuth;
    $user = $request->user();
    dd($user);

    $state = $reg->unregister(userId: $user->id);

    return ['state' => $state];
  }
}
