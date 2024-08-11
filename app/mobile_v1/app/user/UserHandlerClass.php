<?php

namespace App\mobile_v1\app\user;

use App\mobile_v1\classes\Constants;
use App\mobile_v1\classes\FileHanderClass;
use App\Models\Couple;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Json;

class UserHandlerClass
{
  /**
   * Create a new class instance.
   */
  public function __construct(protected string $userId)
  {
    //
  }

  public function updateInfos(string $name, string $fullname, string $brithData, string $civility): bool
  {
    $data = [
      'name' => $name,
      'fullname' => $fullname,
      'civility' => $civility,
      'd_naissance' => $brithData,
    ];

    $state = User::whereId($this->userId)->update($data);

    return $state;
  }

  public function updatePhoneNumber(string $phoneCode, string $newPhoneNumber, string $oldPhoneNumber): bool
  {
    $data = ['telephone' => Json::encode([$phoneCode, $newPhoneNumber])];

    $state = User::whereId($this->userId)->whereJsonContains('telephone', [$phoneCode, $oldPhoneNumber])->update($data);

    return $state;
  }

  /** Get user datas. */
  public function getUserData(): ?array
  {
    $user = User::firstWhere('id', $this->userId);
    if ($user == null) return null;

    // Photo :
    $photo = FileHanderClass::get(
      type: FileHanderClass::TYPE['IMAGE'],
      owner: $user->id,
      ownerGroup: Constants::GROUPS_USER,
      contentGroup: 'USER_PROFILE',
    );

    // Datas :
    $data = [
      'role'                    => $user->role,
      'name'                    => $user->name,
      'fullname'                => $user->fullname,
      'civility'                => $user->civility,
      'd_naissance'             => $user->d_naissance,
      'genre'                   => $user->genre,
      'pool'                    => $user->pool,
      'com_loc'                 => $user->com_loc,
      'noyau_af'                => $user->noyau_af,
      'pcn_in_waiting_validation' => $user->pcn_in_waiting_validation,
      'telephone'                 => $user->telephone,

      'photo'                     => null,
    ];

    if ($photo->first()) $data['photo'] = $photo->first()->pid;

    return $data;
  }

  /** Initialize properties. */
  public function getUserCouple(): ?array
  {
    $user = User::whereId($this->userId)->first();

    if ($this->userId) {
      $mariageHand = 'epoue';
      $civility = $user->civility;

      if ($civility == 'S') $mariageHand = 'epouse';

      return Couple::firstWhere($mariageHand, $this->userId);
    }

    return null;
  }
}
