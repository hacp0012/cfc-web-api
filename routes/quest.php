<?php

use App\mobile_v1\app\com\ComEditHandler;
use App\mobile_v1\app\com\ComPostHandler;
use App\mobile_v1\app\echo\EchoEditHandler;
use App\mobile_v1\app\echo\EchoPostHandler;
use App\mobile_v1\app\teaching\TeachingEditHandler;
use App\mobile_v1\app\teaching\TeachingPostHandler;
use App\Quest\demo\QuestTest;

return [
  QuestTest::class,

  // --------------------------------------- :
  EchoPostHandler::class,
  EchoEditHandler::class,

  ComPostHandler::class,
  ComEditHandler::class,

  TeachingPostHandler::class,
  TeachingEditHandler::class,
];
