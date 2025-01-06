<?php

namespace App\mobile_v1\app\user;

use App\mobile_v1\classes\Constants;
use App\mobile_v1\classes\FileHanderClass;
use App\Models\Admin;
use App\Models\CalendarEvent;
use App\Models\ChildOf;
use App\Models\Comment;
use App\Models\Communique;
use App\Models\Couple;
use App\Models\Echos;
use App\Models\Enseignement;
use App\Models\Reaction;
use App\Models\User;
use App\Models\Validable;
use Illuminate\Support\Facades\Log;

/** DESTROY ALL OF THIS:
 *
 * - user profile
 * - user comments
 * - user admin
 * - user coms
 * - user echos
 * - user teachings
 * - user notifications
 * - user calendar events
 * - user children
 * - user couple
 * - user login tokens
 * - user reactions
 * - user validable
 */
class UserDestroyer
{
  /**
   * Create a new class instance.
   */
  public function __construct(protected string $userId) {}

  /** Start destroying. */
  public function destroy(): bool
  {
    Log::alert("STARTING DELETING USER ACCOUNT : " . $this->userId);

    if ($this->profile())         Log::info("USER PROFILE DELETED IN DATABASE");
    if ($this->comments())        Log::info("ALL ASSOCIATEDS COMMENTS DELETED IN DATABASE");
    if ($this->admin())           Log::info("ADMIN BADGET DELETED IN DATABASE");
    if ($this->coms())            Log::info("ALL PUBLISHEDS COMMUNIQUES DELETED IN DATABASE");
    if ($this->echos())           Log::info("ALL PUBLISHEDS ECHOS DELETED IN DATABASE");
    if ($this->teachings())       Log::info("ALL PUBLISHEDS TEACHINGS DELETED IN DATABASE");
    if ($this->notifications())   Log::info("ALL SENT NOTIFICATIONS DELETED IN DATABASE");
    if ($this->calendarEvents())  Log::info("ALL EVENT SHEDULED BY THIS USER, DELETED IN DATABASE");
    if ($this->child())           Log::info("ALL CHILD STATUS DELETED IN DATABASE");
    if ($this->couple())          Log::info("CREATED COUPLE DELETED IN DATABASE");
    if ($this->loginTokens())     Log::info("ALL LOGIN TOKENS DELETED IN DATABASE");
    if ($this->reactions())       Log::info("ALL REACTIONS DELETED IN DATABASE");
    if ($this->validables())      Log::info("ALL SENTS VALIDABLES, DELETED IN DATABASE");

    Log::alert("FINISHED DELETED PROFILE");

    return true;
  }

  // ! ACTIONS ! -----------------------------------------------------------------------------------:

  private function profile(): bool
  {
    // Destroy pictures
    $pictures = FileHanderClass::get(
      type: FileHanderClass::TYPE['IMAGE'],
      owner: $this->userId,
      ownerGroup: Constants::GROUPS_USER,
      contentGroup: 'PHOTO_PROFILE',
    );

    foreach ($pictures as $picture) FileHanderClass::destroy(id: $picture->id);

    // Delete user.
    $state = User::withTrashed()->where('id', $this->userId)->forceDelete();

    return $state;
  }

  private function comments(): bool
  {
    $state = Comment::whereUser($this->userId)->delete();

    return $state;
  }

  private function admin(): bool
  {
    $state = Admin::whereUser_ref($this->userId)->delete();

    return $state;
  }

  private function coms(): bool
  {
    // delete documeent
    // delete pictures
    // delete coms
    // delete comments
    // ------------------------

    // Get all for this user.
    $coms = Communique::wherePublished_by($this->userId)->get();

    if (count($coms)) {
      foreach($coms as $com) {
        // Destroying pictures
        FileHanderClass::destroy(publicId: $com->picture);
        // Destroying documents
        FileHanderClass::destroy(publicId: $com->document);
        // Destroy comments
        Comment::withTrashed()->where(['for' => Communique::class, 'for_id' => $com->id])->forceDelete();
      }
    }

    return true;
  }

  private function echos(): bool
  {
    // delete audios
    // delete documeent
    // delete pictures
    // delete coms
    // delete comments
    // ------------------------

    // Get all for this user.
    $echos = Echos::wherePublished_by($this->userId)->get();

    if (count($echos)) {
      foreach ($echos as $echo) {
        // Destroy pictures
        $pictures = FileHanderClass::get(
          type: FileHanderClass::TYPE['IMAGE'],
          owner: $echo->id,
          ownerGroup: Constants::GROUPS_ECHO,
          contentGroup: 'ATACHED_IMAGE',
        );

        foreach($pictures as $picture) {
          FileHanderClass::destroy(id: $picture->id);
        }

        // Destroying audios
        FileHanderClass::destroy(publicId: $echo->audio);
        // Destroying documents
        FileHanderClass::destroy(publicId: $echo->document);
        // Destroy comments
        Comment::withTrashed()->where(['for' => Echos::class, 'for_id' => $echo->id])->forceDelete();
      }
    }

    return true;
  }

  private function teachings(): bool
  {
    // delete audios
    // delete documeent
    // delete pictures
    // delete coms
    // delete comments
    // ------------------------

    // Get all for this user.
    $teachings = Enseignement::wherePublished_by($this->userId)->get();

    if (count($teachings)) {
      foreach ($teachings as $teaching) {
        // Destroy pictures
        FileHanderClass::destroy(publicId: $teaching->picture);
        // Destroying audios
        FileHanderClass::destroy(publicId: $teaching->audio);
        // Destroying documents
        FileHanderClass::destroy(publicId: $teaching->document);
        // Destroy comments
        Comment::withTrashed()->where(['for' => Enseignement::class, 'for_id' => $teaching->id])->forceDelete();
      }
    }

    return true;
  }

  private function notifications(): bool
  {
    // TODO: Check notification owner In notification data.

    return true;
  }

  private function calendarEvents(): bool
  {
    $state = CalendarEvent::whereCreated_by($this->userId)->delete();

    return $state;
  }

  private function child(): bool
  {
    $state = ChildOf::whereChild($this->userId)->delete();

    return $state;
  }

  private function couple(): bool
  {
    $state = Couple::whereEpoue($this->userId)->whereOr('epouse', $this->userId)->delete();

    return $state;
  }

  private function loginTokens(): bool
  {
    $user = User::find($this->userId);

    if ($user) {
      // TODO: Make good user request to delete login tokens. use request()->user()->tokens()->delete();
      $state = $user->tokens()->delete();
      // $state = request()->user()->tokens()->delete();

      return $state;
    }
    return false;
  }

  private function reactions(): bool
  {
    $state = Reaction::whereBy($this->userId)->delete();

    return $state;
  }

  private function validables(): bool
  {
    $state = Validable::whereSender($this->userId)->whereOr('receiver', $this->userId)->delete();

    return $state;
  }
}
