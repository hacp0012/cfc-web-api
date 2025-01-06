<?php

namespace App\mobile_v1\app\comment;

use App\mobile_v1\app\reactions\LikesHandler;
use App\mobile_v1\app\reactions\ViewsHandler;
use App\mobile_v1\classes\Constants;
use App\Models\Comment;
use App\Models\Communique;
use App\Models\Echos;
use App\Models\Enseignement;
use App\Models\Reaction;
use App\Models\User;
use Hacp0012\Quest\Attributs\QuestSpaw;
use Hacp0012\Quest\SpawMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use stdClass;

class CommentsHandler
{
  /**
   * Create a new class instance.
   */
  public function __construct() {}

  private int $contentStep = Constants::MAX_COMMENTS_PER_REQUEST;

  /** @return stdClass --> add */
  #[QuestSpaw(ref: 'add.DQrta4poHvDfJEeyX2VQ27IW1H1')]
  function addFromRequest(string $model, string $model_id, Request $request, string $comment, ?string $parent = null): stdClass
  {
    $return = new stdClass;
    $return->success = false;

    $correctModel = match ($model) {
      'ECHO' => Echos::class,
      'COM' => Communique::class,
      'TEACHING' => Enseignement::class,

      default => null,
    };

    $user = $request->user();

    if ($correctModel && $user) {
      $data = $this->add(model: $correctModel, modelId: $model_id, poster: $user->id, comment: $comment, parent: $parent);
      $data->success = true;

      return $data;
    } else return $return;
  }

  /**
   * Add new comment.
   *
   * @return stdClass {id}
   */
  function add(string $model, string $modelId, string $poster, string $comment, ?string $parent = null): stdClass
  {
    $return = new stdClass;

    $new = Comment::create([
      'user'      => $poster,
      'for'       => $model,
      'for_id'    => $modelId,
      'parent'    => $parent,
      'comment'   => $comment,
    ]);

    if ($new) $return->id = $new->id;

    return $return;
  }

  #[QuestSpaw(ref: 'edit.yRR26S9K4haLzej0rZLLlBwqj1M')]
  function edit(string $comment_id, string $comment): stdClass
  {
    $return = new stdClass;
    $return->success = false;

    $commentModel = Comment::find($comment_id);

    if ($commentModel) {
      $commentModel->comment = $comment;
      $commentModel->save();

      $return->success = true;
    }

    return $return;
  }

  /** @return stdClass {state:bool} */
  #[QuestSpaw(ref: 'remove.oua2TPqGoWhF8zkAjFrhPgT8dVG', method: SpawMethod::DELETE)]
  function remove(string $comment_id): stdClass
  {
    $return = new stdClass;

    $state = Comment::firstWhere('id', $comment_id)?->delete() ?? false;

    $return->success = $state;

    return $return;
  }

  /** Add view (read)
   * @return stdClass {state:bool}
   */
  function addRread(string $modelId, string $posterId): stdClass
  {
    $return = new stdClass;

    $viewHandler = new ViewsHandler;

    $viewId = $viewHandler->add(model: Comment::class, modelId: $modelId, viewer: $posterId);

    $return->state = $viewId->id ? true : false;

    return $return;
  }

  /** Get one comment. */
  function get() {}

  /** @return stdClass --> getAllOf */
  #[QuestSpaw(ref: 'get.EoJkzyMftwPf72SCUuNxv8IMiinUL9rKIOTi', method: SpawMethod::GET)]
  function getAllOfFromRequest(string $model, string $model_id, int $currentPosition = 0)
  {
    $return = new stdClass;
    $return->success = false;

    $correctModel = match ($model) {
      'ECHO' => Echos::class,
      'COM' => Communique::class,
      'TEACHING' => Enseignement::class,

      default => null,
    };

    if ($correctModel) {
      $data = $this->getAllOf(model: $correctModel, modelId: $model_id);
      $data->success = true;

      // Slice.
      $data->comments = $this->sliceContent($data->comments, $currentPosition);
      $data->currentPosition = count($data->comments) > 0 ? $currentPosition + 1 : $currentPosition;

      return $data;
    } else return $return;
  }

