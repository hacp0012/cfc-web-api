<?php

namespace App\mobile_v1\app\teaching;

use App\mobile_v1\app\comment\CommentsHandler;
use App\mobile_v1\app\reactions\LikesHandler;
use App\mobile_v1\app\reactions\ViewsHandler;
use App\mobile_v1\app\user\UserHandlerClass;
use App\mobile_v1\classes\FileHanderClass;
use App\Models\Enseignement;
use App\Models\User;
use Hacp0012\Quest\Attributs\QuestSpaw;
use Hacp0012\Quest\QuestSpawMethod;
use Illuminate\Http\Request;
use stdClass;

class TeachingHandlerClass
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
  /** @return stdClass {success:bool, teach, poster, pictures} */
  #[QuestSpaw(ref: 'get.m4tUR9UtiKrfOrwBIc9LuQ4XPU9', method: QuestSpawMethod::GET)]
  function get(string $teachId): stdClass
  {
    $return = new stdClass;
    $return->success = false;

    $teach = Enseignement::find($teachId);

    if ($teach) {
      $return->teach = $teach;

      $return->poster = $this->poster(posterId: $teach->published_by);

      // $return->pictures = [];

      // foreach ($this->pictures($teachId)->pictures as $picture) $return->pictures[] = $picture['pid'];

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
      $return->picture  = UserHandlerClass::getUserPicture($poster->id);
    }

    return $return;
  }

  # REACTIONS : ------------------------------------------------------------------------:
  /** @return stdClass {likes:{count:int,user:bool}, views:idem, comments:idem} */
  #[QuestSpaw(ref: 'reactions.yfZBOElWFt8n7GuAX4RzK6KyYDY', method: QuestSpawMethod::GET)]
  function getReactions(string $teachId): stdClass
  {
    $return = new stdClass;

    $comments = (new CommentsHandler)->countAllOf(Enseignement::class, $teachId);
    $return->comments = ['count' => $comments->count, 'user' => false];

    $views = new ViewsHandler;
    $viewsCount = $views->countAllOf(Enseignement::class, $teachId);
    $return->views    = [
      'count' => $viewsCount->count,
      'user' => (bool) $views->countOf(Enseignement::class, $teachId, $this->userId)->count,
    ];

    $likes = new LikesHandler;
    $likesCount = $likes->countAllOf(Enseignement::class, $teachId);
    $return->likes    = [
      'count' => $likesCount->count,
      'user' => (bool) $likes->countOf(Enseignement::class, $teachId, $this->userId)->count,
    ];

    return $return;
  }

  /** @return stdClass {success:bool} */
  #[QuestSpaw(ref: 'like.reaction.Sm1Sp7H9HoCmRRJNZJwnL8t5udq')]
  function like(string $teachId): stdClass
  {
    $return = new stdClass;
    $return->success = false;

    if ($this->userId) {
      $like = new LikesHandler;

      $like->toggle(Enseignement::class, $teachId, $this->userId);

      $return->success = true;
    }

    return $return;
  }

  /** @return stdClass {success:bool} */
  #[QuestSpaw(ref: 'add.view.qAyA28hWGlZw5QxFYJr1XdMef8V')]
  function addRead(string $teachId): stdClass
  {
    $return = new stdClass;

    $read = new ViewsHandler;

    $read->add(Enseignement::class, $teachId, $this->userId);

    $return->success = true;

    return $return;
  }
}
