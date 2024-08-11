<?php

namespace App\mobile_v1\app\family;

use App\mobile_v1\app\user\UserHandlerClass;
use App\mobile_v1\handlers\ValidableHandler;
use App\Models\ChildOf;
use App\Models\Couple;
use App\Models\User;
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
    // Check if are not a child of an other couple.
    $isChild = ChildOf::firstWhere(['type' => 'CONCRET', 'child' => $childId]);
    if ($isChild) return false;

    // Add to family.
    if ($isChild == null) {
      $childOf = new ChildOf;

      $childOf->type        = 'CONCRET';
      $childOf->parent_type = $parentType;
      $childOf->couple      = $this->couple->id;
      $childOf->child       = $childId;

      $state = $childOf->save();
      return $state;
    }

    return false;
  }

  /** Send an invitation to a parent. */
  function sendInvitationToParent(string $coupleId): bool
  {
    // Check wether user are not a child of an other couple.
    $isChild = ChildOf::firstWhere(['type' => 'CONCRET', 'child' => $this->user->id]);
    if ($isChild) return false;

    // Send validable.
    if ($isChild) {
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

  /** Revoque ou cancel an inviation. */
  function revoqueInvitationToParent(string $validableId): bool
  {
    $validable = new ValidableHandler;

    $data = [
      'child_id' => $this->user->id,
    ];

    return $validable->reject(validableId: $validableId);
    //
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
        $userHandler = new UserHandlerClass(userId: $child->id);
        $data = $userHandler->getUserData();

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
    $child = ChildOf::firstWhere(['type' => 'VIRTUAL', 'data->id' => $childId]);

    // Start Update.
    if ($child) {
      $_data = [];

      if ($data['nom']) $_data['nom'] = $data['nom'];
      if ($data['genre']) $_data['genre'] = $data['genre'];
      if ($data['d_naissance']) $_data['d_naissance'] = $data['d_naissance'];
      if ($data['photo_id']) $_data['photo_id'] = $data['photo_id'];

      if (count($_data)) {
        $child->data = Json::encode($_data);

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
    $children = $this->getChildren();

    // Loop on them & remove.
    foreach ($children as $child) {
      if (($child->type == 'CONCRET' && $child->child == $chilId) || ($child->type == 'VIRTUAL' && $child->data['id'] == $chilId)) {
        return $child->delete();
      }
    }

    return false;
  }

  /** Add or create a new virtual child. */
  function addChild(string $nom, string $genre, string $d_naissance, string $photo_pid = null): bool
  {
    // Datas.
    $data = [
      'id'            => Uuid::uuid4(),
      'nom'           => $nom,
      'genre'         => $genre,
      'd_naissance'   => $d_naissance,
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
}
