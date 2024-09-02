<?php

namespace App\Quest;

class QuestRouter extends QuestRoutes
{
  /**
   * Create a new class instance.
   * @param array<int, string> $routes An array of spawned class's. But class's listed
   * here are not visible by the ID-Tracker in console.
   */
  public function __construct(protected string $questRef, array $routes = [], protected array|string|null $middleware = null) {
    parent::__construct();

    $this->routes = array_merge($this->routes, $routes);
  }

  /**
   * Begin the quest by making their way.
   * Spawn a way.
   */
  public function spawn() {
    $quest = new Quest;

    $questResult = $quest->router(questId: $this->questRef, classes: $this->routes, parentMiddleware: $this->middleware);

    if (($questResult instanceof QuestReturnVoid) == false) return $questResult;
  }
}