  private function sliceContent(array $data, int $currentStep = 0): array
  {
    $sliceds = array_slice($data, $this->contentStep * $currentStep, $this->contentStep);
    return $sliceds;
  }

  /** Get all of a model.
   * @return stdClass {comments:array}
   */
  function getAllOf(string $model, string $modelId): stdClass
  {
    $return = new stdClass;

    $comments = Comment::where([
      'for' => $model,
      'for_id' => $modelId,
      'parent' => null,
    ])->get();

    // $return->comments = $comments;
    $return->comments = $this->formatListOf($comments);

    return $return;
  }

  function getChildrenOf(string $commentId, int $indent): array
  {
    $comments = Comment::whereParent($commentId)->get();

    return $this->formatListOf($comments, indent: $indent + 1);
  }

  /**
   * Format the comments list to a mobile app format
   * make it idententable.
   *
   * ⚠️ Use with `getAllOff`
   */
  function formatListOf(Collection $comments, int $indent = 0): array
  {
    $collection = $comments;
    $user = request()->user();

    // $count = $collection->count();

    $formateds = [];
    foreach ($collection as $comment) {
      $model = new stdClass;

      $model->indent = $indent;
      $model->comment = $comment;
      $model->children = $this->getChildrenOf($comment['id'], $indent);

      $like = new LikesHandler;

      $reactions = $like->countAllOf(model: Comment::class, id: $comment->id);

      $poster = User::find($comment->user);

      $posterModel = ['fullname' => null, 'id' => null];

      if ($poster) {
        $posterModel['fullname'] = $poster->fullname;
        $posterModel['id'] = $poster->id;
        $posterModel['is_owner'] = $user?->id === $poster->id;
      }

      $model->poster = $posterModel;

      $model->likes = $reactions->count;

      $formateds[] = $model;
    }

    // for ($index = 0; $index < $count; $index++) {
    //   $copyOfForamateds = $formateds;
    //   $last = array_pop($copyOfForamateds);
    //   $formateds = $copyOfForamateds;

    //   $hasNoParent = true;
    //   foreach ($copyOfForamateds as $index => $comment) {
    //     if ($comment->comment->id == $last->comment->parent) {
    //       $last->indent = $comment->comment->indent + 1;

    //       $f = array_slice($formateds, $index + 1);
    //       $b = array_slice($formateds, 0, $index + 1);

    //       $formateds = array_merge($f, [$last], $b);

    //       $hasNoParent = false;
    //       break;
    //     }
    //   }

    //   if ($hasNoParent) $formateds[] = $last;
    // }

    return $formateds;
  }

  /** Count all of model.
   * @return stdClass {count:int}
   */
  function countAllOf(string $model, string $modelId): stdClass
  {
    $return = new stdClass;

    $count = Comment::where([
      'for' => $model,
      'for_id' => $modelId,
    ])->count();

    $return->count = $count;

    return $return;
  }

  /** Count all of person.
   * @return stdClass {count:int}
   */
  function countOf(string $model, string $modelId, string $posterId)
  {
    $return = new stdClass;

    $count = Reaction::where([
      'for'     => $model,
      'for_id'  => $modelId,
      'user'    => $posterId,
    ])->count();

    $return->count = $count;

    return $return;
  }

  /** Get all of a person. */
  function getOf() {}

  /** @return stdClass {success:bool, count:int} */
  #[QuestSpaw(ref: 'like.reaction.gfsUuXfSdNUDdUruwSGDjbm2xQf')]
  function like(string $commentId, string $reactorId): stdClass
  {
    $return = new stdClass;
    $return->success = false;

    $like = new LikesHandler;

    $state = $like->toggle(Comment::class, $commentId, $reactorId);

    if ($state->state) {
      $count = $like->countAllOf(Comment::class, $commentId);

      $return->count = $count->count;
      $return->success = true;
    }

    return $return;
  }
}
