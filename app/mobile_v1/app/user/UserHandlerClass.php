<?php

namespace App\mobile_v1\app\user;

use App\mobile_v1\admin\AdminMan;
use App\mobile_v1\classes\Constants;
use App\mobile_v1\classes\FileHanderClass;
use App\Models\Couple;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;

class UserHandlerClass
{
  /** @var \App\Models\User|null */
  public ?User $user = null;
  /**
   * Create a new class instance.
   */
  public function __construct(protected string $userId)
  {
    $this->user = User::firstWhere('id', $userId);
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
      owner: $this->userId,
      ownerGroup: Constants::GROUPS_USER,
      contentGroup: 'PHOTO_PROFILE',
    );

    // Datas :
    $data = [
      'child_can_be_maried'     => $user->child_can_be_maried,
      'child_state'             => $user->child_state,
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

  /** Get simplifeds user datas.
   * Via userID or constructor userID.
   */
  public static function getSimpleUserData(string $userId): ?array
  {
    $user = User::firstWhere('id', $userId);
    if ($user == null) return null;

    // Photo :
    $photo = FileHanderClass::get(
      type: FileHanderClass::TYPE['IMAGE'],
      owner: $userId,
      ownerGroup: Constants::GROUPS_USER,
      contentGroup: 'PHOTO_PROFILE',
    );

    // Check admin state.
    $isAdmin = AdminMan::isAdmin(userId: $userId);

    // Datas :
    $data = [
      'id'                      => $user->id,
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
      'is_admin'                  => $isAdmin,
    ];

    if ($photo->first()) $data['photo'] = $photo->first()->pid;

    return $data;
  }

  /** Get user picture. */
  public static function getUserPicture(string $userId): ?string
  {
    // Photo :
    $photo = FileHanderClass::get(
      type: FileHanderClass::TYPE['IMAGE'],
      owner: $userId,
      ownerGroup: Constants::GROUPS_USER,
      contentGroup: 'PHOTO_PROFILE',
    )->first();

    return $photo?->pid;
  }

  /** Initialize properties. */
  public function getUserCouple(): ?Couple
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

  public function updateOrStorePhoto(Request $request): string|null
  {
    $validatedFile = FileHanderClass::validateFile(FileHanderClass::TYPE['IMAGE'], $request, Constants::IMAGE_UPLOAD_NAME);
    $newUploadedPhotoPID = null;

    // Uploading.
    if ($validatedFile) {
      $reesult = FileHanderClass::get(
        type: FileHanderClass::TYPE['IMAGE'],
        owner: $this->userId,
        ownerGroup: Constants::GROUPS_USER,
        contentGroup: 'PHOTO_PROFILE',
      );

      # Store.
      if ($reesult->isEmpty()) {
        FileHanderClass::store(
          document: $validatedFile,
          type: FileHanderClass::TYPE['IMAGE'],
          owner: $this->userId,
          ownerGroup: Constants::GROUPS_USER,
          contentGroup: 'PHOTO_PROFILE',
          public_id: $newUploadedPhotoPID,
        );

        # Update (replace).
      } else {
        $oldPhoto = $reesult->first();

        FileHanderClass::replace(
          document: $validatedFile,
          type: FileHanderClass::TYPE['IMAGE'],
          id: $oldPhoto->id,
          new_public_id: $newUploadedPhotoPID,
        );
      }
    }

    return $newUploadedPhotoPID;
  }

  public function updateOrSendPcnSubscription(string $pool, string $comLoc, string $noyauAf): bool
  {
    $newData = [
      'pool' => $pool,
      'com_loc' => $comLoc,
      'noyau_af' => $noyauAf,
      'created_at' => now(),
    ];

    if ($this->user) {
      $this->user->pcn_in_waiting_validation = $newData;
      return $this->user->save();
    }

    return false;;
  }

  public function updateRole(string $level, string $role): bool
  {
    $data = [
      'state' => 'INVALIDATE', // 'INWAIT',
      'name' => null,
      'level' => $level,
      'role' => $role,
      'can' => [/* A remplir par l'admin */]
    ];

    $this->user->role = $data;

    return $this->user->save();
  }
}
