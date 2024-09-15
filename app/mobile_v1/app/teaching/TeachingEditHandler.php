<?php

namespace App\mobile_v1\app\teaching;

use App\mobile_v1\app\comment\CommentsHandler;
use App\mobile_v1\app\reactions\LikesHandler;
use App\mobile_v1\app\reactions\ViewsHandler;
use App\mobile_v1\classes\FileHanderClass;
use App\Models\Enseignement;
use App\Models\File;
use Hacp0012\Quest\Attributs\QuestSpaw;
use Hacp0012\Quest\QuestSpawMethod;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use stdClass;

class TeachingEditHandler
{
  /**
   * Create a new class instance.
   */
  public function __construct() {}

  #[QuestSpaw(ref: 'edit.get.teach.za40Hx5A0r1UbT0pZs', method: QuestSpawMethod::GET)]
  function get(string $teach_id): stdClass
  {
    $return = new stdClass;

    $state = false;

    // Get com :
    /** @var \App\Models\Enseignement|null */
    $teach = Enseignement::find($teach_id);

    if ($teach) {
      $teach = $teach->toArray();

      // Comments :
      $comments = (new CommentsHandler)->countAllOf(Enseignement::class, $teach_id);
      $return->comments = $comments->count;

      // Get document name:
      $documentFile = FileHanderClass::getByPublicId($teach['document'] ?? '---');

      $teach['document'] = ['name' => null, 'pid' => null];

      if ($documentFile) {
        $comDocumentData = [
          'name' => $documentFile->original_name . '.' . $documentFile->ext,
          'pid' => $documentFile->pid,
        ];

        $teach['document'] = $comDocumentData;
      }

      // Com :
      $return->teach = $teach;

      $state = true;
    }

    // State :
    $return->success = $state;

    return $return;
  }

  #[QuestSpaw(ref: 'edit.getlist.RrOWXRfKOjauvSpc7y', method: QuestSpawMethod::GET)]
  function getist(): array
  {
    // User :
    $user = request()->user();

    // Coms :
    /** @var array<App\Model\Communique> */
    $teachings = Enseignement::withTrashed()->where('published_by', $user->id)->get();
    // $teachings = Communique::all();

    $list = [];
    foreach ($teachings as $teaching) {
      $list[] = [
        'teaching' => [
          'id'          => $teaching->id,
          'title'       => $teaching->title,
          'verse'       => $teaching->verse,
          'date'        => $teaching->date,
          'predicator'  => $teaching->predicator,
          'deleted_at'  => $teaching->deleted_at,
          'created_at'  => $teaching->created_at,
          'updated_at'  => $teaching->updated_at,
        ],
        'reactions' => [
          'comments'  => (new CommentsHandler)->countAllOf(Enseignement::class, $teaching->id)->count,
          'likes'     => (new LikesHandler)->countAllOf(Enseignement::class, $teaching->id)->count,
          'views'     => (new ViewsHandler)->countAllOf(Enseignement::class, $teaching->id)->count,
        ],
      ];
    }

    return array_reverse($list);
  }

  /** @return stdClass {state:DELETED|DAILED} */
  #[QuestSpaw(ref: 'edit.delete.jq0zdulQMkM4PQ84Fb')]
  function destroy(string $id): stdClass
  {
    $return = new stdClass;

    $state = Enseignement::withTrashed()->find($id)->forceDelete();

    $return->state = $state ? 'DELETED' : "FAILED";

    return $return;
  }

  /** @return stdClass {state:FAILED|HIDDEN|VISIBLE} */
  #[QuestSpaw(ref: 'edit.toggle.visibility.NAhLlRZW3g3Fbh30dZ')]
  function toggleMask(string $id): stdClass
  {
    $return = new stdClass;

    $teaching = Enseignement::withTrashed()->find($id);

    $mainState = 'FAILED';

    if ($teaching) {
      if ($teaching->deleted_at) {
        // un delete.
        $teaching->deleted_at = null;
        $state = $teaching->save();
        if ($state) $mainState = 'VISIBLE';
      } else {
        // delete it.
        $state = $teaching->delete();
        if ($state) $mainState = 'HIDDEN';
      }
    }

    $return->state = $mainState;

    return $return;
  }

  // --> TEXT & STATE : -------------------------------------------------------->
  function updateState() {}

  #[QuestSpaw(ref: 'edit.update.text.emG2li4U0tQUzZLukA')]
  function updateText(string $teach_id, string $title, string $text, string $date = null, string $verse = null, string $predicator = null): stdClass
  {
    $return = new stdClass;

    $com = Enseignement::find($teach_id);

    $state = false;

    $formatedDate = null;
    if ($date) {
      $carbon = new Carbon(str_replace('/', '-', $date));
      $formatedDate = $carbon->toISOString();
    }

    if ($com) {
      $com->title       = $title;
      $com->text        = $text;
      $com->date        = $formatedDate;
      $com->verse       = $verse;
      $com->predicator  = $predicator;

      $state = $com->save();
    }

    $return->success = $state;

    return $return;
  }

