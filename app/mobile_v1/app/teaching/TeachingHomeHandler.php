<?php

namespace App\mobile_v1\app\teaching;

use App\Models\Enseignement;
use App\Models\User;
use Hacp0012\Quest\Attributs\QuestSpaw;
use Hacp0012\Quest\SpawMethod;
use stdClass;

class TeachingHomeHandler
{
  public function __construct() {}

  #[QuestSpaw(ref: 'home.teachs.get.tNakED4gqPiuBHcOGHa7IT3U86n', method: SpawMethod::GET)]
  public function getSuggestions(?array $byDate = null): stdClass
  {
    $return = new stdClass;
    $return->success = false;

    # Teachings.
    $teachs = [];
    if ($byDate && count($byDate) == 2) {
      $teachs = Enseignement::query()
        ->whereDate('date', '>=', $byDate[0])
        ->whereDate('date', '<=', $byDate[1])
        ->get();
    } else {
      $teachs = Enseignement::all();
    }

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
    $list = array_values($list->toArray());

    $return->success = true;
    $return->teachs = $list;

    return $return;
  }

  function filter() {}

  function slice() {}
}
