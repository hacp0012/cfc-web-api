<?php

namespace App\mobile_v1\handlers;

use App\mobile_v1\app\user\UserHandlerClass;
use App\Models\Validable;
use InnerValidableHandler\Notify;
use InnerValidableHandler\Rejector;
use InnerValidableHandler\Validator;

class ValidableHandler
{
  const TYPE_COUPLE_BIND        = 'JOIN_COUPLE_INVITATION';
  const TYPE_CHILD_BIND         = 'CHILD_JOIN_INVITATION';
  const TYPE_CHILD_CAN_MARIED   = 'CHILD_CAN_BE_MARIED';

  /**
   * Create a new class instance.
   */
  public function __construct()
  {
    //
  }

  /** Send a notification.
   * If the receiver are maried, then send this nofification
   * to both couple members.
   * The validable is sent to a couple.
   */
  public function send(string $type, string $sender, string $receiver, array $data = null): bool
  {
    Validable::create([
      'type'      => $type,
      'sender'    => $sender,
      'receiver'  => $receiver,
      'datas'     => $data ?? '{}',
    ]);

    // Send notification.
    $notify = new Notify(receiver: $receiver, sender: $sender);
    return $notify->send(type: $type);

    return false;
  }

  /** Get a list of all validable notifications of a user.
   * Some notificatitons are avalable for both member of
   * a couple.
   * @return array<string,array>
   */
  function getAllOut(string $receiver): array
  {
    // Get user & couple.
    $userHandler = new UserHandlerClass(userId: $receiver);
    $userCouple = $userHandler->getUserCouple();

    // get all validables for user.
    $forUser = Validable::where('receiver', $receiver)->get();

    // get all validables for couple.
    $forCouple = Validable::where('receiver', $userCouple?->id)->get();

    // end.
    return ['user' => $forUser, 'couple' => $forCouple];
  }

  /** @return int 0 or 1+ */
  public function checkIfHas(string $receiverId): int
  {
    $result = $this->getAllOut(receiver: $receiverId);

    $forUser = $result['user'];
    $forCouple = $result['couple'];

    if (count($forUser) || count($forCouple)) {
      return count($forCouple) + count($forUser);
    } else {
      return 0;
    }
  }

  /** Get all validables sents */
  function getAllSentOf(string $senderId): array
  {
    // Get user & couple.
    $userHandler = new UserHandlerClass(userId: $senderId);
    $userCouple = $userHandler->getUserCouple();

    // get all validables for user.
    $forUser = Validable::where('sender', $senderId)->get();

    // get all validables for couple.
    $forCouple = Validable::where('sender', $userCouple?->id)->get();

    // end.
    return ['user' => $forUser, 'couple' => $forCouple];
  }

  /** Rejecte a validable notification. */
  function reject(string $validableId): bool
  {
    /** @var \App\Models\Validable|null */
    $validable = Validable::firstWhere('id', $validableId);

    if ($validable) {
      $validator = new Rejector(validable: $validable);

      $state = $validator->reject();

      if ($state) $validable->delete();

      return $state;
    }

    return false;
  }

  /** Mark as validated. */
  function validate(string $validableId): bool
  {
    /** @var \App\Models\Validable|null */
    $validable = Validable::firstWhere('id', $validableId);

    if ($validable) {
      $validator = new Validator(validable: $validable);

      $state = $validator->validate();

      if ($state) {
        $removeState = Validable::where('id', $validableId)->delete();

        return $removeState;
      }
    }

    return false;
  }
}

namespace InnerValidableHandler;

use App\mobile_v1\app\family\FamilyChildren;
use App\mobile_v1\app\family\FamilyCouple;
use App\mobile_v1\handlers\ValidableHandler;
use App\Models\Validable;

class Notify
{
  public function __construct(
    protected string $receiver,
    protected string $sender,
  ) {
    //
  }

  public function send(string $type): bool
  {
    return true;
  }
}

class Validator
{
  public function __construct(private Validable $validable) {}

  public function validate(): bool
  {
    switch ($this->validable->type) {
      case ValidableHandler::TYPE_CHILD_BIND:
        return $this->_typeChilBind();
        break;

      case ValidableHandler::TYPE_CHILD_CAN_MARIED:
        return $this->_typeChilBind();
        break;

      case ValidableHandler::TYPE_COUPLE_BIND:
        return $this->_typeCoupleBind();
        break;
    }

    return false;
  }

  private function _typeCoupleBind(): bool
  {
    $user = request()->user();

    $family = new FamilyCouple(userId: $user->id);

    $validationState = $family->acceptPartner($this->validable->sender);

    return $validationState;
  }

  private function _typeChilBind(): bool
  {
    $user = request()->user();

    $childrenHander = new FamilyChildren(userId: $user->id);

    $state = false;

    // default accept as parent.
    if ($this->validable->type == ValidableHandler::TYPE_CHILD_BIND) {
      $state = $childrenHander->acceptChild(childId: $this->validable->sender);
    } elseif ($this->validable->type == ValidableHandler::TYPE_CHILD_CAN_MARIED) {
      $state = $childrenHander->acceptChildCanBeMaried(childId: $this->validable->sender);
    }

    return $state;
  }
}

class Rejector
{
  public function __construct(private Validable $validable) {}

  public function reject(): bool
  {
    switch ($this->validable->type) {
      case ValidableHandler::TYPE_CHILD_BIND:
        return $this->_typeChilBind();
        break;

      case ValidableHandler::TYPE_CHILD_CAN_MARIED:
        return $this->_typeChilBind();
        break;

      case ValidableHandler::TYPE_COUPLE_BIND:
        return $this->_typeCoupleBind();
        break;
    }

    return false;
  }

  // When rejected: The child account is deleted if there are not validated yet.
  private function _typeChilBind(): bool
  {
    $user = request()->user();

    $childrenHander = new FamilyChildren(userId: $user->id);

    if ($this->validable->type == ValidableHandler::TYPE_CHILD_BIND) {
      $state = $childrenHander->revoqueInvitationToParent($this->validable->id);
    } elseif ($this->validable->type == ValidableHandler::TYPE_CHILD_CAN_MARIED) {
      $state = $childrenHander->revoqueCanBeMariedInvitation($this->validable->id);
    }

    return $state;
  }

  private function _typeCoupleBind(): bool
  {
    $user = request()->user();

    $couplenHandler = new FamilyCouple(userId: $user->id);

    $state = $couplenHandler->revoqueInvitationToPartner();

    return $state;
  }
}
