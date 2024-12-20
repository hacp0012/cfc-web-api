<?php

namespace App\mobile_v1\app\calendar;

use App\Models\CalendarEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use stdClass;

class CalendarHandlerClass
{
  /**
   * Create a new class instance.
   */
  public function __construct(private User $user) {}


  # METHODS ****************************************************************************************************************
  public function addEvent(
    string $startDate,
    string $endDate,
    string $summary,
    string|null $description = null,
    int|null $color = null,
    array|null $metadata = null,
  ): string|null { # return id.
    $userRole = $this->user->role;

    if ($userRole['state'] == 'ACTIVE') {

      $level = $userRole['level'];
      $levelId = $this->user->pool;


      $state = CalendarEvent::create([
        'visibility'  => ['level' => $level, 'leve_id' => $levelId],
        'start'       => $startDate,
        'end'         => $endDate,
        'done'        => false,
        'summary'     => $summary,
        'description' => $description,
        'color'       => $color,
        'metadata'    => $metadata,
        'created_by'  => $this->user->id,
      ]);

      return $state->id;
    }

    return null;
  }

  public function markAsDone(string $eventId, bool $done = true): bool
  {
    $userRole = $this->user->role;

    if ($userRole['state'] == 'ACTIVE') {
      $event = CalendarEvent::find($eventId);

      if ($event) {
        $event->done = $done;

        $state = $event->save();

        return $state;
      }
    }

    return false;
  }

  public function removeEvent(string $eventId): bool
  {
    $userRole = $this->user->role;

    if ($userRole['state'] == 'ACTIVE') {
      $event = CalendarEvent::find($eventId);

      if ($event) {
        $state = $event->delete();

        return $state;
      }
    }

    return false;
  }

  public function update(
    string $eventId,
    string|null $startDate = null,
    string|null $endDate = null,
    string|null $summary = null,
    string|null $description = null,
    int|null $color = null,
    array|null $metadata = null,
  ): bool {
    $userRole = $this->user->role;

    if ($userRole['state'] == 'ACTIVE') {
      $event = CalendarEvent::find($eventId);

      if ($event) {
        if ($startDate) $event->start           = $startDate;
        if ($endDate) $event->end               = $endDate;
        if ($description) $event->description   = $description;
        if ($summary) $event->summary           = $summary;
        if ($color) $event->color               = $color;
        if ($metadata) $event->metadata         = $metadata;

        if ($startDate || $endDate || $description || $summary || $color || $metadata) {
          $state = $event->save();

          return $state;
        }
      }
    }

    return false;
  }

  private function updateDoneState()
  {
    CalendarEvent::query()->whereDate('end', '<', now())->update(['done' => true]);
  }

  public function getOne(string $eventId): CalendarEvent|null
  {
    $event = CalendarEvent::find($eventId);

    if ($event) return $event;

    return null;
  }

  public function getAlldones(): Collection
  {
    $events = CalendarEvent::where(['created_by' => $this->user->id, 'done' => true])->get();

    return $events;
  }

  public function getAllUndones(): Collection
  {
    $events = CalendarEvent::where(['created_by' => $this->user->id, 'done' => false])->get();

    return $events;
  }

  /** Get events in list.
   * by default it get event between current year : -1year <- current year -> +1year.
   *
   * @param bool $completList Get all events recorded.
   */
  public function getAll(bool $completList = false): Collection
  {
    $this->updateDoneState();

    $events = collect();

    if ($completList) {
      $results = CalendarEvent::all();

      $events = $results;
    } else {
      $now = now();
      $lastYear = $now->year(getdate()['year'] - 1);
      $startDate = $lastYear->toISOString();

      $endDate = now()->addYear();

      $events = CalendarEvent::query()
        ->whereDate('start', '>=', $startDate)
        ->whereDate('start', '<=', $endDate->toISOString())
        ->get();
    }

    return $events;
  }
}
