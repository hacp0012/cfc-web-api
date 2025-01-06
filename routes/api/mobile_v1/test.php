<?php

use App\mobile_v1\app\search\SearchEngine;
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
use Illuminate\Support\Facades\Process;

Route::middleware('guest')->prefix('test')->group(function () {
  Route::get('command', function() {
    // dd(base_path());
    // Process::start("cd ". base_path() ." & php artisan queue:work --stop-when-empty");
    // exec("cd " . base_path() . " & php artisan queue:work --stop-when-empty");
  });

  // Route::any('quest/{quest_ref}', fn(string $quest_ref) => (new QuestRouter(questRef: $quest_ref))->spawn());
  Quest::spawn(routes: [
    QuestTest::class,
    // 'app'
  ])->middleware('guest');

  // Quest::spaw('demo', [SearchEngine::class, 'search-engine-571ca4f1-1e81-4934-a523-1721792a4660']);
  Quest::spaw('demo', [SearchEngine::class, 'search-next-0a1f5c1c-4e1d-42a7-b626-3985f4356ee8']);

  Route::get('', function () {
    return ["Aliquip amet exercitation incididunt incididunt adipisicing et mollit Lorem esse consectetur."];
  })->name('test');
  Route::get('data', [Loader::class, 'load']);

  Route::get('notify', function () {
    $user = User::first();
    // dd($user);

    // NotificationHandler::send(title: fake()->title(), body: fake()->sentence(9))->std(Wellcome::class)->to($user);
    NotificationHandler::send(title: fake()->name(), body: fake()->sentence(18))
      ->std(Teaching::class, '9cf2adaf-c44c-4d2d-b4a1-280584eefc1d')
      // ->flash(Wellcome::class)
      ->to($user);
    // $nots = NotificationHandler::deleteAllOf('9cd5c881-958e-4e63-9c7c-f9d71af1c019');
    // $nots = NotificationHandler::delete('0a583ec9-179f-4c41-8479-2bc1e222c83a');
    // dd($nots);
    // return $nots;
    // return (new NotificationHandler($user->id))->getAllUnreads();
  });

  Route::prefix('midd')->group(function () {

    // if ($me == 'dar') {
    Route::get('', function () {
      return "message ,,,";
    });
    // }
  });
});
