<?php

namespace App\mobile_v1\app\com;

use App\mobile_v1\app\comment\CommentsHandler;
use App\mobile_v1\app\reactions\LikesHandler;
use App\mobile_v1\app\reactions\ViewsHandler;
use App\mobile_v1\classes\FileHanderClass;
use App\Models\Communique;
use App\Models\File;
use Illuminate\Http\UploadedFile;
use Hacp0012\Quest\Attributs\QuestSpaw;
use Hacp0012\Quest\QuestSpawMethod;
use stdClass;

class ComEditHandler
{
  /**
   * Create a new class instance.
   */
  public function __construct() {}

  /** @return stdClass {state:GETTED|FAILED, ?comments:int, ?com:App\Models\Communique } */
  #[QuestSpaw(ref: 'edit.get.KRT7TBTvs5yGP2rUsy', method: QuestSpawMethod::GET)]
  function get(string $com_id): stdClass
  {
    $return = new stdClass;

    $state = false;

    // Get com :
    /** @var \App\Models\Communique|null */
    $com = Communique::find($com_id);

    if ($com) {
      $com = $com->toArray();

      // Comments :
      $comments = (new CommentsHandler)->countAllOf(Communique::class, $com_id);
      $return->comments = $comments->count;

      // Get document name:
      $documentFile = FileHanderClass::getByPublicId($com['document'] ?? '---');

      $com['document'] = ['name' => null, 'pid' => null];

      if ($documentFile) {
        $comDocumentData = [
          'name' => $documentFile->original_name . '.' . $documentFile->ext,
          'pid' => $documentFile->pid,
        ];

        $com['document'] = $comDocumentData;
      }

      // Com :
      $return->com = $com;

      $state = true;
    }

    // State :
    $return->state = $state ? 'GETTED' : 'FAILED';

    return $return;
  }

  #[QuestSpaw(ref: 'edit.getlist.d8CmMR0YTSeFFF6mUe', method: QuestSpawMethod::GET)]
  function getist(): array
  {
    // User :
    $user = request()->user();

    // Coms :
    /** @var array<App\Model\Communique> */
    $coms = Communique::withTrashed()->where('published_by', $user->id)->get();
    // $coms = Communique::all();

    $list = [];
    foreach ($coms as $com) {
      $list[] = [
        'com' => [
          'id'          => $com->id,
          'title'       => $com->title,
          'status'      => $com->status,
          'deleted_at'  => $com->deleted_at,
          'created_at'  => $com->created_at,
          'updated_at'  => $com->updated_at,
        ],
        'reactions' => [
          'comments'  => (new CommentsHandler)->countAllOf(Communique::class, $com->id)->count,
          'likes'     => (new LikesHandler)->countAllOf(Communique::class, $com->id)->count,
          'views'     => (new ViewsHandler)->countAllOf(Communique::class, $com->id)->count,
        ],
      ];
    }

    return array_reverse($list);
  }

  /** @return stdClass {state:DELETED|DAILED} */
  #[QuestSpaw(ref: 'edit.delete.6Hwc5FQq029YMiVQkl')]
  function destroy(string $id): stdClass
  {
    $return = new stdClass;

    $state = Communique::withTrashed()->find($id)->forceDelete();

    $return->state = $state ? 'DELETED' : "FAILED";

    return $return;
  }

  /** @return stdClass {state:FAILED|HIDDEN|VISIBLE} */
  #[QuestSpaw(ref: 'edit.toggle.visibility.Lnnq0j9aiS4trdkArb')]
  function toggleMask(string $id): stdClass
  {
    $return = new stdClass;

    $com = Communique::withTrashed()->find($id);

    $mainState = 'FAILED';

    if ($com) {
      if ($com->deleted_at) {
        // un delete.
        $com->deleted_at = null;
        $state = $com->save();
        if ($state) $mainState = 'VISIBLE';
      } else {
        // delete it.
        $state = $com->delete();
        if ($state) $mainState = 'HIDDEN';
      }
    }

    $return->state = $mainState;

    return $return;
  }

  function updateState() {}

