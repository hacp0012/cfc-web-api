<?php

namespace App\mobile_v1\auth;

use App\mobile_v1\app\family\FamilyChildren;
use App\mobile_v1\app\family\FamilyCouple;
use App\mobile_v1\handlers\NotificationHandler;
use App\mobile_v1\handlers\OtpHandler;
use App\Models\User;
use App\Notifications\Wellcome;
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
      'child_state' => $isParent ? 'COMFIRMED' : 'UNCOMFIRMED',
      'child_can_be_maried' => $isParent == false ? 'NO' : 'YES',
      'name'        => $nom,
      'fullname'    => $nomComplet,
      'civility'    => $civility,
      'd_naissance' => $dBrith,
      'telephone'   => [$phoneCode, $phoneNumber],

      // TODO: This role, pool, ..., is juste for debug. {#f00, 4}
      'role' => ['state' => "ACTIVE", 'name' => "Chargé de communication",     'level' => 'pool', 'role' => "COMMUNICATION_MANAGER",  'can' => []],
      'pool' => '58X9z9YnOJTKeuBWr4I6KNCmjDN6FRLFAIda',
      'com_loc' => '58X9z9YnOJTKeuBWr4I6KNCmjDN6FRLFAIda',
      'noyau_af' => '58X9z9YnOJTKeuBWr4I6KNCmjDN6FRLFAIda',
    ];

    if ($alreadyMember) $data['pcn_in_waiting_validation'] = Json::encode([
      'pool' => $pool,
      'cl' => $cl,
      'na' => $na,
    ]);

    // REGISTER : -------------------------------------------- >
    $newUser = User::create($data);

    // CREATE OR SEND VALIDABLE FOR FAMILY HANDLER ----------- >
    $this->familialHandler($newUser->id, $isParent, $familyName, $familyId);

    { // Send notification to him.
      $user = User::find($newUser->id);

      if ($user) {
        NotificationHandler::send(title: $user->fullname, body: "Amen ". $user->name .", nous sommes ravis de vous compter parmi nous. Nous vous accueillons sur la plate-forme Famille Chrétienne avec tout l'amour du Christ. \n\Bienvenu parmi nous.")
          ->flash(Wellcome::class)
          ->to($user);
      }
    }

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

    return ['state' => 'SUCCESS', 'otp_expire_at' => $expireAt, '_otp' => $generateOtp->otp, 'user_id' => $newUser->id];
  }

  private function familialHandler($userId, bool $isParent, ?string $familyName, ?string $familyId): bool
  {
    if ($isParent) {
      if ($familyId) {
        // Send a validable to the selected couple.
        $family = new FamilyCouple($userId);

        $state = $family->sendInvitationToPartner($familyId);

        return $state;
      } elseif ($familyName) {
        // Create new incomplet couple.
        $coupleHandler = new FamilyCouple($userId);

        $coupleId = $coupleHandler->createNewIncompletCouple($familyName);

        if ($coupleId) return true;
        else return false;
      } else return false;
    } else {
      // Send a validable to the selected family (couple).
      $family = new FamilyChildren(userId: $userId);

      $state = $family->sendInvitationToParent($familyId);

      return $state;
    }
  }

  /** Unregister a user.
   * @return bool
   */
  public function unregister(string $userId): bool
  {
    $user = User::firstWhere('id', $userId);

    // TODO: finalize user UNREGISTER implementation.
    // FELETE USER ACCOUNT.
    $state = $user->delete();
    // DELETE SANCTUM TOKENS.
    // DELETE VIRTUAL CHILDREN.
    // DELETE TEACHINGS.
    // DELETE VALIDABLES.
    // DELETE USER DATAS.
    // DELETE REACTIONS.
    // DELETE NOTIFICATIONS.
    // DELETE FILES : PHOTOS|DOCUMENTS.
    // DELETE COUPLE.
    // DELETE COMMENTS.
    // DELETE COMMUNCATIONS.

    return $state ? true : false;
  }
}
