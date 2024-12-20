<?php

namespace App\mobile_v1\app\calendar;

use App\Models\User;
use Hacp0012\Quest\Attributs\QuestSpaw;
use Hacp0012\Quest\QuestSpawMethod;
use Illuminate\Http\Request;
use stdClass;

class CalendarHomeHandler
{
  /**
   * Create a new class instance.
   */
  public function __construct(Request $request)
  {
    $this->user = $request->user();
  }

  private User $user;

  #[QuestSpaw(ref: 'ouKHUS4pTKKgvBCblYbgt5ubBLA72TBnf9hK', method: QuestSpawMethod::GET)]
  public function getAll(bool $all = false): stdClass
  {
    $return = new stdClass;

    $evenHandler = new CalendarHandlerClass($this->user);

    $results = $evenHandler->getAll($all);

    $return->success = true;
    $return->events = $results;

    return $return;
  }

  #[QuestSpaw(ref: 'wDtdNHb7GNnFQ6XEfjTCb5SHxKIErluiYmVE', method: QuestSpawMethod::GET)]
  public function getOne(string $eventId): stdClass
  {
    $return = new stdClass;

    $evenHandler = new CalendarHandlerClass($this->user);

    $event = $evenHandler->getOne($eventId);

    $return->success = true;
    $return->event = $event;

    return $return;
  }
}
