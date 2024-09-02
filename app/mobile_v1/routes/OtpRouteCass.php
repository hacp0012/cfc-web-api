<?php

namespace App\mobile_v1\routes;

use App\mobile_v1\handlers\OtpHandler;
use Illuminate\Http\Request;

class OtpRouteCass
{
  /**
   * Create a new class instance.
   */
  public function __construct() {}

  public function verify(string $otp): array
  {
    $otpHandler = new OtpHandler;

    $state = $otpHandler->check(otp: $otp);

    return ['state' => $state ? 'VALIDE' : 'INVALIDE'];
  }

  public function sentOtp(Request $request): array
  {
    $otp = new OtpHandler;

    $validateds = $request->validate([
      'phone_code' => "required|numeric",
      'phone_number' => "required|string",
    ]);

    $state = $otp->sendOtp(phoneCode: $validateds['phone_code'], phoneNumber: $validateds['phone_number']);

    return $state;
  }
}
