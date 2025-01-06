<?php

use Hacp0012\Quest\SpawMethod;

return [
  /** The default Request Method. */
  'method' => SpawMethod::POST,

  // Quest track base routes.
  'base_routes' => [
    base_path('/routes/api.php'),
    base_path('/routes/web.php'),
  ],
];
