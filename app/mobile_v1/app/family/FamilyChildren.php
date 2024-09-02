<?php

namespace App\mobile_v1\app\family;

use App\mobile_v1\app\user\UserHandlerClass;
use App\mobile_v1\auth\RegisterAuth;
use App\mobile_v1\handlers\ValidableHandler;
use App\Models\ChildOf;
use App\Models\Couple;
use App\Models\User;
use App\Models\Validable;
use Illuminate\Database\Eloquent\Casts\Json;
use Ramsey\Uuid\Uuid;

class FamilyChildren
{
  /** @var \Illuminate\Support\Collection */
  public $couple = null;

  /** @var \App\Models\User */
  public $user = null;

  /** Create a new class instance. */
  public function __construct(private string $userId)
  {
    $this->getUserCouple();
  }

  /** Initialize properties. */
  private function getUserCouple(): void
  {
    $this->user = User::whereId($this->userId)->first();

    if ($this->user) {
      $mariageHand = 'epoue';
      $civility = $this->user->civility;

      if ($civility == 'S') $mariageHand = 'epouse';

      $this->couple = Couple::firstWhere($mariageHand, $this->userId);
    }
  }

  # CHILDREN -------------------------------------------------------------------- :
  /** Accept a Concret child invitation via Validable */
  function acceptChild(string $childId, string $parentType = 'PARENT'): bool
  {
    $user = User::firstWhere('id', $childId);

    // Check if are not a child of an other couple.
    $isChild = ChildOf::firstWhere(['type' => 'CONCRET', 'child' => $childId]);
    if ($isChild) return false;

    // Add to family.
    if ($isChild == null) {
      $childOf = new ChildOf;

      $childOf->type        = 'CONCRET';
      // $childOf->child_state = 'COMFIRMED';
      $childOf->parent_type = $parentType;
      $childOf->couple      = $this->couple->id;
      $childOf->child       = $childId;

      $state = $childOf->save();

      // Update user profile : child_state.
      $user->child_state = 'COMFIRMED';
      $user->save();

      // Finish.
      return $state;
    }

    return false;
  }

  /** Send an invitation to a parent. */
  function sendInvitationToParent(string $coupleId): bool
  {
    // The person can't send an invitation to him self.
    if ($coupleId == $this->couple?->id) return false;

    // Check wether user are not a child of an other couple.
    $isChild = ChildOf::firstWhere(['type' => 'CONCRET', 'child' => $this->user->id]);
    if ($isChild) return false;

    // Send validable.
    if ($isChild == null) {
      $data = ['child_id' => $this->user->id, 'couple_id' => $coupleId];

      return (new ValidableHandler)->send(
        type: ValidableHandler::TYPE_CHILD_BIND,
        receiver: $coupleId,
        sender: $this->userId,
        data: $data
      );
    }

    return false;
  }

  /** Revoque an inviation.
   * ! ⚠️ This action will unregister the newly registred child !
   */
  function revoqueInvitationToParent(string $validableId): bool
  {
    // $validable = new ValidableHandler;

    // $state = $validable->reject(validableId: $validableId);

    if ($this->user->child_state == 'COMFIRMED') {
      // Dont delete a user account when it already in usage.
      // beacause it can be normal person [parent] no a young.
      return true;
    } else {
      $register = new RegisterAuth;

      $unregisterState = $register->unregister(userId: $this->user->id);
      return $unregisterState;
    }

    // return $state;
  }

  /** Get a couple children. */
  function getChildren(): array
  {
    // Get all children.
    $children = ChildOf::where('couple', $this->couple->id)->get();

    // Datas.
    $list = [];
    foreach ($children as $child) {
      if ($child->type == 'CONCRET') {
        // $userHandler = new UserHandlerClass(userId: $child->id);
        // $data = $userHandler->getUserData();
        $data = UserHandlerClass::getSimpleUserData(userId: $child->child);

        if ($data) {
          $child->data = $data;
          array_push($list, $child);
        }
      } else { # ype is VIRTUAL.
        array_push($list, $child);
      }
    }

    return $list;
  }

  /** Update a Virtual child info. */
  function updateChild(string $childId, array $data): bool
  {
    // Get child.
    $child = ChildOf::firstWhere(['type' => 'VIRTUAL', 'id' => $childId]);

    // Start Update.
    if ($child) {
      $_data = [];

      if ($data['nom']) $_data['nom'] = $data['nom'];
      if ($data['genre']) $_data['genre'] = $data['genre'];
      if ($data['d_naissance']) $_data['d_naissance'] = $data['d_naissance'];
      $_data['is_maried'] = $data['is_maried'];
      $_data['photo_pid'] = $data['photo_pid'];

      if (count($_data)) {
        $child->data = $_data;

        return $child->save();
      }
    }

    return false;
  }

