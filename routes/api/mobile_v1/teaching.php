<?php

use App\Http\Middleware\SanctumCustomMiddleware;
use App\mobile_v1\app\teaching\TeachingPostHandler;
use App\Quest\QuestRouter;
use Illuminate\Support\Facades\Route;

Route::prefix('teaching')->group(function () {
  Route::middleware(SanctumCustomMiddleware::class)->any('quest/{quest_ref}', function (string $quest_ref) {
    $quest = new QuestRouter(
      questRef: $quest_ref,
      middleware: SanctumCustomMiddleware::class,
      routes: [TeachingPostHandler::class],
    );

    return $quest->spawn();
  });
});
