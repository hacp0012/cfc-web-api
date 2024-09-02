<?php

namespace App\Quest;

use App\quest\demo\QuestTest;

/** Quest routes register. */
class QuestRoutes
{
  /**
   * Create a new class instance.
   */
  public function __construct() {}

  /**
   * Routes list.
   *
   * @var array<int, string> $routes An array of spawned class's
   */
  public array $routes = [QuestTest::class];
}
