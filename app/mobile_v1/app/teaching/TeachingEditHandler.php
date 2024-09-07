<?php

namespace App\mobile_v1\app\teaching;

use App\mobile_v1\app\comment\CommentsHandler;
use App\mobile_v1\app\reactions\LikesHandler;
use App\mobile_v1\app\reactions\ViewsHandler;
use App\Models\Enseignement;
use App\Quest\QuestSpaw;
use App\quest\QuestSpawMethod;
use stdClass;

class TeachingEditHandler
{
  /**
   * Create a new class instance.
   */
  public function __construct() {}

  function get() {}

  #[QuestSpaw(ref: 'edit.getlist.RrOWXRfKOjauvSpc7y', method: QuestSpawMethod::GET)]
  function getist(): array
  {
    // User :
    $user = request()->user();

    // Coms :
    /** @var array<App\Model\Communique> */
    $teachings = Enseignement::withTrashed()->where('published_by', $user->id)->get();
    // $teachings = Communique::all();

    $list = [];
    foreach ($teachings as $teaching) {
      $list[] = [
        'teaching' => [
          'id'          => $teaching->id,
          'title'       => $teaching->title,
          'verse'       => $teaching->verse,
          'date'        => $teaching->date,
          'predicator'  => $teaching->predicator,
          'deleted_at'  => $teaching->deleted_at,
          'created_at'  => $teaching->created_at,
          'updated_at'  => $teaching->updated_at,
        ],
        'reactions' => [
          'comments'  => (new CommentsHandler)->countAllOf(Enseignement::class, $teaching->id)->count,
          'likes'     => (new LikesHandler)->countAllOf(Enseignement::class, $teaching->id)->count,
          'views'     => (new ViewsHandler)->countAllOf(Enseignement::class, $teaching->id)->count,
        ],
      ];
    }

    return array_reverse($list);
  }

  /** @return stdClass {state:DELETED|DAILED} */
  #[QuestSpaw(ref: 'edit.delete.jq0zdulQMkM4PQ84Fb')]
  function destroy(string $id): stdClass
  {
    $return = new stdClass;

    $state = Enseignement::withTrashed()->find($id)->forceDelete();

    $return->state = $state ? 'DELETED' : "FAILED";

    return $return;
  }

  /** @return stdClass {state:FAILED|HIDDEN|VISIBLE} */
  #[QuestSpaw(ref: 'edit.toggle.visibility.NAhLlRZW3g3Fbh30dZ')]
  function toggleMask(string $id): stdClass
  {
    $return = new stdClass;

    $teaching = Enseignement::withTrashed()->find($id);

    $mainState = 'FAILED';

    if ($teaching) {
      if ($teaching->deleted_at) {
        // un delete.
        $teaching->deleted_at = null;
        $state = $teaching->save();
        if ($state) $mainState = 'VISIBLE';
      } else {
        // delete it.
        $state = $teaching->delete();
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
