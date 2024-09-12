<?php

namespace Hacp0012\Quest\providers;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;
use Hacp0012\Quest\Commands\QuestGenerateId;
use Hacp0012\Quest\Commands\QuestPublish;
use Hacp0012\Quest\Commands\QuestTrackId;

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
    AboutCommand::add('Quest', fn() => ['version' => '1.0.0', 'channel' => 'dev']);

    if ($this->app->runningInConsole()) {
      $this->commands([
        QuestGenerateId::class,
        QuestPublish::class,
        QuestTrackId::class,
      ]);
    }

    $this->publishes(
      groups: 'quest',
      paths: [
        __DIR__ . '/../publishables/quest.php' => config_path('quest.php'),
        __DIR__ . '/../publishables/quest_routes.php' => base_path('/routes/quest.php'),
      ],
    );
  }
}
