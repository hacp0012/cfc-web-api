<?php

namespace App\mobile_v1\app\home;

use App\mobile_v1\app\com\ComHomeHandler;
use App\mobile_v1\app\teaching\TeachingHomeHandler;
use Hacp0012\Quest\Attributs\QuestSpaw;
use Hacp0012\Quest\QuestSpawMethod;
use stdClass;

class HomeContentsHandler
{
  #[QuestSpaw(ref: 'get.home.3nLq7p0NwnXpcHjH9NANsNCNWJXKTyTKxJ9V', method: QuestSpawMethod::GET)]
  public function getHome(): stdClass
  {
    $return = new stdClass;
    $return->success = false;

    # -------------------------------------------- :
    // Get coms.
    $comObject = new ComHomeHandler;
    $coms = $comObject->getSuggestions();
    $filledsComs = [];

    foreach ($coms->coms as $item) {
      $filledsComs[] = [
        'type' => 'COM',
        'created_at' => $item['com']->created_at,
        'item' => $item,
      ];
    }

    // Get teachings.
    $teachingObject = new TeachingHomeHandler;
    $teachs = $teachingObject->getSuggestions();
    $filledsTeachs = [];

    foreach ($teachs->teachs as $item) {
      $filledsTeachs[] = [
        'type' => 'TEACH',
        'created_at' => $item['teach']->created_at,
        'item' => $item,
      ];
    }

    // Get echos.
    // TODO: add get echo at home.

    # -------------------------------------------- :
    // Merge.
    $mergeds = array_merge($filledsTeachs, $filledsComs);

    // Sort.
    // $sorteds = [];

    $comp = function ($a, $b) {
      // return $a <=> $b;
      /** @var \Illuminate\Support\Carbon */
      $atA = $a['created_at'];

      /** @var \Illuminate\Support\Carbon */
      $atB = $b['created_at'];

      // $atA->lessThan($atB);
      // $atA->equalTo($atB);

      if ($atA->equalTo($atB)) {
        return 0;
      }
      return ($atA->lessThan($atB)) ? 1 : -1;
    };

    usort($mergeds, $comp);
    # --------------------------------------------- :

    $return->success = true;
    $return->list = $mergeds;

    return $return;
  }
}
