<?php

use App\quest\demo\QuestTest;
use App\Quest\QuestRouter;
use Illuminate\Support\Facades\Route;

Route::any(
  'quest/{quest_id}',
  fn(string $quest_id) => (new QuestRouter(questId: $quest_id, routes: [QuestTest::class]))->spawn(),
);
