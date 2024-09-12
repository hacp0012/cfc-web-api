<?php

use App\mobile_v1\handlers\NotificationHandler;
use App\mobile_v1\routes\Loader;
use App\Models\User;
use App\Notifications\Teaching;
use App\Notifications\Wellcome;
use App\quest\demo\QuestTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Hacp0012\Quest\Quest;

Route::middleware('guest')->prefix('test')->group(function () {
  // Route::any('quest/{quest_ref}', fn(string $quest_ref) => (new QuestRouter(questRef: $quest_ref))->spawn());
  Quest::spawn(routes: [QuestTest::class])->middleware('guest');

  Route::get('', function () {
    return ["Aliquip amet exercitation incididunt incididunt adipisicing et mollit Lorem esse consectetur."];
  })->name('test');
  Route::get('data', [Loader::class, 'load']);

  Route::get('notify', function () {
    $user = User::first();
    // dd($user);

    // NotificationHandler::send(title: fake()->title(), body: fake()->sentence(9))->std(Wellcome::class)->to($user);
    NotificationHandler::send(title: fake()->name(), body: fake()->sentence(18))
      // ->std(Teaching::class, '5076ff2e-f192-4504-8fa7-da16a5c83df0')
      ->flash(Wellcome::class)
      ->to($user);
    // $nots = NotificationHandler::deleteAllOf('9cd5c881-958e-4e63-9c7c-f9d71af1c019');
    // $nots = NotificationHandler::delete('0a583ec9-179f-4c41-8479-2bc1e222c83a');
    // dd($nots);
    // return $nots;
    // return (new NotificationHandler($user->id))->getAllUnreads();
  });

  Route::prefix('midd')->group(function() {

    // if ($me == 'dar') {
      Route::get('', function() {
        return "message ,,,";
      });
    // }
  });
});
