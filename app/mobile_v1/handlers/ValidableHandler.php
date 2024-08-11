<?php

namespace App\mobile_v1\handlers;

use App\mobile_v1\app\user\UserHandlerClass;
use App\Models\Validable;
use InnerValidableHandler\Notify;
use InnerValidableHandler\Validator;

class ValidableHandler
{
  const TYPE_COUPLE_BIND  = 'JOIN_COUPLE_INVITATION';
  const TYPE_CHILD_BIND   = 'CHILD_JOIN_INVITATION';

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
    $state = Validable::whereId($validableId)->delete();

    return $state;
  }

  /** Mark as validated. */
  function validate(string $validableId)
  {
    $validable = Validable::firstWhere('id', $validableId);

    if ($validable) {
      $validator = new Validator(validable: $validable);

      $state = $validator->validate();

      if ($state) {
        $this->reject(validableId: $validableId);

        return $state;
      }
    }

    return false;
  }
}

namespace InnerValidableHandler;

use App\mobile_v1\handlers\ValidableHandler;

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
    return false;
  }
}

class Validator
{
  public function __construct(private mixed $validable) {}

  public function validate(): bool
  {
    switch ($this->validable->type) {
      case ValidableHandler::TYPE_CHILD_BIND:
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
    return false;
  }

  private function _typeChilBind(): bool
  {
    return false;
  }
}
