<?php

namespace App\mobile_v1\app\reactions;

use App\Models\Reaction;
use stdClass;

class LikesHandler
{
  /**
   * Create a new class instance.
   */
  public function __construct() {}

  /** @return stdClass {id} */
  function add(string $model, string $modelId, string $liker): stdClass
  {
    $return = new stdClass;

    $new = Reaction::create([
      'type'    => 'LIKE',
      'for'     => $model,
      'for_id'  => $modelId,
      'by'      => $liker,
    ]);

    $return->id = $new->id;

    return $return;
  }

  /** @return stdClass {state:bool} */
  function remove(string $model, string $modelId, string $liker): stdClass
  {
    $return = new stdClass;

    $state = Reaction::where([
      'type'    => 'LIKE',
      'for'     => $model,
      'for_id'  => $modelId,
      'by'      => $liker,
    ])->delete();

    $return->state = $state;

    return $return;
  }

  /** @return stdClass {state:bool} */
  function toggle(string $model, string $modelId, string $liker): stdClass
  {
    $return = new stdClass;

    // check if has.
    $has = Reaction::firstWhere([
      'type'    => 'LIKE',
      'for'     => $model,
      'for_id'  => $modelId,
      'by'      => $liker,
    ]);

    if ($has) {
      $state = $this->remove(model: $model, modelId: $modelId, liker: $liker);

      $return->state = $state->state;
    } else {
      $state = $this->add(model: $model, modelId: $modelId, liker: $liker);

      $return->state = $state->id ? true : false;
    }

    return $return;
  }

  /** Count all of a model
   * @param \App\Model $model
   * @return stdClass {count}
   */
  function countAllOf(string $model, string $id): stdClass
  {
    $object = new stdClass;

    $count = Reaction::where([
      'type' => 'LIKE',
      'for' => $model,
      'for_id' => $id,
    ])->count();

    $object->count = $count;

    return $object;
  }

  /** Count all of a person on a model
   * @return stdClass {count}
   */
  function countOf(string $model, string $modelId, string $LIKEerId): stdClass
  {
    $return = new stdClass;

    $count = Reaction::where([
      'type' => 'LIKE',
      'for' => $model,
      'for_id' => $modelId,
      'by' => $LIKEerId,
    ])->count();

    $return->count = $count;

    return $return;
  }
}
