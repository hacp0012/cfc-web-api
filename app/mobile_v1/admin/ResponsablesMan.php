<?php

namespace App\mobile_v1\admin;

use App\Models\User;
use Hacp0012\Quest\Attributs\QuestSpaw;
use Hacp0012\Quest\QuestResponse;
use Hacp0012\Quest\QuestSpawMethod;
use Illuminate\Database\Eloquent\Collection;

class ResponsablesMan
{
  /**
   * Create a new class instance.
   */
  public function __construct() {}

  # METHODS -------------------------------------------------------------------------------:
  #[QuestSpaw(ref: 'QaqgoeMhx3qp5JDiQbQPMhhZ7MWfqRootc3g', method: QuestSpawMethod::GET)]
  public function getActives(): Collection
  {
    /** @var \Illuminate\Database\Eloquent\Collection */
    $users = User::where('role->state', 'ACTIVE')
      ->whereNot('role->role', 'STANDARD_USER')
      ->whereNot('role->role', null)
      ->get(['role', 'id', 'fullname', 'pool', 'com_loc', 'noyau_af', 'telephone']);

    QuestResponse::setForJson(ref: 'QaqgoeMhx3qp5JDiQbQPMhhZ7MWfqRootc3g', dataName: 'users');

    return $users;
  }

  #[QuestSpaw(ref: 'rLDfwh9HheHbDBZoQrfb4lmxsSUT5dkn5WFC', method: QuestSpawMethod::GET)]
  public function getIniactives(): Collection
  {
    /** @var \Illuminate\Database\Eloquent\Collection */
    $users = User::where('role->state', 'INVALIDATE')->get(['role', 'id', 'fullname', 'pool', 'com_loc', 'noyau_af', 'telephone']);

    QuestResponse::setForJson(ref: 'rLDfwh9HheHbDBZoQrfb4lmxsSUT5dkn5WFC', dataName: 'users');

    return $users;
  }

  #[QuestSpaw(ref: 'H58ngYc2vsZOjprve1YKtmrV0WqlLomWqSWc')]
  public function validate(string $userId): bool
  {
    QuestResponse::setForJson(ref: 'H58ngYc2vsZOjprve1YKtmrV0WqlLomWqSWc', dataName: 'success');

    $user = User::find($userId);

    if ($user) {
      $role = $user->role;
      $role['state'] = 'ACTIVE';

      $user->role = $role;

      $state = $user->save();

      return $state;
    }

    return false;
  }

  #[QuestSpaw(ref: 'RsVDBDyJVmiRMgQ1M8HwbLd1vG2wFB2SmAPu')]
  public function reject(string $userId): bool
  {
    QuestResponse::setForJson(ref: 'RsVDBDyJVmiRMgQ1M8HwbLd1vG2wFB2SmAPu', dataName: 'success');

    $user = User::find($userId);

    if ($user) {
      $role = [
        "state" => "ACTIVE",
        "name" => "Utilisateur standard",
        "level" => null,
        "role" => "STANDARD_USER",
        "can" => []
      ];

      $user->role = $role;

      $state = $user->save();

      return $state;
    }

    return false;
  }

  # MISC ----------------------------------------------------------------------------------:
  public function search(string $userName, $isActive = true): array
  {
    return [];
  }
}
