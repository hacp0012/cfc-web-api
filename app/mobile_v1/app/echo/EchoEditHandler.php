<?php

namespace App\mobile_v1\app\echo;

use App\mobile_v1\app\comment\CommentsHandler;
use App\mobile_v1\app\reactions\LikesHandler;
use App\mobile_v1\app\reactions\ViewsHandler;
use App\mobile_v1\classes\FileHanderClass;
use App\Models\Echos;
use App\Models\File;
use Hacp0012\Quest\Attributs\QuestSpaw;
use Hacp0012\Quest\QuestSpawMethod;
use Illuminate\Http\UploadedFile;
use stdClass;

class EchoEditHandler
{
  /**
   * Create a new class instance.
   */
  public function __construct() {}

  /** @return stdClass {success:bool, echo:map, picture:array, comments:int} */
  #[QuestSpaw(ref: 'edit.get.UclrFc9HDV4oCtJhfq', method: QuestSpawMethod::GET)]
  function get(string $echo_id): stdClass
  {
    $return = new stdClass;

    $state = false;

    // Get com :
    /** @var \App\Models\Echos|null */
    $echo = Echos::find($echo_id);

    if ($echo) {
      $echo = $echo->toArray();

      // Comments :------------------------------------------
      $comments = (new CommentsHandler)->countAllOf(Echos::class, $echo_id);
      $return->comments = $comments->count;

      // Get document name:----------------------------------
      $documentFile = FileHanderClass::getByPublicId($echo['document'] ?? '---');

      $echo['document'] = ['name' => null, 'pid' => null];

      if ($documentFile) {
        $comDocumentData = [
          'name' => $documentFile->original_name . '.' . $documentFile->ext,
          'pid' => $documentFile->pid,
        ];

        $echo['document'] = $comDocumentData;
      }

      // Pictures :------------------------------------------
      $pictures = FileHanderClass::get(
        type: FileHanderClass::TYPE['IMAGE'],
        owner: $echo_id,
        ownerGroup: 'ECHO',
        contentGroup: 'ATACHED_IMAGE',
      );

      $picturesList = $pictures->map(fn($model, $key) => $model->pid);

      $return->pictures = $picturesList->toArray();

      // Echo :---------------------------------------------
      $return->echo = $echo;

      $state = true;
    }

    // State :
    $return->success = $state;

    return $return;
  }

  #[QuestSpaw(ref: 'edit.getlist.giH8YUKxPAVr38DBIg', method: QuestSpawMethod::GET)]
  function getist(): array
  {
    // User :
    $user = request()->user();

    // Coms :
    /** @var array<App\Model\Communique> */
    $echos = Echos::withTrashed()->where('published_by', $user->id)->get();
    // $echos = Communique::all();

    $list = [];
    foreach ($echos as $echo) {
      $list[] = [
        'echo' => [
          'id'          => $echo->id,
          'title'       => $echo->title,
          'deleted_at'  => $echo->deleted_at,
          'created_at'  => $echo->created_at,
          'updated_at'  => $echo->updated_at,
        ],
        'reactions' => [
          'comments'  => (new CommentsHandler)->countAllOf(Echos::class, $echo->id)->count,
          'likes'     => (new LikesHandler)->countAllOf(Echos::class, $echo->id)->count,
          'views'     => (new ViewsHandler)->countAllOf(Echos::class, $echo->id)->count,
        ],
      ];
    }

    return array_reverse($list);
  }

  /** @return stdClass {state:DELETED|DAILED} */
  #[QuestSpaw(ref: 'edit.delete.qpu8Grt0tu3L1eNGj6')]
  function destroy(string $id): stdClass
  {
    $return = new stdClass;

    $state = Echos::withTrashed()->find($id)->forceDelete();

    $return->state = $state ? 'DELETED' : "FAILED";

    return $return;
  }

  /** @return stdClass {state:FAILED|HIDDEN|VISIBLE} */
  #[QuestSpaw(ref: 'edit.toggle.visibility.cxIwDFNyA7Xm9uYrAn')]
  function toggleMask(string $id): stdClass
  {
    $return = new stdClass;

    $echo = Echos::withTrashed()->find($id);

    $mainState = 'FAILED';

    if ($echo) {
      if ($echo->deleted_at) {
        // un delete.
        $echo->deleted_at = null;
        $state = $echo->save();
        if ($state) $mainState = 'VISIBLE';
      } else {
        // delete it.
        $state = $echo->delete();
        if ($state) $mainState = 'HIDDEN';
      }
    }

    $return->state = $mainState;

    return $return;
  }

  function updateState() {}

  /** @return stdClass {success:bool} */
  #[QuestSpaw(ref: 'edit.update.text.HpCyO42ugbAa85Q1MG')]
  function updateText(string $echo_id, string $title, string $text): stdClass
  {
    $return = new stdClass;

    $com = Echos::find($echo_id);

    $state = false;

    if ($com) {
      $com->title = $title;
      $com->text = $text;

      $state = $com->save();
    }

    $return->success = $state;

    return $return;
  }

