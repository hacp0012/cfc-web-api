<?php

namespace App\Providers;

use App\Console\Commands\QuestGenerateId;
use App\Console\Commands\QuestPublish;
use App\Console\Commands\QuestTrackId;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;

class QuestProvider extends ServiceProvider
{
  /**
   * Register services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap services.
   */
  public function boot(): void
  {
    AboutCommand::add('quest', fn () => ['version' => '1.0.0', 'channel' => 'stable']);

    if ($this->app->runningInConsole()) {
      $this->commands([
        QuestGenerateId::class,
        QuestPublish::class,
        QuestTrackId::class,
      ]);
    }

    $this->publishes([
      __DIR__ . '/../app/Quest/publishables/quest.php' => config_path('quest.php'),
      __DIR__ . '/../app/Quest/publishables/quest_routes.php' => base_path('/routes/quest.php'),
    ]);
  }
}
