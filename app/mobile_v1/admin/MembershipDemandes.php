<?php

namespace App\mobile_v1\admin;

use App\Models\Pcn;
use App\Models\User;
use Hacp0012\Quest\Attributs\QuestSpaw;
use Hacp0012\Quest\QuestResponse;
use Hacp0012\Quest\SpawMethod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class MembershipDemandes
{
  /**
   * Create a new class instance.
   */
  public function __construct(protected Request $request)
  {
    $this->user = $request->user();
    $this->initRole();
  }

  private array $ficthable = [
    'id',
    'fullname',
    'telephone',
    'address',
    'pool',
    'com_loc',
    'noyau_af',
    'pcn_in_waiting_validation',
  ];

  public User $user;
  private ?string $level = null;
  private bool $isMaster = false;
  # ------------------------------------------------------------------------------:
  private function initRole(): void
  {
    $role = $this->user->role;

    if ($role['state'] == 'ACTIVE') {
      $this->level = $role['level'];

      $adminMan = new AdminMan;
      if (AdminMan::isAdmin(userId: $this->user->id)) {
        $admin = $adminMan->getOne(userId: $this->user->id);
        $this->isMaster = $admin->is_master;
      }
    }
  }

  private function getAll(): Collection
  {
    $users = User::where('pcn_in_waiting_validation', '<>', null)->get($this->ficthable);

    return $users;
  }

  private function getOnlyForLevel(): Collection
  {
    $users = User::where(
      // 'pcn_in_waiting_validation->noyau_af',
      'pcn_in_waiting_validation->' . $this->level,
      '=',
      $this->user->{$this->level},
      // $this->user->noyau_af,
    )->get($this->ficthable);

    return $users;
  }
  // -----------------------------------------------------------------------------:

  #[QuestSpaw(ref: 'q136sugDE3VbMy1ZxSC4DmDkyBANxpOH5rPz', method: SpawMethod::GET)]
  public function get(): Collection
  {
    QuestResponse::setForJson(ref: 'q136sugDE3VbMy1ZxSC4DmDkyBANxpOH5rPz', dataName: 'users');

    if ($this->isMaster) return $this->getAll();
    else return $this->getOnlyForLevel();
  }

  #[QuestSpaw(ref: 'PaplufYs0q4dGtPx16TWG4l6dviyY6HD1pHu', method: SpawMethod::POST)]
  public function validate(string $userId): bool
  {
    QuestResponse::setForJson(ref: 'PaplufYs0q4dGtPx16TWG4l6dviyY6HD1pHu', dataName: 'success');

    $user = User::find($userId);
    if ($user && $user->pcn_in_waiting_validation) {
      $demande = $user->pcn_in_waiting_validation;

      // Update.
      $user->pool = $demande['pool'];
      $user->com_loc = $demande['com_loc'];
      $user->noyau_af = $demande['noyau_af'];

      // Init.
      $user->pcn_in_waiting_validation = null;

      // Saving.
      $state = $user->save();

      return $state;
    }

    return false;
  }

  #[QuestSpaw(ref: 'ELi6ZuJLEqjknR0LTXgD12w9zY8u9kqntKy1', method: SpawMethod::POST)]
  public function reject(string $userId): bool
  {
    QuestResponse::setForJson(ref: 'ELi6ZuJLEqjknR0LTXgD12w9zY8u9kqntKy1', dataName: 'success');
    $user = User::find($userId);

    if ($user) {
      $user->pcn_in_waiting_validation = null;

      $state = $user->save();
      return $state;
    }

    return false;
  }
}
