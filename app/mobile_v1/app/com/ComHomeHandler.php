<?php

namespace App\mobile_v1\app\com;

use App\Models\Communique;
use App\Models\User;
use Hacp0012\Quest\Attributs\QuestSpaw;
use Hacp0012\Quest\QuestSpawMethod;
use stdClass;

class ComHomeHandler
{
  public function __construct() {}

  #[QuestSpaw(ref: 'home.coms.get.ImbynpTohaSCWy6Wkc0P8cCPglI', method:QuestSpawMethod::GET)]
  public function getSuggestions(): stdClass
  {
    $return = new stdClass;
    $return->success = false;

    # Comm.
    $coms = Communique::all();
    // $reversed = $coms->shuffle();

    # Poster & Reactions
    $list = collect();
    foreach ($coms as $com) {
      // $publiser = User::find($com->published_by);

      // if ($publiser) {
      // $poster = ['id' => $publiser->id];

      $comHandler = new ComHandlerClass(request: request());
      $reactions = $comHandler->getReactions($com->id);

      $list->add([
        'com' => $com,
        // 'poster' => $poster,
        'reactions' => $reactions,
      ]);
      // }
    }

    $return->success = true;
    $return->coms = $list;

    return $return;
  }

  function filter() {}

  function slice() {}
}
