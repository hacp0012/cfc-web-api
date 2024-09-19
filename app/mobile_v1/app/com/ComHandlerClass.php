<?php

namespace App\mobile_v1\app\com;

use App\mobile_v1\app\comment\CommentsHandler;
use App\mobile_v1\app\reactions\LikesHandler;
use App\mobile_v1\app\reactions\ViewsHandler;
use App\mobile_v1\app\user\UserHandlerClass;
use App\mobile_v1\classes\FileHanderClass;
use App\Models\Communique;
use App\Models\User;
use Hacp0012\Quest\Attributs\QuestSpaw;
use Hacp0012\Quest\QuestSpawMethod;
use Illuminate\Http\Request;
use stdClass;

class ComHandlerClass
{
  /**
   * Create a new class instance.
   */
  public function __construct(Request $request)
  {
    $this->userId = $request->user()?->id;
  }

  private string $userId;

  # ECHO : -----------------------------------------------------------------------------:
  /** @return stdClass {success:bool, com, poster} */
  #[QuestSpaw(ref: 'get.3MbecKe6Vd0CvcvDqu6n37Du74O', method: QuestSpawMethod::GET)]
  function get(string $comId): stdClass
  {
    $return = new stdClass;
    $return->success = false;

    $com = Communique::find($comId);

    if ($com) {
      $return->com = $com;

      $return->poster = $this->poster(posterId: $com->published_by);

      $return->success = true;
    }

    return $return;
  }

  /** @return stdClass */
  function poster(string $posterId): stdClass
  {
    $return = new stdClass;

    $poster = User::find($posterId);

    if ($poster) {
      $return->id       = $poster->id;
      $return->name     = $poster->name;
      $return->fullname = $poster->fullname;
      $return->pool     = $poster->pool;
      $return->civility = $poster->civility;
      // $return->picture  = UserHandlerClass::getUserPicture($poster->id);
    }

    return $return;
  }

  # REACTIONS : ------------------------------------------------------------------------:
  /** @return stdClass {likes:{count:int,user:bool}, views:idem, comments:idem} */
  #[QuestSpaw(ref: 'reactions.wgg3p0XpKDzkMiO9xOGjuG4bqim', method: QuestSpawMethod::GET)]
  function getReactions(string $comId): stdClass
  {
    $return = new stdClass;

    $comments = (new CommentsHandler)->countAllOf(Communique::class, $comId);
    $return->comments = ['count' => $comments->count, 'user' => false];

    $views = new ViewsHandler;
    $viewsCount = $views->countAllOf(Communique::class, $comId);
    $return->views    = [
      'count' => $viewsCount->count,
      'user' => (bool) $views->countOf(Communique::class, $comId, $this->userId)->count,
    ];

    $likes = new LikesHandler;
    $likesCount = $likes->countAllOf(Communique::class, $comId);
    $return->likes    = [
      'count' => $likesCount->count,
      'user' => (bool) $likes->countOf(Communique::class, $comId, $this->userId)->count,
    ];

    return $return;
  }

  /** @return stdClass {success:bool} */
  #[QuestSpaw(ref: 'like.reaction.sfCUO1AFChRWhGhkwZxcR5wOm9l')]
  function like(string $comId): stdClass
  {
    $return = new stdClass;
    $return->success = false;

    if ($this->userId) {
      $like = new LikesHandler;

      $like->toggle(Communique::class, $comId, $this->userId);

      $return->success = true;
    }

    return $return;
  }

  /** @return stdClass {success:bool} */
  #[QuestSpaw(ref: 'add.view.AXS3Szzd7P3WCgMMkBICFohEI4R')]
  function addRead(string $comId): stdClass
  {
    $return = new stdClass;

    $read = new ViewsHandler;

    $read->add(Communique::class, $comId, $this->userId);

    $return->success = true;

    return $return;
  }
}
