<?php

namespace App\Quest;

class QuestRouter extends QuestRoutes
{
  /**
   * Create a new class instance.
   * @param string $questRef Reference ID.
   *
   * @param array<int, string> $routes An array of spawned class's. But class's listed
   * here are not visible by the Ref-Tracker in console. The Class referenced here are private to this route.
   * If `$routes` is not empty, only the global routes `$routes` a accessible. The base routes quest are not quested.
   *
   * @param array|string|null $middleware It very recomanded to declare your middlewares here
   * wetherway at the top level `->middlware(_)`.
   */
  public function __construct(protected string $questRef, array $routes = [], protected array|string|null $middleware = null)
  {
    parent::__construct();

    $this->routes = array_merge($this->routes, $routes);

    // Make $routes private and ignore quest routes.
    if (count($routes) > 0) return;

    QuestRouter::createRouteFile();

    $this->routes = QuestRouter::routesList();
  }

  /**
   * Begin the quest by making their way.
   * Spawn a way.
   */
  public function spawn()
  {
    $quest = new Quest;

    $questResult = $quest->router(questId: $this->questRef, classes: $this->routes, parentMiddleware: $this->middleware);

    if (($questResult instanceof QuestReturnVoid) == false) return $questResult;
  }

  /** Create quest routes file in routes base dir. */
  static function createRouteFile(): void
  {
    $routeQuestFile = base_path('routes/quest.php');

    if (is_file($routeQuestFile) == false) file_put_contents(
      $routeQuestFile,
      "<?php\n\n" .
        "return [\n" .
        " // Spawed classes names here ...\n" .
        "];\n\n",
    );
  }

  /** Get quest routes list. */
  static function routesList(): array
  {
    $routes = new QuestRoutes;

    $routesList = $routes->routes;

    $routeQuestFile = base_path('routes/quest.php');

    if (is_file($routeQuestFile)) {
      $questRoutes = fn(): array => include $routeQuestFile;

      $routesList = array_merge($routes->routes, $questRoutes());
    }

    return $routesList;
  }
}
