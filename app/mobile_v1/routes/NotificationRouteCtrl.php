<?php

namespace App\mobile_v1\routes;

use App\mobile_v1\handlers\NotificationHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class NotificationRouteCtrl
{
  public function requestHandler(Request $request)
  {
    $function = $request->string('f', '---');

    switch ($function) {
      case 'download':
        return $this->unreads($request);
      case 'mark_all_as_read':
        $this->markAllAsRead($request);
      case 'delete_unreads':
        return $this->deleteReads($request);
      case 'mark_as_read':
        return $this->makrAsRead($request);
      case 'count':
        return $this->count($request);
      case 'get_one':
        return $this->getOne($request);
      case 'delete':
        return $this->delete($request);

      default:
        # code...
        break;
    }
    // return match ($function) {
    // 'download'          => $this->unreads($request),
    // 'mark_all_as_read'  => $this->markAllAsRead($request),
    // 'delete_unreads'    => $this->deleteReads($request),
    // 'count'             => $this->count($request),
    // 'get_one'           => $this->getOne($request),
    // 'mark_as_read'      => $this->makrAsRead($request),
    // 'delete'            => $this->delete($request),
    // default             => 'null',
    // };
  }

  // for un authenticateds requests.
  public function guestRequestHandler(Request $request)
  {
    $function = $request->string('f', '---');

    return match ($function) {
      //
    };
  }

  # -------------------------------------------------------------------------------- :
  function unreads(Request $request): Collection
  {
    $user = $request->user();

    $nofication = new NotificationHandler(userID: $user->id);

    $nofications = $nofication->getAllUnreads();

    return $nofications;
  }

  function markAllAsRead(Request $request): array
  {
    $user = $request->user();

    $nofication = new NotificationHandler(userID: $user->id);

    $count = $nofication->markAsReadAllGeted();

    return ['state' => 'OK', 'count' => $count];
  }

  function deleteReads(Request $request): array
  {
    $user = $request->user();

    $nofication = new NotificationHandler(userID: $user->id);

    $count = $nofication->deleteAllReads();

    return ['state' => 'OK', 'count' => $count];
  }

  function count(Request $request): array
  {
    $user = $request->user();

    $nofication = new NotificationHandler(userID: $user->id);

    $count = $nofication->countUnreads();

    return ['count' => $count];
  }

  function getOne(Request $request): array
  {
    $notificationId = $request->string('notification_id', '---');

    $nofication = NotificationHandler::get(notificationID: $notificationId);

    return ['state' => $nofication ? 'OK' : 'NO', 'notification' => $nofication];
  }

  function makrAsRead(Request $request): array
  {
    $notificationId = $request->string('notification_id', '---');

    $state = NotificationHandler::markAsRead(notificationID: $notificationId);

    return ['state' => $state ? 'MARKED' : 'FAILED'];
  }

  function delete(Request $request): array
  {
    $notificationId = $request->string('notification_id', '---');

    $state = NotificationHandler::delete(notificationID: $notificationId);

    return ['state' => $state ? 'DELETED' : 'FAILED'];
  }
}
