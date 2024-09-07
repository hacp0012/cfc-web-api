<?php

namespace App\mobile_v1\app\com;

use App\mobile_v1\app\comment\CommentsHandler;
use App\mobile_v1\app\reactions\LikesHandler;
use App\mobile_v1\app\reactions\ViewsHandler;
use App\Models\Communique;
use App\Quest\QuestSpaw;
use App\quest\QuestSpawMethod;
use stdClass;

class ComEditHandler
{
  /**
   * Create a new class instance.
   */
  public function __construct() {}

  function get() {}

  #[QuestSpaw(ref: 'edit.getlist.d8CmMR0YTSeFFF6mUe', method: QuestSpawMethod::GET)]
  function getist(): array
  {
    // User :
    $user = request()->user();

    // Coms :
    /** @var array<App\Model\Communique> */
    $coms = Communique::withTrashed()->where('published_by', $user->id)->get();
    // $coms = Communique::all();

    $list = [];
    foreach($coms as $com) {
      $list[] = [
        'com' => [
          'id'          => $com->id,
          'title'       => $com->title,
          'status'      => $com->status,
          'deleted_at'  => $com->deleted_at,
          'created_at'  => $com->created_at,
          'updated_at'  => $com->updated_at,
        ],
        'reactions' => [
          'comments'  => (new CommentsHandler)->countAllOf(Communique::class, $com->id)->count,
          'likes'     => (new LikesHandler)->countAllOf(Communique::class, $com->id)->count,
          'views'     => (new ViewsHandler)->countAllOf(Communique::class, $com->id)->count,
        ],
      ];
    }

    return array_reverse($list);
  }

  /** @return stdClass {state:DELETED|DAILED} */
  #[QuestSpaw(ref: 'edit.delete.6Hwc5FQq029YMiVQkl')]
  function destroy(string $id): stdClass
  {
    $return = new stdClass;

    $state = Communique::withTrashed()->find($id)->forceDelete();

    $return->state = $state ? 'DELETED' : "FAILED";

    return $return;
  }

  /** @return stdClass {state:FAILED|HIDDEN|VISIBLE} */
  #[QuestSpaw(ref: 'edit.toggle.visibility.Lnnq0j9aiS4trdkArb')]
  function toggleMask(string $id): stdClass
  {
    $return = new stdClass;

    $com = Communique::withTrashed()->find($id);

    $mainState = 'FAILED';

    if ($com) {
      if ($com->deleted_at) {
        // un delete.
        $com->deleted_at = null;
        $state = $com->save();
        if ($state) $mainState = 'VISIBLE';
      } else {
        // delete it.
        $state = $com->delete();
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
