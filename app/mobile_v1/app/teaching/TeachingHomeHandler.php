<?php

namespace App\mobile_v1\app\teaching;

use App\Models\Enseignement;
use App\Models\User;
use Hacp0012\Quest\Attributs\QuestSpaw;
use Hacp0012\Quest\QuestSpawMethod;
use stdClass;

class TeachingHomeHandler
{
  public function __construct() {}

  #[QuestSpaw(ref: 'home.teachs.get.tNakED4gqPiuBHcOGHa7IT3U86n', method: QuestSpawMethod::GET)]
  public function getSuggestions(): stdClass
  {
    $return = new stdClass;
    $return->success = false;

    # Comm.
    $teachs = Enseignement::all();
    // $reversed = $teachs->shuffle();

    # Poster & Reactions
    $list = collect();
    foreach ($teachs as $teach) {
      $publiser = User::find($teach->published_by);

      if ($publiser) {
        $poster = ['id' => $publiser->id];

        $teachHandler = new TeachingHandlerClass(request: request());
        $reactions = $teachHandler->getReactions($teach->id);

        $list->add([
          'teach' => $teach,
          'poster' => $poster,
          'reactions' => $reactions,
        ]);
      }
    }

    $return->success = true;
    $return->teachs = $list;

    return $return;
  }

  function filter() {}

  function slice() {}
}
