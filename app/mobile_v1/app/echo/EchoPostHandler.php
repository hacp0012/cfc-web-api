<?php

namespace App\mobile_v1\app\echo;

use App\mobile_v1\classes\FileHanderClass;
use App\Models\Echos;
use App\Quest\QuestSpaw;
use Illuminate\Http\UploadedFile;

class EchoPostHandler
{
  /**
   * Create a new class instance.
   */
  public function __construct() {}

  /** @return array<string,string> [state:POSTED|FAILED, id] */
  #[QuestSpaw(ref: 'tSUr7URWYyIaxa4nCN')]
  function create(string $title, string $echo): mixed
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
        'text'            => $echo,
      ];

      // return $data;
      $newCreatedPostId = Echos::create($data);

      return ['state' => 'POSTED', 'id' => $newCreatedPostId->id];
    }

    return ['state' => 'FAILED'];
  }

  /** @return array<string,string> [state:STORED|FAILED] */
  #[QuestSpaw(ref: 'jXHh0IbqrGJQe2XxkH', filePocket: 'picture')]
  function uploadHeadPicture(string $echo_id, UploadedFile $picture): array
  {
    // Get teaching.
    $teaching = Echos::find($echo_id);

    if ($teaching) {
      $validatedFile = FileHanderClass::validate(FileHanderClass::TYPE['IMAGE'], $picture);

      if ($validatedFile) {
        // $picturePid = null;

        $storeState = FileHanderClass::store(
          document: $validatedFile,
          type: FileHanderClass::TYPE['IMAGE'],
          owner: $echo_id,
          ownerGroup: 'ECHO',
          contentGroup: 'ATACHED_IMAGE',
          // public_id: $picturePid,
        );

        return ['state' => $storeState ? 'STORED' : 'FAILED'];
      }
    }

    return ['state' => 'FAILED'];
  }

  /** @return array<string,string> [state:STORED|FAILED] */
  #[QuestSpaw(ref: 'VJBZ1EEQMZGDxRSKNz', filePocket: 'audio')]
  function uploadAudio(string $echo_id, UploadedFile $audio)
  {
    // Get teaching.
    $teaching = Echos::find($echo_id);

    if ($teaching) {
      $validatedFile = FileHanderClass::validate(FileHanderClass::TYPE['AUDIO'], $audio);

      if ($validatedFile) {
        $audioPid = null;

        $storeState = FileHanderClass::store(
          document: $validatedFile,
          type: FileHanderClass::TYPE['AUDIO'],
          owner: $echo_id,
          ownerGroup: 'ECHO',
          contentGroup: 'AUDIO',
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
  #[QuestSpaw(ref: '8g9D22LLquKYePyDa9', filePocket: 'document')]
  function uploadDocument(string $echo_id, UploadedFile $document)
  {
    // Get teaching.
    $teaching = Echos::find($echo_id);

    if ($teaching) {
      $validatedFile = FileHanderClass::validate(FileHanderClass::TYPE['IMAGE'], $document);

      if ($validatedFile) {
        $documentPid = null;

        $storeState = FileHanderClass::store(
          document: $validatedFile,
          type: FileHanderClass::TYPE['DOCUMENT'],
          owner: $echo_id,
          ownerGroup: 'ECHO',
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
