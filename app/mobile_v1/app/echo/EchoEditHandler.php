<?php

namespace App\mobile_v1\app\echo;

use App\mobile_v1\app\comment\CommentsHandler;
use App\mobile_v1\app\reactions\LikesHandler;
use App\mobile_v1\app\reactions\ViewsHandler;
use App\Models\Echos;
use Princ\Quest\Attributs\QuestSpaw;
use Princ\Quest\QuestSpawMethod;
use stdClass;

class EchoEditHandler
{
  /**
   * Create a new class instance.
   */
  public function __construct() {}

  function get() {}

  #[QuestSpaw(ref: 'edit.getlist.giH8YUKxPAVr38DBIg', method: QuestSpawMethod::GET)]
  function getist(): array
  {
    // User :
    $user = request()->user();

    // Coms :
    /** @var array<App\Model\Communique> */
    $echos = Echos::withTrashed()->where('published_by', $user->id)->get();
    // $echos = Communique::all();

    $list = [];
    foreach ($echos as $echo) {
      $list[] = [
        'echo' => [
          'id'          => $echo->id,
          'title'       => $echo->title,
          'deleted_at'  => $echo->deleted_at,
          'created_at'  => $echo->created_at,
          'updated_at'  => $echo->updated_at,
        ],
        'reactions' => [
          'comments'  => (new CommentsHandler)->countAllOf(Echos::class, $echo->id)->count,
          'likes'     => (new LikesHandler)->countAllOf(Echos::class, $echo->id)->count,
          'views'     => (new ViewsHandler)->countAllOf(Echos::class, $echo->id)->count,
        ],
      ];
    }

    return array_reverse($list);
  }

  /** @return stdClass {state:DELETED|DAILED} */
  #[QuestSpaw(ref: 'edit.delete.qpu8Grt0tu3L1eNGj6')]
  function destroy(string $id): stdClass
  {
    $return = new stdClass;

    $state = Echos::withTrashed()->find($id)->forceDelete();

    $return->state = $state ? 'DELETED' : "FAILED";

    return $return;
  }

  /** @return stdClass {state:FAILED|HIDDEN|VISIBLE} */
  #[QuestSpaw(ref: 'edit.toggle.visibility.cxIwDFNyA7Xm9uYrAn')]
  function toggleMask(string $id): stdClass
  {
    $return = new stdClass;

    $echo = Echos::withTrashed()->find($id);

    $mainState = 'FAILED';

    if ($echo) {
      if ($echo->deleted_at) {
        // un delete.
        $echo->deleted_at = null;
        $state = $echo->save();
        if ($state) $mainState = 'VISIBLE';
      } else {
        // delete it.
        $state = $echo->delete();
        if ($state) $mainState = 'HIDDEN';
      }
    }

    $return->state = $mainState;

    return $return;
  }

  function updateState() {}

  function updateText() {}

  // --> HEAD PICTURE : -------------------------------------------------------->
  function uploadHeadImage() {}

  function removeHeadImage() {}

  // --> DOCUMENT :------------------------------------------------------------->
  function uploadDocument() {}

  function removeDocument() {}
}
