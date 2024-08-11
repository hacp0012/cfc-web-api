<?php

namespace App\mobile_v1\auth;

use App\mobile_v1\handlers\OtpHandler;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Json;
use InnerOtpHandler\Notifier;

class RegisterAuth
{
  /**
   * Create a new class instance.
   */
  public function __construct()
  {
    //
  }

  public function checkIfAlreadyRegistred(string $phoneCode, string $phoneNumber): bool
  {
    $user = User::whereJsonContains('telephone', [$phoneCode, $phoneNumber])->get()->first();

    if ($user) return true;
    else return false;
  }

  /** Validate account afret otp checking. */
  public function validateAfterOtpCheck(string $phoneCode, string $phoneNumber): array
  {
    $user = User::whereJsonContains('telephone', [$phoneCode, $phoneNumber])->first();

    if ($user) {
      $user->state = 'VALIDATED';
      $state = $user->save();

      return ['state' => $state ? 'SUCCESS' : 'FAILED'];
    }

    return ['state' => 'FAILED'];
  }

  // TODO: implement couple support.
  /** @return array [otp_expire_at, user_id, state:ERROR|SUCCESS] */
  public function register(
    string $nom,
    string $nomComplet,
    string $civility,
    string $dBrith,
    string $phoneCode,
    string $phoneNumber,
    bool $isParent = true,
    ?string $familyName,
    ?string $familyId,
    bool $alreadyMember = false,
    ?string $pool = null,
    ?string $cl = null,
    ?string $na = null,
  ): array {
    if ($this->checkIfAlreadyRegistred(phoneCode: $phoneCode, phoneNumber: $phoneNumber))
      return ['state' => 'ERROR'];

    $data = [
      'name' => $nom,
      'fullname' => $nomComplet,
      'civility' => $civility,
      'd_naissance' => $dBrith,
      'telephone' => [$phoneCode, $phoneNumber],
    ];

    if ($alreadyMember) $data['pcn_in_waiting_validation'] = Json::encode([
      'pool' => $pool,
      'cl' => $cl,
      'na' => $na,
    ]);

    // REGISTER : -------------------------------------------- >
    $newUser = User::create($data);

    // SEND OTP : -------------------------------------------- >
    $expireAt = time() + (60 * 3); // expire in 3Munites.
    $otpHandler = new OtpHandler;
    // SMS OTP MESSAGE :
    $otpMessageText = "La Communauté Famille Chrétienne vous souhaite le bienvenue. Voici votre code: CFC-[OTP-CODE]";

    $generateOtp = $otpHandler->generate();
    $generateOtp->store(for: $phoneCode . $phoneNumber, expirAt: $expireAt);

    $otpHandler->notify->message(text: $otpMessageText, mask: '[OTP-CODE]', maskReplacer: $generateOtp->otp)->via(
      via: Notifier::SEND_VIA_SMS,
      at: $phoneCode . $phoneNumber,
    );

    return ['state' => 'SUCCESS', 'otp_expire_at' => $expireAt, '_otp'=> $generateOtp->otp, 'user_id' => $newUser->id];
  }

  /** Unregister a user.
   * @return bool
   */
  public function unregister(string $userId): bool
  {
    $user = User::firstWhere('id', $userId);

    $state = $user->delete();

    return $state ? true : false;
  }
}
