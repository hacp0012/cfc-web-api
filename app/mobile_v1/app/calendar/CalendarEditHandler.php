<?php

namespace App\mobile_v1\app\calendar;

use App\Models\User;
use Hacp0012\Quest\Attributs\QuestSpaw;
use Hacp0012\Quest\QuestSpawMethod;
use Illuminate\Http\Request;
use stdClass;

class CalendarEditHandler
{
  /**
   * Create a new class instance.
   */
  public function __construct(Request $request)
  {
    $this->user = $request->user();
  }

  private User $user;

  #[QuestSpaw(ref: 'grija8VUjUIxcF0Iv9zy83V01B56hwL14kEB', method: QuestSpawMethod::POST)]
  public function update(
    string $eventId,
    string|null $startDate = null,
    string|null $endDate = null,
    string|null $summary = null,
    string|null $description = null,
    int|null $color = null,
    array|null $metadata = null,
  ): stdClass {
    $return = new stdClass;

    $eventHandler = new CalendarHandlerClass($this->user);

    $updataState = $eventHandler->update(
      eventId: $eventId,
      startDate: $startDate,
      endDate: $endDate,
      summary: $summary,
      description: $description,
      color: $color,
      metadata: $metadata,
    );

    $return->success = $updataState;

    return $return;
  }

  #[QuestSpaw(ref: 'J7bcSOI6gukwO1Zc7OrTbQiho3PXpbgGQMYO', method: QuestSpawMethod::POST)]
  public function setItDone(string $eventId, bool $doneState): stdClass
  {
    $return = new stdClass;

    $eventHandler = new CalendarHandlerClass($this->user);

    $state = $eventHandler->markAsDone($eventId, $doneState);

    $return->success = $state;

    return $return;
  }

  #[QuestSpaw(ref: 'sfUGAxLEedNFZMhcpB6fwc6XTaPIquobVm84', method: QuestSpawMethod::GET)]
  public function getAllUndones(): stdClass
  {
    $return = new stdClass;

    $eventHandler = new CalendarHandlerClass($this->user);

    $undones = $eventHandler->getAllUndones();
    $reverseds = $undones->reverse()->toArray();

    $return->success = true;
    $return->events = array_values($reverseds);

    return $return;
  }

  #[QuestSpaw(ref: 'f9LlNWizExSshq7akTjMKMJSLM5VRnRSK5lZ', method: QuestSpawMethod::GET)]
  public function getAllDones(): stdClass
  {
    $return = new stdClass;

    $eventHandler = new CalendarHandlerClass($this->user);

    $dones = $eventHandler->getAllDones();

    $reverseds = $dones->reverse()->toArray();

    $return->success = true;
    $return->events = array_values($reverseds);

    return $return;
  }

  #[QuestSpaw(ref: "djYovchblg3ClVxUrVpnuDBgilJHJ49QSsSj", method: QuestSpawMethod::DELETE)]
  public function remove(string $eventId): stdClass
  {
    $return = new stdClass;

    $eventHandler = new CalendarHandlerClass($this->user);

    $state = $eventHandler->removeEvent(eventId: $eventId);

    $return->success = $state;

    return $return;
  }

  #[QuestSpaw(ref: '5L9YlJW1zn4nyoPMkifAPNQiWIy9AtUkCwHS', method: QuestSpawMethod::GET)]
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