  /** @return stdClass {state:UPDATED|FAILED} */
  #[QuestSpaw(ref: 'com.update.text.628L7cLg1RGTvaxkgg')]
  function updateText(string $com_id, string $title, string $text, string $status): stdClass
  {
    $return = new stdClass;

    $com = Communique::find($com_id);

    $state = false;

    if ($com) {
      $com->title = $title;
      $com->text = $text;
      $com->status = $status;

      $state = $com->save();
    }

    $return->state = $state ? 'UPDATED' : 'FAILED';

    return $return;
  }

  // --> HEAD PICTURE : -------------------------------------------------------->
  /** @return stdClass {success:bool, pid:string} */
  #[QuestSpaw(ref: 'com.edit.update.picture.uCJPfanAYmhvQesGEG', filePocket: 'picture')]
  function uploadHeadImage(string $com_id, UploadedFile $picture): stdClass
  {
    $return = new stdClass;

    $state = false;
    $newPid = null;

    $validated = FileHanderClass::validate(type: FileHanderClass::TYPE['IMAGE'], uploadedFile: $picture);

    $com = Communique::find($com_id);

    if ($com && $validated) {
      // get com picture
      $comPicture = $com->picture;

      if ($comPicture) {
        // update.
        $file = File::firstWhere('pid', $comPicture);

        if ($file) {
          $state = FileHanderClass::replace(
            document: $validated,
            type: FileHanderClass::TYPE['IMAGE'],
            id: $file->id,
            new_public_id: $newPid,
          );
        }
      } else {
        // store new
        $state = FileHanderClass::store(
          document: $validated,
          type: FileHanderClass::TYPE['IMAGE'],
          owner: $com_id,
          ownerGroup: 'COMMUCATION',
          contentGroup: 'HEAD_IMAGE',
          public_id: $newPid,
        );
      }

      if ($state) {
        $com->picture = $newPid;
        $com->save();
      }
    }

    $return->success = $state;
    $return->pid = $newPid;

    return $return;
  }

  /** @return stdClass {success:bool} */
  #[QuestSpaw(ref: 'com.edit.relete.picture.0q69A65BL0f6LRRDDz', method: QuestSpawMethod::DELETE)]
  function removeHeadImage(string $pid): stdClass
  {
    $return = new stdClass;

    $document = FileHanderClass::getByPublicId($pid);

    // Update document.
    if ($document) {
      $owner = $document->owner;

      $docOwner = Communique::find($owner);
      if ($docOwner) {
        $docOwner->picture = null;
        $docOwner->save();
      }
    }

    $return->success = FileHanderClass::destroy(publicId: $pid);

    return $return;
  }

  // --> DOCUMENT :------------------------------------------------------------->
  /** @return stdClass {state:UPDATED|FAILED, pid:string} */
  #[QuestSpaw(ref: 'com.edit.update.doc.1q2IIeqday1xSwRHZl', filePocket: 'document')]
  function uploadDocument(string $com_id, UploadedFile $document)
  {
    $return = new stdClass;

    $state = false;
    $newPid = null;

    $validated = FileHanderClass::validate(type: FileHanderClass::TYPE['DOCUMENT'], uploadedFile: $document);

    $com = Communique::find($com_id);

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
          owner: $com_id,
          ownerGroup: 'COMMUCATION',
          contentGroup: 'DOCUMENT',
          public_id: $newPid,
        );
      }

      if ($state) {
        $com->document = $newPid;
        $com->save();
      }
    }

    $return->state = $state ? 'UPDATED' : 'FAILED';
    $return->pid = $newPid;

    return $return;
  }

  /** @return stdClass {success:bool} */
  #[QuestSpaw('com.edit.delete.doc.6LUlI6O4yAnKXI2M1J', method: QuestSpawMethod::DELETE)]
  function removeDocument(string $pid): stdClass
  {
    $return = new stdClass;

    $document = FileHanderClass::getByPublicId($pid);

    // Update user
    if ($document) {
      $owner = $document->owner;

      $docOwner = Communique::find($owner);
      if ($docOwner) {
        $docOwner->document = null;

        $docOwner->save();
      }
    }

    $return->success = FileHanderClass::destroy(publicId: $pid);

    return $return;
  }
}
