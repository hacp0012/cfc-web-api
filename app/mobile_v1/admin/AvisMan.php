<?php

namespace App\mobile_v1\admin;

use App\mobile_v1\app\search\SearchEngine;
use App\mobile_v1\classes\SysdataHandlerClass;
use App\mobile_v1\classes\SysDataType;
use Hacp0012\Quest\Attributs\QuestSpaw;
use Hacp0012\Quest\QuestResponse;
use Hacp0012\Quest\SpawMethod;
use Illuminate\Http\Request;

class AvisMan
{
  /**
   * Create a new class instance.
   */
  public function __construct(Request $request)
  {
    $this->user = $request->user();
  }

  private $user = null;

  // ------------------------------------------------------------------------------------ :
  private string $storeKey = 'Qx4miAbfDDAG7Gr8bK';
  private int $max = 1024;
  private int $contentStep = 18;

  #[QuestSpaw(ref: 'n8WXJPoItiYPqegHWPCw55GTwkdlDi1tzszN8LLru9jVR', method: SpawMethod::GET)]
  public function search(string $keyword): array
  {
    $data = $this->getAll();

    $fileds = ['text', 'created_at'];

    $results = (new SearchEngine(keyphrase: $keyword))->customSearchFrom(data: $data, fields: $fileds);

    QuestResponse::setForJson(
      ref: 'n8WXJPoItiYPqegHWPCw55GTwkdlDi1tzszN8LLru9jVR',
      model: ['success' => true],
      dataName: 'results',
    );

    return $results;
  }

  private function sliceContent(array $data, int $currentStep = 0): array
  {
    $sliceds = array_slice($data, $this->contentStep * $currentStep, $this->contentStep);
    return $sliceds;
  }

  #[QuestSpaw(ref: 'bcTjZdNU43bjMNO8dhVetL1z3o6ixoSJZQs0OeiUIJjix', method: SpawMethod::GET)]
  public function getAll(int $currentPosition = 0): array
  {
    $data = SysdataHandlerClass::get(type: SysDataType::ARRAY, key: $this->storeKey) ?? [];

    if (is_null($data)) return [];

    array_reverse($data);

    $sliceds = $this->sliceContent($data, $currentPosition);

    QuestResponse::setForJson(
      ref: 'bcTjZdNU43bjMNO8dhVetL1z3o6ixoSJZQs0OeiUIJjix',
      model: [
        'success' => true,
        'currentPosition' => count($sliceds) > 0 ? $currentPosition + 1 : $currentPosition,
      ],
      dataName: 'notices',
    );

    return $sliceds;
  }

  #[QuestSpaw(ref: 'ECme0q9KBkC9IIZl2QbrqFFwMehTwH7RiFOgYDzNN0HAP')]
  public function post(int $satisfactionLevel, string $text = null): bool
  {
    $data = $this->getAll();

    if (count($data) >= $this->max) array_splice(array: $data, offset: 0, length: 1);

    $userId = '---';

    if ($this->user) $userId = $this->user->id;

    $model = [
      'user_id'       => $userId,
      'level'         => $satisfactionLevel,
      'text'          => $text,
      'created_at'    => now(),
    ];

    $data[] = $model;

    $state = SysdataHandlerClass::set(type: SysDataType::ARRAY, key: $this->storeKey, data: $data);

    QuestResponse::setForJson(ref: 'ECme0q9KBkC9IIZl2QbrqFFwMehTwH7RiFOgYDzNN0HAP', dataName: 'success');

    return $state;
  }

  #[QuestSpaw(ref: 'P7PsrdUfIXdk9XhUzHeff8DXzqvaqC8Uw1mkYvaBWF1JJ', method: SpawMethod::DELETE)]
  public function remove(int $index): bool
  {
    $data = $this->getAll();

    if ($index > count($data) || $index < 0) return false;

    array_splice(array: $data, offset: $index, length: 1);

    $state = SysdataHandlerClass::set(type: SysDataType::ARRAY, key: $this->storeKey, data: $data);

    QuestResponse::setForJson(ref: 'P7PsrdUfIXdk9XhUzHeff8DXzqvaqC8Uw1mkYvaBWF1JJ', dataName: 'success');

    return $state;
  }
}
