<?php

namespace App\mobile_v1\app\com;

use App\Jobs\SendNotificationsToAllUsers;
use App\mobile_v1\classes\FileHanderClass;
use App\mobile_v1\handlers\NotificationHandler;
use App\Models\Communique;
use App\Notifications\Communinication;
use Illuminate\Http\UploadedFile;
use Hacp0012\Quest\Attributs\QuestSpaw;
use Illuminate\Support\Facades\Process;

class ComPostHandler
{
  /**
   * Create a new class instance.
   */
  public function __construct() {}

  /** @return array<string,string> [state:POSTED|FAILED, id] */
  #[QuestSpaw(ref: 'meXRQbm0WQP6ZpAN5U')]
  function create(string $title, bool $status, string $com): mixed
  {
    // User :
    $user = request()->user();

    // Get user role.
    $userRolw = $user->role;
    $roleLevel = isset($userRolw['level']) ? $userRolw['level'] : null;
    $levelId = match ($userRolw['level']) {
      'pool'      => $user->pool,
      'com_loc'   => $user->com_loc,
      'noyau_ag'  => $user->noyau_af,
      default     => null,
    };

    // Creating publication.
    if ($roleLevel != null && $levelId != null) {
      $visibility = ['level' => $roleLevel, 'level_id' => $levelId];

      $data = [
        'published_by'    => $user->id,
        'state'           => 'PUBLIC',
        'visibility'      => $visibility,
        'title'           => $title,
        'text'            => $com,
        'status'          => $status ? 'INWAIT' : 'NONE'
      ];

      // return $data;
      $newCreatedPostId = Communique::create($data);

      // Send notification to all users.
      $this->notify(userId: $user->id, subjetId: $newCreatedPostId->id, title: $title, message: $com, picture: null);

      return ['state' => 'POSTED', 'id' => $newCreatedPostId->id];
    }

    return ['state' => 'FAILED'];
  }

  private function notify(string $userId, string $subjetId, string $title, string $message, ?string $picture): void
  {
    SendNotificationsToAllUsers::dispatch(Communinication::class, $userId, $subjetId, $title, $message, $picture);
    Process::path(app_path())->start("php artisan queue:work --stop-when-empty");

    // $notificationHandler = new NotificationHandler($userId);

    // $group = $notificationHandler->send(title: $title, body: $message, picture: $picture);
    // $action = $group->std(Communinication::class, $subjetId);
    // $action->toAll();
  }

  /** @return array<string,string> [state:STORED|FAILED] */
  #[QuestSpaw(ref: 'vNaLNWUX4Boh3PcpxO', filePocket: 'picture')]
  function uploadHeadPicture(string $com_id, UploadedFile $picture): array
  {
    // Get teaching.
    $teaching = Communique::find($com_id);

    if ($teaching) {
      $validatedFile = FileHanderClass::validate(FileHanderClass::TYPE['IMAGE'], $picture);

      if ($validatedFile) {
        $picturePid = null;

        $storeState = FileHanderClass::store(
          document: $validatedFile,
          type: FileHanderClass::TYPE['IMAGE'],
          owner: $com_id,
          ownerGroup: 'COMMUCATION',
          contentGroup: 'HEAD_IMAGE',
          public_id: $picturePid,
        );

        if ($storeState) {
          $teaching->picture = $picturePid;

          $state = $teaching->save();

          return ['state' => $state ? 'STORED' : 'FAILED'];
        }
      }
    }

    return ['state' => 'FAILED'];
  }

  /** @return array<string,string> [state:STORED|FAILED] */
  #[QuestSpaw(ref: 'rw0rEEOIJOYeuG4mUL', filePocket: 'document')]
  function uploadDocument(string $com_id, UploadedFile $document)
  {
    // Get teaching.
    $teaching = Communique::find($com_id);

    if ($teaching) {
      $validatedFile = FileHanderClass::validate(FileHanderClass::TYPE['DOCUMENT'], $document);

      if ($validatedFile) {
        $documentPid = null;

        $storeState = FileHanderClass::store(
          document: $validatedFile,
          type: FileHanderClass::TYPE['DOCUMENT'],
          owner: $com_id,
          ownerGroup: 'COMMUCATION',
          contentGroup: 'DOCUMENT',
          public_id: $documentPid,
        );

        if ($storeState) {
          $teaching->document = $documentPid;

          $state = $teaching->save();

          return ['state' => $state ? 'STORED' : 'FAILED'];
        }
      }
    }

    return ['state' => 'FAILED'];
  }
}
