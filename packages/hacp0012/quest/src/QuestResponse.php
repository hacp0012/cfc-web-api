<?php

namespace Hacp0012\Quest;

/**
 * Manage response when call a normal method (function) tha has
 * normal response type that can be handled by other methods (functions).
 */
class QuestResponse
{
  const GLOBAL_REF_NAME = 'GLOBAL_REF_NAME';

  /**
   * Set response for json response data type.
   *
   *```php
   [
      'ref' => $ref,
      'data_name' => $dataName,
      'params' => $model,
    ]
   *```

   * @param array<mixed,mixed> $model
   *
   */
  public static function setForJson(string $ref, array $model = [], string $dataName = 'data'): void
  {
    $global = [
      'ref' => $ref,
      'data_name' => $dataName,
      'params' => $model,
    ];

    $GLOBALS[QuestResponse::GLOBAL_REF_NAME] = $global;
  }

  /** Check is `ref` is setted. */
  public function hasSetted(string $ref): bool
  {
    if (isset($GLOBALS[QuestResponse::GLOBAL_REF_NAME]) && strcmp($GLOBALS[QuestResponse::GLOBAL_REF_NAME]['ref'], $ref) == 0) {
      return true;
    }

    return false;
  }

  /** Set and get data stored to a `ref` key settd by `setForJson`.
   * If no ref has setted, return value will be the `response` data.
   */
  public function setAdnGetIt(string $ref, mixed $response): mixed
  {
    if ($this->hasSetted(ref: $ref)) {
      $dataName = $GLOBALS[QuestResponse::GLOBAL_REF_NAME]['data_name'];
      $params = $GLOBALS[QuestResponse::GLOBAL_REF_NAME]['params'];

      $params[$dataName] = $response;

      return $params;
    }

    return $response;
  }
}
