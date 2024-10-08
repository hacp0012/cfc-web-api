<?php

namespace App\mobile_v1\app\family;

use App\mobile_v1\app\user\UserHandlerClass;
use App\mobile_v1\handlers\ValidableHandler;
use App\Models\Couple;
use App\Models\User;
use App\Models\Validable;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class FamilyCouple
{
  /** @var \Illuminate\Support\Collection */
  public $couple = null;

  /** @var App\Models\User */
  public $user = null;

  /**
   * Create a new class instance.
   */
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

  # COUPLE ---------------------------------------------------------------------- :
  /** Find a couple of one side person : uncpleted couples.
   * @param string $where parent|child
   */
  static function findLeftCoupleBy(string $civility, string $name, string $where): Collection
  {
    $partnerhand = $civility == 'F' ? 'epoue' : 'epouse';

    if ($where == 'child') {
      /** @var Collection */
      $results = Couple::/* where('epouse', '<>', null)
        ->where('epoue', '<>', null)
        -> */where('nom', 'LIKE', "%$name%")
        ->get(['id', 'nom', 'epoue', 'epouse', 'd_mariage', 'phone', 'adresse']);
    } else {
      # Parent search.
      /** @var Collection */
      $results = Couple::where([$partnerhand => null])->where('nom', 'LIKE', "%$name%")->get(['id', 'nom', 'epoue', 'epouse', 'd_mariage', 'phone', 'adresse']);
    }

    return $results;
  }

  /** Update some couple informations. */
  function updateInfos(string $name = null, string $mariageDate = null, string $address = null, string $phoneNumber = null): bool
  {
    $data = [];

    $date = Carbon::parse($mariageDate);

    // Control data.
    if ($name) $data['nom']                         = $name;
    if ($mariageDate) $data['d_mariage']            = $mariageDate ? $date->toDateString() : null;
    if ($address) $data['adresse']                  = $address;
    if ($phoneNumber) $data['phone']                = $phoneNumber;

    // Storing.
    if (count($data) && $this->couple) {
      $state = Couple::whereId($this->couple->id)->update($data);
      return $state;
    }

    return false;
  }

  /** Accept a partner invitation via Validable.
   * The new partner will dont be maried.
   */
  function acceptPartner(string $partnerId): bool
  {
    // Check gender.
    $partneer = User::firstWhere('id', $partnerId);
    if ($partneer && $partneer->civility == $this->user->civility) return false;

    // Check if is not maried.
    $partnerhand = 'epoue';
    if ($partneer && $partneer->civility == 'S') {
      $partnerhand = 'epouse';

      $isMaried = Couple::firstWhere($partnerhand, $partnerId);
      if ($isMaried) return false;
    }

    // Accept :
    if ($partneer) {
      $isFounden = Couple::where([
        'id' => $this->couple->id,
        $this->user->civility == 'F' ? 'epoue' : 'epouse' => $this->userId,
        $partnerhand => null
      ])->first();

      if ($isFounden) {
        $state = Couple::where('id', $isFounden->id)->update([$partnerhand => $partnerId]);
        return $state;
      }
    }

    return false;
  }

  /** Send an invitation to a couple, that a partner can accept via validable.
   *
   * Sended data to Validable : type: TYPE_COUPLE_BIND, data: [partner_id]
   */
  function sendInvitationToPartner(string $coupleId): bool
  {
    // Check if has a place in this couple.
    $userHand = $this->user->civility == 'F' ? 'epoue' : 'epouse';
    $hasPlace = Couple::firstWhere(['id' => $coupleId, $userHand => null]);
    if ($hasPlace == null) return false;

    // Check if send are not maried.
    $areMaried = Couple::firstWhere($userHand, $this->user->id);
    if ($areMaried) return false;

    // Send invitation.
    if ($areMaried == null) {
      (new ValidableHandler)->send(
        type: ValidableHandler::TYPE_COUPLE_BIND,
        receiver: $coupleId,
        sender: $this->userId,
        data: ['partner_id' => $this->user->id]
      );
      return true;
    }

    return false;
  }

  /** Revoque or cancel an invitation. */
  function revoqueInvitationToPartner(): bool
  {
    // $validable = new ValidableHandler;

    // Get user validable.
    // $userValidable = Validable::firstWhere(['type' => ValidableHandler::TYPE_COUPLE_BIND, 'sender' => $this->user->id]);

    // if ($userValidable) {
      // $state = $validable->reject(validableId: $userValidable->id);

      // return $state;
    // }
    // return false;

    return true;
  }

  /** Get couple information (small infos) */
  function getCoupleInfos(): ?Collection
  {
    if ($this->couple) {
      $only = ['nom', 'epoue', 'epouse', 'd_mariage', 'adresse', 'phone', 'photo', 'enfants', 'created_at'];

      $data = collect($this->couple)->only($only)->all();

      // Fitch left partner.
      $hand = $this->user->civility == 'F' ? 'epouse' : 'epoue';
      if ($data[$hand]) {
        $partner = UserHandlerClass::getSimpleUserData((string) $data[$hand]);

        $data[$hand] = $partner;
      }

      return collect($data);
    }

    return null;
  }

  function createNewIncompletCouple(string $name): ?string
  {
    // Data.
    $data = [
      'nom' => $name,
      // 'epoue',
      // 'epouse',
      // 'd_mariage',
    ];

    // Get user civility.
    $hand = $this->user->civility == 'F' ? 'epoue' : 'epouse';

    $data[$hand] = $this->user->id;

    // Create couple.
    $newCouleId = Couple::create($data);

    // Return.
    return $newCouleId;
  }

  function checkIfHasSentInvitationRequest(): bool
  {
    $validable = Validable::firstWhere(['sender'=> $this->user->id, 'type'=> ValidableHandler::TYPE_COUPLE_BIND]);

    if ($validable) return true;

    return false;
  }
}
