<?php

namespace App\mobile_v1\app\echo;

use App\mobile_v1\app\comment\CommentsHandler;
use App\mobile_v1\app\reactions\LikesHandler;
use App\mobile_v1\app\reactions\ViewsHandler;
use App\mobile_v1\app\user\UserHandlerClass;
use App\mobile_v1\classes\FileHanderClass;
use App\Models\Echos;
use App\Models\User;
use Hacp0012\Quest\Attributs\QuestSpaw;
use Hacp0012\Quest\QuestSpawMethod;
use Illuminate\Http\Request;
use stdClass;

class EchoHandlerClass
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
  /** @return stdClass {success:bool, echo, poster, pictures} */
  #[QuestSpaw(ref: 'get.WM6cArGmD28c34T93173emBfxQl', method: QuestSpawMethod::GET)]
  function get(string $echoId): stdClass
  {
    $return = new stdClass;
    $return->success = false;

    $echo = Echos::find($echoId);

    if ($echo) {
      $return->echo = $echo;

      $return->poster = $this->poster(posterId: $echo->published_by);

      $return->pictures = [];

      foreach($this->pictures($echoId)->pictures as $picture) $return->pictures[] = $picture['pid'];

      $return->success = true;
    }

    return $return;
  }

  /** @return stdClass {success:bool, pictures:array} */
  function pictures(string $echoId): stdClass
  {
    $return = new stdClass;
    $return->success = true;

    $pictures = FileHanderClass::get(
      type: FileHanderClass::TYPE['IMAGE'],
      owner: $echoId,
      ownerGroup: 'ECHO',
      contentGroup: 'ATACHED_IMAGE'
    );

    $return->pictures = $pictures->toArray();

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
  #[QuestSpaw(ref: 'reactions.2aMeWrZNsATywaFe9IfouKnH2Et', method: QuestSpawMethod::GET)]
  function getReactions(string $echoId): stdClass
  {
    $return = new stdClass;

    $comments = (new CommentsHandler)->countAllOf(Echos::class, $echoId);
    $return->comments = ['count' => $comments->count, 'user' => false];

    $views = new ViewsHandler;
    $viewsCount = $views->countAllOf(Echos::class, $echoId);
    $return->views    = [
      'count' => $viewsCount->count,
      'user' => (bool) $views->countOf(Echos::class, $echoId, $this->userId)->count,
    ];

    $likes = new LikesHandler;
    $likesCount = $likes->countAllOf(Echos::class, $echoId);
    $return->likes    = [
      'count' => $likesCount->count,
      'user' => (bool) $likes->countOf(Echos::class, $echoId, $this->userId)->count,
    ];

    return $return;
  }

  /** @return stdClass {success:bool} */
  #[QuestSpaw(ref: 'like.reaction.Tcn5FcFNnozQ0SX725E7HGpzmPo')]
  function like(string $echoId): stdClass
  {
    $return = new stdClass;
    $return->success = false;

    if ($this->userId) {
      $like = new LikesHandler;

      $like->toggle(Echos::class, $echoId, $this->userId);

      $return->success = true;
    }

    return $return;
  }

  /** @return stdClass {success:bool} */
  #[QuestSpaw(ref: 'add.view.rBXQcq9eeABp0GMqjC1LkS7BKsH')]
  function addRead(string $echoId): stdClass
  {
    $return = new stdClass;

    $read = new ViewsHandler;

    $read->add(Echos::class, $echoId, $this->userId);

    $return->success = true;

    return $return;
  }
}
