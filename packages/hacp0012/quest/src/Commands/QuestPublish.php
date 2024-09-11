<?php

namespace Princ\Quest\Commands;

use Illuminate\Console\Command;
use Princ\Quest\QuestRouter;

class QuestPublish extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'quest:publish';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Publish quest route file in the routes base folder.';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    QuestRouter::createRouteFile();
  }
}
