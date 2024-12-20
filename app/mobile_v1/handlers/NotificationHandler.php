<?php

namespace App\mobile_v1\handlers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InnerNotificationHandler\Group;
use stdClass;

/**
 *
 * NotificationHandler::send()->wake(className::class)->to($user);
 */
class NotificationHandler
{
  /**
   * Create a new class instance.
   */
  public function __construct(private string $userID) {}

  /** Send a new notification. */
  public static function send(string $title, string $body, string $picture = null, string $logo = null): Group
  {
    $firstData = [
      'title' => $title,
      'body'  => $body,
      'picture' => $picture,
      'logo'  => $logo,
    ];

    return new Group(data: $firstData);
  }

  /** Get all user unreads notifications. */
  public function getAllUnreads(): Collection
  {
    // Get user.
    /** @var User|null */
    $user = User::find($this->userID);

    // Fetch all notifications for this user.
    $unReads = $user->unreadNotifications()->get();

    // $notifications = collect();

    // foreach ($unReads as $notification) $notifications->add($notification);

    // return $notifications;
    return $unReads;
  }

  /** Mark all reads as read, */
  public function markAsReadAllGeted(): int
  {
    // Get user.
    /** @var User|null */
    $user = User::find($this->userID);

    // Start mark all user receiveds notifications as Reads.
    $unReads = $user->unreadNotifications()->update(['read_at' => now()]);

    // Mark as reads.
    // $notification->markAsRead();
    return $unReads;
  }

  /** Delete all readed notifications. */
  public function deleteAllReads(): int
  {
    /** @var User|null */
    $user = User::find($this->userID);

    $reads = $user->readNotifications();

    return $reads->delete();
    // foreach ($reads as $notification) {
    //   if ($notification->read_at != null) $notification->delete();
    // }
  }

  /** Clean all notifications of a user. */
  public function deleteAll(): int
  {
    /** @var User|null */
    $user = User::find($this->userID);

    $reads = $user->notifications();

    return $reads->delete();
  }

  /** Count if user has new notification. */
  public function countUnreads(): int
  {
    $count = Notification::where(['notifiable_id' => $this->userID, 'read_at' => null])->count();
    return $count;
  }

  /** Get a single specific notification. */
  public static function get(string $notificationID): Notification|null
  {
    $notification = Notification::firstWhere('id', '=', $notificationID);

    if ($notification) return $notification;

    return null;
  }

  /** Mark a specific notification as Read. */
  public static function markAsRead(string $notificationID): bool
  {
    $notification = Notification::firstWhere('id', '=', $notificationID);

    if ($notification) {
      $notification->read_at = now();
      return $notification->save();
    }

    return false;
  }

  /** Delete a specific notitions. */
  public static function delete(string $notificationID): bool
  {
    $notification = Notification::firstWhere('id', '=', $notificationID);

    if ($notification) {
      return $notification->delete();
    }

    return false;
  }
}

# CLASSES --------------------------------------------------------------------------- :
namespace InnerNotificationHandler;

use App\Models\User;
use ReflectionClass;

class Actions
{
  /**
   * @param string $notifiableClass Ex. `className::class`
   * @param array $notification data contents.
   */
  public function __construct(private string $notifiableClass, private array $notificationData)
  {
    $ref = new ReflectionClass($notifiableClass);
    try {
      $this->notifiableClassInstance = $ref->newInstanceArgs([$notificationData]);
    } catch (\Exception $e) {
      $this->notifiableClassInstance = null;
    }
  }

  /** Notification instance or Null */
  private $notifiableClassInstance = null;

  /** Send notification to a single user. */
  public function to(User $user): void
  {
    if ($this->notifiableClassInstance == null) return;

    $user->notify($this->notifiableClassInstance);
  }

  /** Send notification to all provideds users.
   * @param array<int, User> $users */
  public function toMany(array $users): void
  {
    if ($this->notifiableClassInstance == null) return;

    foreach ($users as $user) $user->notify($this->notifiableClassInstance);
  }

  /** Send notification to users records in users table. */
  public function toAll()
  {
    if ($this->notifiableClassInstance == null) return;

    // Get all users.
    $users = User::all();

    foreach ($users as $user) $user->notify($this->notifiableClassInstance);
  }
}

class Group
{
  public function __construct(private array $data)
  {
    $this->configs['id']            = random_int(99, 900000000);
    $this->configs['notification']  = $data;
  }

  private array $configs = [
    'id'            => 0,
    'group'         => '',
    'type'          => null,
    'max_visible'   => 1,
    //'shedules'      => [],
    'notification'  => null,
    'subject_id'    => null,
  ];

  public function wake(string $class, string $subjectId, int $maxVisible = 1, array $shedules = []): Actions
  {
    $this->configs['type']          = 'WAKE';
    $this->configs['group']         = null;
    $this->configs['shedules']      = $shedules;
    $this->configs['max_visible']   = $maxVisible;
    $this->configs['subject_id']    = $subjectId;

    return new Actions(notifiableClass: $class, notificationData: $this->configs);
  }

  /** @return array[] */
  static function wakeDuration(int $weekDay = null, int $hour, int $minute, int $second = null): array
  {
    return [
      'week_day'  => $weekDay,
      'hour'      => $hour,
      'minute'    => $minute,
      'second'    => $second,
    ];
  }

  public function std(string $class, string $subjectId = null): Actions
  {
    $this->configs['type']         = 'STD';
    $this->configs['subject_id']   = $subjectId;

    return new Actions(notifiableClass: $class, notificationData: $this->configs);
  }

  public function flash(string $class, string $subjectId = null): Actions
  {
    $this->configs['type']         = 'FLASH';
    $this->configs['subject_id']   = $subjectId;

    return new Actions(notifiableClass: $class, notificationData: $this->configs);
  }
}
