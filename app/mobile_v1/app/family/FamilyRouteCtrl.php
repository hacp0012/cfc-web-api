<?php

namespace App\mobile_v1\app\family;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class FamilyRouteCtrl
{
  /**
   * Create a new class instance.
   */
  public function __construct()
  {
    //
  }

  /** section S| function F */
  public function requestHandler(Request $request)
  {
    $section = $request->input('s');
    $function = $request->input('f');

    if ($section == 'family')
      return match ($function) {
        'find_couple'     => $this->findLeftCouple($request),
        'get_couple'      => $this->coupleGetUserCouple($request),
        'update_couple'   => $this->coupleUpdate($request),
        'has_sent_couple_request'   => $this->coupleHasCoupleRequest($request),
        'send_couple_bind_request'  => $this->coupleSendCoupleBindRequest($request),
        'create_couple'   => $this->coupleCreateCouple($request),
      };

    elseif ($section == 'child')
      return match ($function) {
        'add'           => $this->childAdd($request),
        'get_children'  => $this->childGetChildren($request),
        'remove'        => $this->childRemove($request),
        'update'        => $this->childUpdate($request),
        'has_validable_to_parent' => $this->childCheckDamande($request),
        'send_demande_to_couple'  => $this->childSendDemandeToCouple($request),
        'send_can_be_maried_request'  => $this->childSendCanBeMariedToCouple($request),
        'has_can_be_maried_request'   => $this->childCheckIfCanBeMaried($request),
      };
  }

  // ---------------------------------------------------------------------- :

  function findLeftCouple(Request $request)
  {
    $couples = FamilyCouple::findLeftCoupleBy(
      civility: $request->string('civility', ''),
      name: $request->string('name', ''),
      where: $request->string('where', ''),
    );

    return $couples;
  }

  // COUPLE --------------------------------------------------------------- :
  function coupleGetUserCouple(Request $request)
  {
    $user = $request->user();

    $family = new FamilyCouple($user->id);

    $couple = $family->getCoupleInfos();

    return $couple;
  }

  function coupleUpdate(Request $request)
  {
    $user = $request->user();

    $family = new FamilyCouple($user->id);

    $updateState = $family->updateInfos(
      name: $request->input('name'),
      mariageDate: $request->input('d_mariage'),
      address: $request->input('adresse'),
      phoneNumber: $request->input('phone'),
    );

    return ['state' => $updateState ? 'UPDATED' : 'FAILED'];
  }

  function coupleHasCoupleRequest(Request $request)
  {
    $user = $request->user();

    $family = new FamilyCouple(userId: $user->id);

    $state = $family->checkIfHasSentInvitationRequest();

    return ['state' => $state ? "YES" : "NO"];
  }

  function coupleSendCoupleBindRequest(Request $request)
  {
    $user = $request->user();
    $coupleId = $request->string('couple_id', '---');

    $family = new FamilyCouple(userId: $user->id);

    $state = $family->sendInvitationToPartner(coupleId: $coupleId);

    return ['state' => $state ? "SENT" : "FAILED"];
  }

  function coupleCreateCouple(Request $request)
  {
    $user = $request->user();

    $family = new FamilyCouple(userId: $user->id);

    $state = false;

    if ($request->string('name')) {
      $newCoupleId = $family->createNewIncompletCouple(name: $request->string('name'));

      if ($newCoupleId) $state = true;
    }

    return ['state' => $state ? 'CREATED' : 'FAILED'];
  }

  // CHILD ---------------------------------------------------------------- :
  function childGetChildren(Request $request)
  {
    $user = $request->user();

    $family = new FamilyChildren($user->id);

    $children = $family->getChildren();

    return $children;
  }

  function childRemove(Request $request)
  {
    $user = $request->user();

    $family = new FamilyChildren($user->id);

    $removeState = $family->removeChild(chilId: $request->input('child_id', '---'));

    return ['state' => $removeState ? 'REMOVED' : 'FAILED'];
  }

  function childUpdate(Request $request)
  {
    $user = $request->user();

    $family = new FamilyChildren($user->id);

    $validated = $request->validate([
      'nom'         => "required|string",
      'd_naissance' => "required|string",
      'is_maried'   => "required|boolean",
      'genre'       => "required|string",
    ]);

    $date = new Carbon($request->input('d_naissance', ''));

    $updateState = $family->updateChild(childId: $request->input('child_id', '---'), data: [
      'nom'           => $validated['nom'],
      'd_naissance'   => $date->toDateString(),
      'genre'         => $validated['genre'],
      'is_maried'     => $validated['is_maried'],
      'photo_pid'     => null,
    ]);

    return ['state' => $updateState ? 'UPDATED' : 'FAILED'];
  }

  function childAdd(Request $request)
  {
    $user = $request->user();

    $family = new FamilyChildren($user->id);

    $date = new Carbon($request->input('d_naissance'));

    $request->validate([
      'nom'         => "required|string",
      'gender'      => "required|string",
      'is_maried'   => "required|boolean",
      'd_naissance' => "required|string",
    ]);

    // return $request->input();

    $addState = $family->addChild(
      nom: $request->input('nom'),
      genre: $request->input('gender'),
      d_naissance: $date->toDateString(),
      isMaried: $request->boolean('is_maried'),
      photo_pid: null,
    );

    return ['state' => $addState ? 'CREATED' : 'FAILED'];
  }

  function childCheckDamande(Request $request)
  {
    $user = $request->user();

    $child = new FamilyChildren(userId: $user->id);

    $state = $child->checkIfHasParentValidable();

    return ['state' => $state ? 'YES' : 'NO'];
  }

  function childSendDemandeToCouple(Request $request)
  {
    $user = $request->user();

    $child = new FamilyChildren(userId: $user->id);

    $state = $child->sendInvitationToParent(coupleId: $request->string('couple_id', '---'));

    return ['state' => $state ? 'SENT' : 'FAILED'];
  }

  function childSendCanBeMariedToCouple(Request $request)
  {
    $user = $request->user();

    $child = new FamilyChildren(userId: $user->id);

    $state = $child->sendCanBeMariedInvitation();

    return ['state' => $state ? 'SENT' : 'FAILED'];
  }

  function childCheckIfCanBeMaried(Request $request)
  {
    $user = $request->user();

    $child = new FamilyChildren(userId: $user->id);

    $state = $child->checkIfHasCanBeMariedValidable();

    return ['state' => $state ? 'YES' : 'NO'];
  }
}