  // --> HEAD PICTURE : -------------------------------------------------------->
  #[QuestSpaw(ref: 'edit.add.picture.ORmFOLoAyTNnWJSNs3', filePocket: 'picture')]
  function uploadHeadImage(string $echo_id, UploadedFile $picture): stdClass
  {
    $return = new stdClass;

    $state = false;
    $newPid = null;

    $validated = FileHanderClass::validate(type: FileHanderClass::TYPE['IMAGE'], uploadedFile: $picture);

    // store new
    $state = FileHanderClass::store(
      document: $validated,
      type: FileHanderClass::TYPE['IMAGE'],
      owner: $echo_id,
      ownerGroup: 'ECHO',
      contentGroup: 'ATACHED_IMAGE',
      public_id: $newPid,
    );

    $return->success = $state;
    $return->pid = $newPid;

    return $return;
  }

  /** @return stdClass {success:bool} */
  #[QuestSpaw(ref: 'edit.remove.picture.Mg1CdY0SbHZ5h76aiC', method: QuestSpawMethod::DELETE)]
  function removeHeadImage(string $pid): stdClass
  {
    $return = new stdClass;

    $return->success = FileHanderClass::destroy(publicId: $pid);

    return $return;
  }

  // --> DOCUMENT :------------------------------------------------------------->
  #[QuestSpaw(ref: 'edit.update.document.64DUWiID5rKO8hHa8l', filePocket: 'document')]
  function uploadDocument(string $echo_id, UploadedFile $document)
  {
    $return = new stdClass;

    $state = false;
    $newPid = null;

    $validated = FileHanderClass::validate(type: FileHanderClass::TYPE['DOCUMENT'], uploadedFile: $document);

    $com = Echos::find($echo_id);

    if ($com && $validated) {
      // get com Document
      $comDocument = $com->document;

      if ($comDocument) {
        // update.
        $file = File::firstWhere('pid', $comDocument);

        if ($file) {
          $state = FileHanderClass::replace(
            document: $validated,
            type: FileHanderClass::TYPE['DOCUMENT'],
            id: $file->id,
            new_public_id: $newPid,
          );
        }
      } else {
        // store new
        $state = FileHanderClass::store(
          document: $validated,
          type: FileHanderClass::TYPE['DOCUMENT'],
          owner: $echo_id,
          ownerGroup: 'ECHO',
          contentGroup: 'DOCUMENT',
          public_id: $newPid,
        );
      }

      if ($state) {
        $com->document = $newPid;
        $com->save();
      }
    }

    $return->success = $state;
    $return->pid = $newPid;

    return $return;
  }

  /** @return stdClass {success:bool} */
  #[QuestSpaw(ref: 'edit.remove.document.43HXMZvScDOZKq1F0z', method: QuestSpawMethod::DELETE)]
  function removeDocument(string $pid): stdClass
  {
    $return = new stdClass;

    $document = FileHanderClass::getByPublicId($pid);

    // Update user
    if ($document) {
      $owner = $document->owner;

      $docOwner = Echos::find($owner);
      if ($docOwner) {
        $docOwner->document = null;

        $docOwner->save();
      }
    }

    $return->success = FileHanderClass::destroy(publicId: $pid);

    return $return;
  }

  // --> AUDIO :---------------------------------------------------------------->
  #[QuestSpaw(ref: 'edit.update.audio.jEZ3K79hxn2RXZ6MFx', filePocket: 'audio')]
  function uploadAudio(string $echo_id, UploadedFile $audio)
  {
    $return = new stdClass;

    $state = false;
    $newPid = null;

    $validated = FileHanderClass::validate(type: FileHanderClass::TYPE['AUDIO'], uploadedFile: $audio);
    $echo = Echos::find($echo_id);

    if ($echo && $validated) {
      // get com Document
      $echoAudio = $echo->audio;

      if ($echoAudio) {
        // update.
        $file = File::firstWhere('pid', $echoAudio);

        if ($file) {
          $state = FileHanderClass::replace(
            document: $validated,
            type: FileHanderClass::TYPE['AUDIO'],
            id: $file->id,
            new_public_id: $newPid,
          );
        }
      } else {
        // store new
        $state = FileHanderClass::store(
          document: $validated,
          type: FileHanderClass::TYPE['AUDIO'],
          owner: $echo_id,
          ownerGroup: 'ECHO',
          contentGroup: 'AUDIO',
          public_id: $newPid,
        );
      }

      if ($state) {
        $echo->audio = $newPid;
        $echo->save();
      }
    }

    $return->success = $state;
    $return->pid = $newPid;

    return $return;
  }

  #[QuestSpaw(ref: 'edit.remove.audio.nuKFP67aCAAokKalWy', method: QuestSpawMethod::DELETE)]
  function removeAudio(string $pid): stdClass
  {
    $return = new stdClass;

    $document = FileHanderClass::getByPublicId($pid);

    // Update user
    if ($document) {
      $owner = $document->owner;

      $docOwner = Echos::find($owner);
      if ($docOwner) {
        $docOwner->audio = null;

        $docOwner->save();
      }
    }

    $return->success = FileHanderClass::destroy(publicId: $pid);

    return $return;
  }
}
