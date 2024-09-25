<?php

namespace App\mobile_v1\app\echo;

use App\Models\Echos;
use App\Models\User;
use Hacp0012\Quest\Attributs\QuestSpaw;
use Hacp0012\Quest\QuestSpawMethod;
use Illuminate\Support\Str;
use stdClass;

class EchoHomeHandler
{
  public function __construct() {}

  #[QuestSpaw(ref: 'home.echos.get.mVBuu9LnEPpBNFm9dJBPFUUNIrz', method: QuestSpawMethod::GET)]
  public function getSuggestions(): stdClass
  {
    $return = new stdClass;
    $return->success = false;

    # Comm.
    $echos = Echos::all();
    $reversed = $echos->shuffle();

    # Poster & Reactions
    $list = collect();
    foreach ($reversed as $echo) {
      $publiser = User::find($echo->published_by);

      $echo->text = Str::words($echo->text, 27, '...');

      if ($publiser) {
        $poster = ['id' => $publiser->id, 'name' => $publiser->fullname, 'pool' => $publiser->pool];

        $echoHandler = new EchoHandlerClass(request: request());
        $reactions = $echoHandler->getReactions($echo->id);
        $pictures_ = $echoHandler->pictures($echo->id);
        $pictures = [];

        foreach($pictures_->pictures as $picture) $pictures[] = $picture['pid'];

        $list->add([
          'echo' => $echo,
          'poster' => $poster,
          'reactions' => $reactions,
          'pictures' => $pictures,
        ]);
      }
    }

    $return->success = true;
    $return->echos = $list;

    return $return;
  }

  function filter() {}

  function slice() {}
}
