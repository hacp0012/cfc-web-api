<?php

namespace App\mobile_v1\app\teaching;

use App\Jobs\SendNotificationsToAllUsers;
use App\mobile_v1\classes\FileHanderClass;
use App\mobile_v1\handlers\NotificationHandler;
use App\Models\Enseignement;
use App\Notifications\Teaching;
use Hacp0012\Quest\Attributs\QuestSpaw;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Process;

class TeachingPostHandler
{
  /**
   * Create a new class instance.
   */
  public function __construct() {}

  /** @return array<string,string> [state:POSTED|FAILED, id] */
  #[QuestSpaw(ref: '6P25iKiAj3KlIXXkrs')]
  function create(string $title, string $teaching, string $date = null, string $verse = null, string $predicator = null): mixed
  {
    // User :
    $user = request()->user();

    $formatedDate = null;
    if ($date) {
      $carbon = new Carbon(str_replace('/', '-', $date));
      $formatedDate = $carbon->toISOString();
    }

    // Get user role.
    $userRolw = $user->role;
    $roleLevel = isset($userRolw['level']) ? $userRolw['level'] : null;
    $levelId = match ($userRolw['level']) {
      'pool'      => $user->pool,
      'com_loc'   => $user->com_loc,
      'noyau_af'  => $user->noyau_af,
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
        'text'            => $teaching,
        'date'            => $formatedDate,
        'verse'           => $verse,
        'predicator'      => $predicator,
      ];

      // return $data;
      $newCreatedPostId = Enseignement::create($data);

      $this->notify(
        userId: $user->id,
        subjetId: $newCreatedPostId->id,
        title: $title,
        message: "[$verse] " . $predicator ?? '' . " : $teaching",
        picture: null,
      );

      return ['state' => 'POSTED', 'id' => $newCreatedPostId->id];
    }

    return ['state' => 'FAILED'];
  }

  private function notify(string $userId, string $subjetId, string $title, string $message, ?string $picture): void
  {
    SendNotificationsToAllUsers::dispatch(Teaching::class, $userId, $subjetId, $title, $message, $picture);
    Process::path(base_path())->start("php artisan queue:work --stop-when-empty");

    // $notificationHandler = new NotificationHandler($userId);

    // $group = $notificationHandler->send(title: $title, body: $message, picture: $picture);
    // $action = $group->std(Teaching::class, $subjetId);
    // $action->toAll();
  }

  /** @return array<string,string> [state:STORED|FAILED] */
  #[QuestSpaw(ref: 'hYEVGEpbMC1K40FBcb', filePocket: 'picture')]
  function uploadHeadPicture(string $teaching_id, UploadedFile $picture): array
  {
    // Get teaching.
    $teaching = Enseignement::find($teaching_id);

    if ($teaching) {
      $validatedFile = FileHanderClass::validate(FileHanderClass::TYPE['IMAGE'], $picture);

      if ($validatedFile) {
        $picturePid = null;

        $storeState = FileHanderClass::store(
          document: $validatedFile,
          type: FileHanderClass::TYPE['IMAGE'],
          owner: $teaching_id,
          ownerGroup: 'TEACHING',
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
  #[QuestSpaw(ref: 'IUWI1vWLpVmeAzCmSR', filePocket: 'audio')]
  function uploadAudio(string $teaching_id, UploadedFile $audio)
  {
    // Get teaching.
    $teaching = Enseignement::find($teaching_id);

    if ($teaching) {
      $validatedFile = FileHanderClass::validate(FileHanderClass::TYPE['AUDIO'], $audio);

      if ($validatedFile) {
        $audioPid = null;

        $storeState = FileHanderClass::store(
          document: $validatedFile,
          type: FileHanderClass::TYPE['AUDIO'],
          owner: $teaching_id,
          ownerGroup: 'TEACHING',
          contentGroup: 'AUDIO_TEACHING',
          public_id: $audioPid,
        );

        if ($storeState) {
          $teaching->audio = $audioPid;

          $state = $teaching->save();

          return ['state' => $state ? 'STORED' : 'FAILED'];
        }
      }
    }

    return ['state' => 'FAILED'];
  }

  /** @return array<string,string> [state:STORED|FAILED] */
  #[QuestSpaw(ref: 'hoXiIFCRIzaMiLCd36', filePocket: 'document')]
  function uploadDocument(string $teaching_id, UploadedFile $document)
  {
    // Get teaching.
    $teaching = Enseignement::find($teaching_id);

    if ($teaching) {
      $validatedFile = FileHanderClass::validate(FileHanderClass::TYPE['DOCUMENT'], $document);

      if ($validatedFile) {
        $documentPid = null;

        $storeState = FileHanderClass::store(
          document: $validatedFile,
          type: FileHanderClass::TYPE['DOCUMENT'],
          owner: $teaching_id,
          ownerGroup: 'TEACHING',
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
