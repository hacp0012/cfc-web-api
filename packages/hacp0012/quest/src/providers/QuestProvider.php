<?php

namespace Princ\Quest\providers;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;
use Princ\Quest\Commands\QuestGenerateId;
use Princ\Quest\Commands\QuestPublish;
use Princ\Quest\Commands\QuestTrackId;

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