  // --> HEAD PICTURE : -------------------------------------------------------->
  #[QuestSpaw(ref: 'edit.upload.picture.47XzYr5UnSQsx2hMPg', filePocket: 'picture')]
  function uploadHeadImage(string $teach_id, UploadedFile $picture): stdClass
  {
    $return = new stdClass;

    $state = false;
    $newPid = null;

    $validated = FileHanderClass::validate(type: FileHanderClass::TYPE['IMAGE'], uploadedFile: $picture);

    $teach = Enseignement::find($teach_id);

    if ($teach && $validated) {
      // get com picture
      $comPicture = $teach->picture;

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
          owner: $teach_id,
          ownerGroup: 'TEACHING',
          contentGroup: 'HEAD_IMAGE',
          public_id: $newPid,
        );
      }

      if ($state) {
        $teach->picture = $newPid;
        $teach->save();
      }
    }

    $return->success = $state;
    $return->pid = $newPid;

    return $return;
  }

  #[QuestSpaw(ref: 'edit.remove.picture.HU1vcOhjCDK4jtRORc', method: QuestSpawMethod::DELETE)]
  function removeHeadImage(string $pid): stdClass
  {
    $return = new stdClass;

    $document = FileHanderClass::getByPublicId($pid);

    // Update document.
    if ($document) {
      $owner = $document->owner;

      $docOwner = Enseignement::find($owner);
      if ($docOwner) {
        $docOwner->picture = null;
        $docOwner->save();
      }
    }

    $return->success = FileHanderClass::destroy(publicId: $pid);

    return $return;
  }

  // --> DOCUMENT :------------------------------------------------------------->
  #[QuestSpaw(ref: 'edit.update.document.lTCdWLZ9nb58DoIXUM', filePocket: 'document')]
  function uploadDocument(string $teach_id, UploadedFile $document)
  {
    $return = new stdClass;

    $state = false;
    $newPid = null;

    $validated = FileHanderClass::validate(type: FileHanderClass::TYPE['DOCUMENT'], uploadedFile: $document);

    $teach = Enseignement::find($teach_id);

    if ($teach && $validated) {
      // get com Document
      $comDocument = $teach->document;

      if ($comDocument) {
        // update.
        $file = File::firstWhere('pid',
          $comDocument
        );

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
          owner: $teach_id,
          ownerGroup: 'TEACHING',
          contentGroup: 'DOCUMENT',
          public_id: $newPid,
        );
      }

      if ($state) {
        $teach->document = $newPid;
        $teach->save();
      }
    }

    $return->state = $state ? 'UPDATED' : 'FAILED';
    $return->pid = $newPid;

    return $return;
  }

  #[QuestSpaw(ref: 'edit.remove.document.Ycnr98PntxRv9B3G0l', method: QuestSpawMethod::DELETE)]
  function removeDocument(string $pid): stdClass
  {
    $return = new stdClass;

    $document = FileHanderClass::getByPublicId($pid);

    // Update user
    if ($document) {
      $owner = $document->owner;

      $docOwner = Enseignement::find($owner);
      if ($docOwner) {
        $docOwner->document = null;

        $docOwner->save();
      }
    }

    $return->success = FileHanderClass::destroy(publicId: $pid);

    return $return;
  }

  // --> AUDIO :---------------------------------------------------------------->
  #[QuestSpaw(ref: 'edit.update.audio.Izum4tK9O4lhIrpgd8', filePocket: 'audio')]
  function uploadAudio(string $teach_id, UploadedFile $audio)
  {
    $return = new stdClass;

    $state = false;
    $newPid = null;

    $validated = FileHanderClass::validate(type: FileHanderClass::TYPE['AUDIO'], uploadedFile: $audio);
    $teach = Enseignement::find($teach_id);

    if ($teach && $validated) {
      // get com Document
      $echoAudio = $teach->audio;

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
          owner: $teach_id,
          ownerGroup: 'TEACHING',
          contentGroup: 'AUDIO',
          public_id: $newPid,
        );
      }

      if ($state) {
        $teach->audio = $newPid;
        $teach->save();
      }
    }

    $return->success = $state;
    $return->pid = $newPid;

    return $return;
  }

  #[QuestSpaw(ref: 'edit.remove.audio.04NveuJzo0HQxx29tZ', method: QuestSpawMethod::DELETE)]
  function removeAudio(string $pid): stdClass
  {
    $return = new stdClass;

    $document = FileHanderClass::getByPublicId($pid);

    // Update user
    if ($document) {
      $owner = $document->owner;

      $docOwner = Enseignement::find($owner);
      if ($docOwner) {
        $docOwner->audio = null;

        $docOwner->save();
      }
    }

    $return->success = FileHanderClass::destroy(publicId: $pid);

    return $return;
  }
}
