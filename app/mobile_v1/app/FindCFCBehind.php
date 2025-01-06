<?php

namespace App\mobile_v1\app;

use App\mobile_v1\app\search\Section;
use App\Models\Pcn;
use Hacp0012\Quest\Attributs\QuestSpaw;
use Hacp0012\Quest\QuestResponse;
use Hacp0012\Quest\SpawMethod;

class FindCFCBehind
{
  /**
   * Create a new class instance.
   */
  public function __construct() {}

  /**
   * @param string $section POOL | COM | NA
   */
  #[QuestSpaw(ref: 'w5JdBPrKjtn0YngSQqJAvkWmxlzzAw7qtWJs', method: SpawMethod::GET)]
  function areaTowers(float $lat, float $lon, String $section, float $ray = .01): array
  {
    $rayon = $ray; // .01; // 1.5Km
    $cfcs = collect();
    $founden = [];

    // Get.
    $cfcs = Pcn::whereType($section)->get();

    // Calculate square area.
    if (count($cfcs) > 0) {
      // ['lat' => .0, 'lon' => .0];

      foreach ($cfcs as $cfc) {
        $coord = $cfc->gps;

        if ($coord == null) continue;
        // elseif ($coord && $coord['lat'] == .0 && $coord['lon'] == .0) continue;

        $tLat = floatval($coord['lat'] ?? .0);
        $tLon = floatval($coord['lon'] ?? .0);
        // NE
        if (($lat < $tLat && $tLat < ($lat + $rayon)) && ($lon < $tLon && $tLon < ($lon + $rayon))) $founden[] = $cfc;
        // ES
        if (($lon < $tLon && $tLon < ($lon + $rayon)) && ($lat > $tLat && $tLat > ($lat - $rayon))) $founden[] = $cfc;
        // SW
        if (($lat < $tLat && $tLat < ($lat - $rayon)) && ($lon > $tLon && $tLon > ($lon - $rayon))) $founden[] = $cfc;
        // WN
        if (($lat < $tLat && $tLat < ($lat - $rayon)) && ($lat < $tLat && $tLat < ($lat + $rayon))) $founden[] = $cfc;
      }
    }

    // Set quest response.
    QuestResponse::setForJson(ref: 'w5JdBPrKjtn0YngSQqJAvkWmxlzzAw7qtWJs', dataName: 'founden');

    // return $cfcs->toArray();
    return $founden;
  }
}