  /** remove a child in couple list. */
  function removeChild(string $chilId): bool
  {
    if ($this->couple == null) return false;

    // Get All children of this couple.
    // $children = $this->getChildren();

    // Loop on them & remove.
    $removeState = ChildOf::whereId($chilId)->delete();
    // foreach ($children as $child) {
    //   if (($child->type == 'CONCRET' && $child->child == $chilId) || ($child->type == 'VIRTUAL' && $child->data['id'] == $chilId)) {
    //     return $child->delete();
    //   }
    // }

    return $removeState;
  }

  /** Add or create a new virtual child. */
  function addChild(string $nom, string $genre, string $d_naissance, bool $isMaried = false, string $photo_pid = null): bool
  {
    // Datas.
    $data = [
      'nom'           => $nom,
      'genre'         => $genre,
      'd_naissance'   => $d_naissance,
      'is_maried'     => $isMaried,
      'photo_pid'     => $photo_pid,
    ];

    // Storing.
    ChildOf::create([
      'type' => 'VIRTUAL',
      'couple' => $this->couple->id,
      'child' => null,
      'data' => $data,
    ]);

    return true;
  }

  /** Get child parent.
   * @return array [father, mother]
   */
  function getParents(): array
  {
    $data = ['father' => null, 'mother' => null];

    $child = ChildOf::firstWhere('child', $this->userId);

    if ($child) {
      $couple = Couple::firstWhere('id', $child->couple);

      if ($couple) {
        $father = $couple->epoue;
        $mother = $couple->epouse;

        // $fatherHandler = $father ? new UserHandlerClass(userId: $father) : null;
        // $motherHandler = $mother ? new UserHandlerClass(userId: $mother) : null;

        // $fatherData = $fatherHandler?->getUserData();
        // $motherData = $motherHandler?->getUserData();

        $fatherData = UserHandlerClass::getSimpleUserData(userId: $father);
        $motherData = UserHandlerClass::getSimpleUserData(userId: $mother);

        $data['father'] = $fatherData;
        $data['mother'] = $motherData;
        $data['couple'] = $couple;
      }
    }

    return $data;
  }

  /** Get child parent.
   * @return array [father, mother, couple]
   */
  function getParentsViaValidable(): array
  {
    $data = ['father' => null, 'mother' => null, 'couple' => null];

    $validable = Validable::firstWhere(['sender' => $this->userId, 'type' => ValidableHandler::TYPE_CHILD_BIND]);

    // $child = ChildOf::firstWhere('child', $this->userId);

    if ($validable) {
      $couple = Couple::firstWhere('id', $validable->receiver);

      if ($couple) {
        $father = $couple->epoue;
        $mother = $couple->epouse;

        // $fatherHandler = $father ? new UserHandlerClass(userId: $father) : null;
        // $motherHandler = $mother ? new UserHandlerClass(userId: $mother) : null;

        // $fatherData = $fatherHandler?->getUserData();
        // $motherData = $motherHandler?->getUserData();

        $fatherData = UserHandlerClass::getSimpleUserData(userId: $father);
        $motherData = UserHandlerClass::getSimpleUserData(userId: $mother);

        $data['father'] = $fatherData;
        $data['mother'] = $motherData;
        $data['couple'] = $couple;
      }
    }

    return $data;
  }

  function checkIfHasParentValidable(): bool
  {
    $validable = Validable::firstWhere(['type'=> ValidableHandler::TYPE_CHILD_BIND, 'sender'=> $this->user->id]);

    if ($validable) return true;
    return false;
  }

  function checkIfHasCanBeMariedValidable(): bool
  {
    $validable = Validable::firstWhere(['type'=> ValidableHandler::TYPE_CHILD_CAN_MARIED, 'sender'=> $this->user->id]);

    if ($validable) return true;
    return false;
  }

  function acceptChildCanBeMaried(string $childId): bool
  {
    $user = User::firstWhere('id', $childId);

    if ($user) {
      $user->child_can_be_maried = 'YES';

      return $user->save();
    }

    return false;
  }

  /** Send a Can Be Maried invitation to a parent. */
  function sendCanBeMariedInvitation(): bool
  {
    $child = ChildOf::firstWhere('child', $this->userId);

    // The person can't send an invitation to him self.
    // if ($child->couple == $this->couple?->id) return false;

    // Check wether this invitaion is unique.
    $isNotUnique = Validable::firstWhere(['type'=>ValidableHandler::TYPE_CHILD_CAN_MARIED, 'sender'=> $this->user->id]);

    // Send validable.
    if ($isNotUnique == null) {
      $data = ['child_id' => $this->user->id, 'couple_id' => $child->couple];

      return (new ValidableHandler)->send(
        type: ValidableHandler::TYPE_CHILD_CAN_MARIED,
        receiver: $child->couple,
        sender: $this->userId,
        data: $data
      );
    }

    return false;
  }

  /** Revoque an maried inviation.*/
  function revoqueCanBeMariedInvitation(string $validableId): bool
  {
    // $validable = new ValidableHandler;

    // $state = $validable->reject(validableId: $validableId);

    // return $state;
    return true;
  }
}
