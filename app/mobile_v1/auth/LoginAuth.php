<?php

namespace App\mobile_v1\auth;

use App\mobile_v1\classes\Constants;
use App\mobile_v1\classes\FileHanderClass;
use App\mobile_v1\handlers\PhotoHandler;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginAuth
{
  /**
   * Create a new class instance.
   */
  public function __construct()
  {
    //
  }

  /** @return array [state: VALIDATED|INVALIDE|UNREGISTERED] */
  public function checkState(string $phoneCode, string $phoneNumber): array
  {
    $user = User::whereJsonContains('telephone', [$phoneCode, $phoneNumber])->first();

    if ($user && $user->state == 'VALIDATED') return ['state' => 'VALIDATED'];
    elseif ($user && $user->state == 'INVALIDE') return ['state' => 'INVALIDE'];
    else return ['state' => 'UNREGISTERED'];
  }

  /**
   * @return array [toke, state: LOGED|INVALIDE|ERROR|UNREGISTRED]
   */
  public function login(string $phoneCode, string $phoneNumber, string $infos = null): ?array
  {
    $user = User::whereJsonContains('telephone', [$phoneCode, $phoneNumber])->first();

    if ($user) {
      if ($user->state == 'VALIDATED') {
        // CREATE SANCTUM TOKEN {#f9f, 1}
        $token = $user->createToken($infos);

        return [
          'state' => 'LOGED', // ! --> LOGED
          'token' => $token->plainTextToken,
        ];
      } elseif ($user->state == 'INVALIDE') {
        return ['state' => 'INVALIDE']; // ! --> INVALIDE
      } else {
        return ['state' => 'ERROR']; // ! --> ERROR
      }
    } else {
      return ['state' => 'UNREGISTRED']; // ! --> UNREGISTRED
    }
  }

  /** Get user datas for mobile loading. */
  public function getUserData(): array
  {
    $user = request()->user();

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
      'can'                     => $user->can,
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

  public function logout(?string $token = null): bool
  {
    $user = request()->user();
    $bearerToken = request()->bearerToken();
    $tokens = $user->tokens()->get();

    [$id, $tokenPart] = explode('|', $bearerToken, 2);

    foreach ($tokens as $token) {
      if (hash('sha256', $tokenPart) === $token->token) {
        $user->tokens()->where('token', $token->token)->delete();
        return true;
      }
    }
    // request()->user()->tokens()->delete();

    return false;
    // return true;
  }
}
