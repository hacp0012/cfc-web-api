<?php

namespace App\mobile_v1\app\comment;

use App\mobile_v1\app\reactions\ViewsHandler;
use App\Models\Comment;
use stdClass;

class CommentsHandler
{
  /**
   * Create a new class instance.
   */
  public function __construct() {}

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

  /** @return stdClass {state:bool} */
  function remove(string $commentId): stdClass
  {
    $return = new stdClass;

    $state = Comment::firstWhere('id', $commentId)?->delete() ?? false;

    $return->state = $state;

    return $return;
  }

  /** Add view (read)
   * @return stdClass {state:bool}
   */
  function addAead(string $model, string $modelId, string $posterId): stdClass
  {
    $return = new stdClass;

    $viewHandler = new ViewsHandler;

    $viewId = $viewHandler->add(model: $model, modelId: $modelId, viewer: $posterId);

    $return->state = $viewId->id ? true : false;

    return $return;
  }

  /** Get one comment. */
  function get() {}

  /** Get all of a model.
   * @return stdClass {comments:array}
   */
  function getAllOf(string $model, string $modelId): stdClass
  {
    $return = new stdClass;

    $comments = Comment::where([
      'for' => $model,
      'for_id' => $modelId,
    ])->get();

    $return->comments = $comments;

    return $return;
  }

  /**
   * Format the comments list to a mobile app format
   * make it idententable.
   *
   * ⚠️ Use with `getAllOff`
   */
  function formatListOf(array $comments): array
  {
    $collection = collect($comments);

    $count = $collection->count();

    $formateds = [];
    foreach ($collection as $comment) {
      $model = new stdClass;

      $model->indent = 0;
      $model->comment = $comment;

      $formateds[] = $model;
    }

    for ($index = 0; $index < $count; $index++) {
      $copyOfForamateds = $formateds;
      $last = array_pop($copyOfForamateds);
      $formateds = $copyOfForamateds;

      $hasNoParent = true;
      foreach ($copyOfForamateds as $index => $comment) {
        if ($comment->comment->id == $last->comment->parent) {
          $last->indent = $comment->comment->indent + 1;

          $f = array_slice($formateds, $index + 1);
          $b = array_slice($formateds, 0, $index + 1);

          $formateds = array_merge($f, [$last], $b);

          $hasNoParent = false;
          break;
        }
      }

      if ($hasNoParent) $formateds[] = $last;
    }

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

  /** Count all of person. */
  function countOf() {}

  /** Get all of a person. */
  function getOf() {}
}
