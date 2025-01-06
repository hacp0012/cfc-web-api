<?php

namespace App\mobile_v1\app\home;

use App\mobile_v1\admin\CarousselMan;
use App\mobile_v1\app\com\ComHomeHandler;
use App\mobile_v1\app\teaching\TeachingHomeHandler;
use App\Models\Enseignement;
use Hacp0012\Quest\Attributs\QuestSpaw;
use Hacp0012\Quest\QuestResponse;
use Hacp0012\Quest\SpawMethod;
use Illuminate\Database\Eloquent\Collection;
use stdClass;

class HomeContentsHandler
{
  private int $contentStep = 18;

  #[QuestSpaw(ref: 'get.home.3nLq7p0NwnXpcHjH9NANsNCNWJXKTyTKxJ9V', method: SpawMethod::GET)]
  public function getHome(?array $byDate = null, int $currentPosition = 0): stdClass
  {
    $return = new stdClass;
    $return->success = false;

    # -------------------------------------------- :
    // Get coms.
    $comObject = new ComHomeHandler;
    $coms = $comObject->getSuggestions(byDate: $byDate);
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
    $teachs = $teachingObject->getSuggestions(byDate: $byDate);
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
    // $mergeds = array_merge($filledsTeachs, $filledsComs);
    $mergeds = array_merge($filledsComs, $filledsTeachs);

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

    $sliceds = $this->sliceContent($mergeds, $currentPosition);

    $return->success = true;
    $return->list = $sliceds;
    $return->currentIndex = count($sliceds) > 0 ? $currentPosition + 1 : $currentPosition;

    return $return;
  }

  private function sliceContent(array $data, int $currentStep = 0): array
  {
    $sliceds = array_slice($data, $this->contentStep * $currentStep, $this->contentStep);
    return $sliceds;
  }

  #[QuestSpaw(ref: 'get.home.caroussel.pictures.HT2eqMBIOPIVT5AeATpHwRJ5OvLgcu6iiBRS', method: SpawMethod::GET)]
  public function getCarousselPictures(): Collection
  {
    $carousselMan = new CarousselMan(request: request());

    $pictures = $carousselMan->getIts();

    QuestResponse::setForJson(ref: 'get.home.caroussel.pictures.HT2eqMBIOPIVT5AeATpHwRJ5OvLgcu6iiBRS', dataName: 'pictures');

    return $pictures;
  }
}
