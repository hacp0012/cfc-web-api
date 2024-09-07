<?php

namespace App\mobile_v1\app\reactions;

use App\Models\Reaction;
use stdClass;

class ViewsHandler
{
  /**
   * Create a new class instance.
   */
  public function __construct() {}

  /** @return stdClass {id} */
  function add($model, string $modelId, string $viewer = null): stdClass
  {
    $return = new stdClass;

    $new = Reaction::create([
      'type'    => 'VIEW',
      'for'     => $model,
      'for_id'  => $modelId,
      'by'      => $viewer,
    ]);

    $return->id = $new->id;

    return $return;
  }

  /** Count all of a model
   * @param \App\Model $model
   * @return stdClass {count}
   */
  function countAllOf($model, string $id): stdClass
  {
    $object = new stdClass;

    $count = Reaction::where([
      'type' => 'VIEW',
      'for' => $model,
      'for_id' => $id,
    ])->count();

    $object->count = $count;

    return $object;
  }

  /** Count all of a person on a model
   * @return stdClass {count}
   */
  function countOf($model, string $modelId, string $viewerId): stdClass
  {
    $return = new stdClass;

    $count = Reaction::where([
      'type' => 'VIEW',
      'for' => $model,
      'for_id' => $modelId,
      'by' => $viewerId,
    ])->count();

    $return->count = $count;

    return $return;
  }
}
