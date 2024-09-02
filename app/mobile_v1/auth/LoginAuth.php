<?php

namespace App\mobile_v1\auth;

use App\mobile_v1\app\user\UserHandlerClass;
use App\mobile_v1\handlers\NotificationHandler;
use App\Models\User;
use App\Notifications\Wellcome;

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

        // Send notification.
        NotificationHandler::send(title: $user->fullname, body: "Shalom " . $user->name . ", nous sommes ravis de vous revoir. \n\nBienvenue !")
          ->flash(Wellcome::class)
          ->to($user);

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
  public function getUserData(): ?array
  {
    $user = request()->user();

    $userHandler = new UserHandlerClass(userId: $user->id);

    $data = $userHandler->getUserData();

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
