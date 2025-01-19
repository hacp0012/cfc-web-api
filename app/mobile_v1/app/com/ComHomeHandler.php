<?php

namespace App\mobile_v1\app\com;

use App\Models\Communique;
use App\Models\User;
use Carbon\Carbon;
use Hacp0012\Quest\Attributs\QuestSpaw;
use Hacp0012\Quest\SpawMethod;
use Illuminate\Support\Facades\DB;
use stdClass;

class ComHomeHandler
{
  public function __construct() {}

  /** @param array<string,string>|null $byDate [date_A, date_B] */
  #[QuestSpaw(ref: 'home.coms.get.ImbynpTohaSCWy6Wkc0P8cCPglI', method: SpawMethod::GET)]
  public function getSuggestions(?array $byDate = null): stdClass
  {
    $return = new stdClass;
    $return->success = false;

    # Comm.
    $coms = [];
    if ($byDate && count($byDate) == 2) {
      $coms = Communique::query()
        ->whereDate('created_at', '>=', $byDate[0])
        ->whereDate('created_at', '<=', $byDate[1])
        ->get();
    } else $coms = Communique::all();
    // $reversed = $coms->shuffle();
    // var_dump($coms);
    // dd($coms);

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

    $list = array_values($list->toArray());

    $return->success = true;
    $return->coms = $list;

    return $return;
  }

  function filter() {}

  function slice() {}
}
