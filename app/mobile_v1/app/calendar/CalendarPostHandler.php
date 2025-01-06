<?php

namespace App\mobile_v1\app\calendar;

use App\Models\User;
use Hacp0012\Quest\Attributs\QuestSpaw;
use Hacp0012\Quest\SpawMethod;
use Illuminate\Http\Request;
use stdClass;

class CalendarPostHandler
{
  /**
   * Create a new class instance.
   */
  public function __construct(protected Request $request)
  {
    $this->user = $request->user();
  }

  private User $user;

  # METHODS *************************************************************************************************************
  #[QuestSpaw(ref: "9qAlnrEMYnsxSItYiAjIQdJ1nLvu1oxvD4Ma", method: SpawMethod::POST)]
  public function add(
    string $startDate,
    string $endDate,
    string $summary,
    string|null $description = null,
    int|null $color = null,
    array|null $metadata = null,
  ): stdClass
  {
    $return = new stdClass;
    $return->success = false;

    $eventHandler = new CalendarHandlerClass($this->user);
    // $return->temp = $metadata;
    // return $return;

    $eventId = $eventHandler->addEvent(
      startDate: $startDate,
      endDate: $endDate,
      summary: $summary,
      description: $description,
      color: $color,
      metadata: $metadata,
    );

    $return->id = $eventId;
    $return->success = $eventId != null;

    return $return;
  }
}
