<?php

namespace App\Quest;

use App\quest\QuestSpawClass;
use App\quest\QuestSpawMethod;
use Closure;
use Exception;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Reflection;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionUnionType;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use stdClass;

/** Quest core handler.
 *
 * The Quest core class.
 */
class Quest
{
  /**
   * Create a new Quest instance.
   */
  public function __construct() {}

  const authorizedAttributs         = ['QuestSpawClass', 'QuestSpaw'];
  const supportedSpawTypes          = ['bool', 'int', 'float', 'string', 'null', 'array', 'mixed'];
  const supportedFilesPocketTypes   = [UploadedFile::class, 'mixed'];
  const allowedMethodModifiers      = ['static', 'public'];

  // QUEST ROUTE -->-------------------------------------------------------------- :
  // Quest::spaw('ref', middlware: []);

  /**
   * Quest Router `QuesetRouter` short hand.
   *
   * @param string $uri
   * ⚠️ At any end of `uri` a `{quest_ref}` route parameter are append. Dont append it twice.
   *
   * @param array<int, string> $routes An array of spawned class's.
   *
   * __Routes precedence__ :
   * 1. Local routes : defined in spawed $routes parameter.
   * 2. Global Base routes : defined in your routes/quest.php.
   * 3. Defaults Global routes : default quest routes.
   */
  static function spawn(string $uri = 'quest', array $routes = []): RoutingRoute
  {
    # TRACKER :
    { # For console track ref -->:
      $vars = isset($GLOBALS[QuestConsole::GLOBAL_TEMP_LIST]) ? $GLOBALS[QuestConsole::GLOBAL_TEMP_LIST] : [];

      $GLOBALS[QuestConsole::GLOBAL_TEMP_LIST] = array_merge($routes, $vars);
    }

    // Bind routes to Route action closure {#fff, 11}
    $anonymClass = new class ($routes) {
      function __construct(protected array $routes) {}
    };

    $closure = function (string $quest_ref) {
      $questRouter = new QuestRouter(questRef: $quest_ref, routes: $this->{'routes'});

      return $questRouter->spawn();
    };

    $bindedClosure = Closure::bind($closure, $anonymClass, $anonymClass);

    # ROUTER :
    if (Str::endsWith($uri, '/')) $uri = Str::replaceEnd('/', '', $uri);

    $router = Route::any(uri: $uri . '/{quest_ref}', action: $bindedClosure);

    # END :
    return $router;
  }

  // METHODS  ROUTER-->----------------------------------------------------------- :
  function router(string $questId, array $classes): mixed
  {
    // loop in classes.
    foreach ($classes as $class) {
      try {
        $classReflexion = new ReflectionClass($class);
      } catch (\Exception $e) {
        throw new \Exception("The provided quest class '$class' not exist. " /* . $e->__toString() */);
      }

      // loop in methods.
      $methods = $classReflexion->getMethods();

      if (count($methods)) {
        foreach ($methods as $method) {

          // get attributs.
          $attributs = $method->getAttributes(QuestSpaw::class);

          // If not attribut found.
          if (count($attributs) == 0) continue;

          // get attribut instance.
          $attributInst = $attributs[0]->newInstance();

          // check if id's match.
          if ($this->controlQuestId(questId: $questId, attribut: $attributInst) == false) continue;

          // Contol middleware.
          if ($attributInst->middleware !== null) {
            if ($this->controlMiddleware($attributInst->middleware) == false) return new QuestReturnVoid;
          }

          // control : Request method | Method avalable.
          $this->controllQuestSpawSetting(spawedQuestAttribut: $attributs[0]);

          // control modifier [only public a authorized].
          $this->controlModifier(method: $method);

          // controller.
          $controlState = $this->controller(method: $method, attribut: $attributs[0]);

          // ? CALL THE METHOD ? {#00f, 1}
          if ($controlState) return $this->call(class: $classReflexion, method: $method);
        }
      }
    }

    return new QuestReturnVoid;
  }

  /**
   * @param array|string|null $attribut
   * @param array|string|null $middlewares
   */
  private function controlMiddleware($attributMiddleware): bool
  {
    $middlewares = Route::current()->middleware();

    if (is_array($middlewares)) {
      if (is_array($attributMiddleware)) {
        foreach ($attributMiddleware as $atMid) {
          if (in_array($atMid, $middlewares)) return true;
        }
      } elseif (is_string($attributMiddleware)) {
        if (in_array($attributMiddleware, $middlewares)) return true;
      } // null.
    } elseif (is_string($middlewares)) {
      if ($attributMiddleware == $middlewares) return true;
    } // null.

    return false;
  }

  /**
   * @param \ReflectionAttribute::newInstance $attribut
   */
  private function controlQuestId(string $questId, $attribut): bool
  {

    // Log::debug("--> $questId | " . $attribut->ref);
    if (strcmp($attribut->ref, $questId) == 0) {
      // * ID'S ARE MATCHED * //
      return true;
    } else {
      // // throw new \Exception("");
      return false;
    }
  }

  function intentionRequest(): ?array
  {
    $request = request();

    // Params :
    $params = (array) $request->input();

    // File :
    if ($request->files->count()) $params = array_merge($params, $request->files->all());

    return $params;
  }

  /** Control modifier [only public are authorized].
   *
   * @param \ReflectionMethod $method
   */
  private function controlModifier($method)
  {
    $modifier = implode(' ', Reflection::getModifierNames($method->getModifiers()));
    $methodName = $method->getName();

    if (str_contains(trim($modifier), 'public') == false && str_contains(trim($modifier), 'static') == false)
      throw new \Exception("Only static and public method are authorized for quest spaw at [$methodName]");
  }

  /**
   * @param \ReflectionMethod $method
   * @param \ReflectionAttribute $attribut
   */
  private function controller($method, $attribut): bool
  {
    // get attribut properties.
    /** @var \ReflectionAttribute|null */
    $foundedAttribut = null;
    $attributName = Arr::last(explode('\\', $attribut->getName()));

    if ($this->thisAttributExist($attributName)) {
      $foundedAttribut = $attribut;
    }

    if ($foundedAttribut !== null) {
      $this->spawTypeCheker($method, $attribut);

      $filePocketName = $attribut->getArguments();

      $this->compareParameters(
        $this->intentionRequest(),
        $method->getParameters(),
        isset($filePocketName['filePocket']) ? $filePocketName['filePocket'] : null,
        $attribut,
      );

      return true;
    }

    return false;
  }

  private function thisAttributExist(string $name): bool
  {
    return in_array($name, Quest::authorizedAttributs);
    // return Arr::exists(Quest::authorizedAttributs, $name);
  }

  /** @param \ReflectionAttribute $spawedQuestAttribut */
  private function controllQuestSpawSetting($spawedQuestAttribut)
  {
    // Control Http quest method.
    $questMethod = request()->method();
    $questState = match ($questMethod) {
      QuestSpawMethod::GET->name => true,
      QuestSpawMethod::POST->name => true,

      default => false,
    };

    if ($questState == false) throw new \Exception("The quest method '$questMethod' are not admit to this quest. Avallable quest method are GET and POST.");

    // REQUEST MOTHOD ------------ :
    $spawedQuestAttributInstance = $spawedQuestAttribut->newInstance();

    $providedMethod = $spawedQuestAttributInstance->method;

    if ($providedMethod->name != $questMethod) throw new \Exception(
      "The spaw method are not match. The expected method are " . $providedMethod->name . " and your provide " . $questMethod .
        ". Default is " . QuestSpawMethod::POST->name
    );
  }

  /** @param \ReflectionMethod $method
   * @param \ReflectionAttribute $attribut
   */
  private function spawTypeCheker($method, $attribut)
  {
    // get file pocket name.
    $spawedQuestAttributInstance = $attribut->newInstance();
    $filePocketName = $spawedQuestAttributInstance->filePocket;

    // method parameters.
    $params = $method->getParameters();
    foreach ($params as $param) {
      /** @var array<int, \ReflectionNamedType|null> */
      $types = [$param->getType()]; # can be null;

      if ($param->getType() instanceof ReflectionUnionType) $types = $param->getType()->getTypes();

      if ($param->getName() == $filePocketName) {
        $isIncorrectType = true;
        foreach ($types as $type) if (in_array($type?->{'getName'}() ?? 'mixed', Quest::supportedFilesPocketTypes)) $isIncorrectType = false;

        if ($isIncorrectType) {
          $paramName = $param->getName();
          throw new \Exception("The quest file pocket '$paramName' support only [" . implode(', ', Quest::supportedFilesPocketTypes) . "] as types.");
        }
      } else {
        foreach ($types as $type) {
          $isIncorrectType = true;
          if (in_array($type?->{'getName'}() ?? 'mixed', Quest::supportedSpawTypes)) $isIncorrectType = false;

          if ($isIncorrectType) throw new \Exception(
            "The spaw quest parameter has a unsupported Type '" . $type->{'getName'}() . "'. Expected [" . implode(', ', Quest::supportedSpawTypes) . "] as types.",
          );
        }
      }
    }
  }

  /** @param \ReflectionMethod $method
   * @param \ReflectionAttribute $attribut
   */
  private function intentionTypeChecker(array $intention, $method, $attribut): array
  {
    // get file pocket name.
    $attributParams = $attribut->getArguments();
    $filePocketName = isset($attributParams['filePocket']) ? $attributParams['filePocket'] : null;

    // method parameters.
    $casteds = [];
    $params = $method->getParameters();
    foreach ($params as $param) {
      /** @var array<int, \ReflectionNamedType|null> */
      $types = [$param->getType()]; # can be null;

      if ($param->getType() instanceof ReflectionUnionType) $types = $param->getType()->getTypes();

      $paramName = $this->getAliasName($param->getName(), $attribut);

      if ($filePocketName) $filePocketName = $this->getAliasName($filePocketName, $attribut);

      if ($paramName == $filePocketName) {
        $casteds[] = $this->intentionTypeCast(isset($intention[$filePocketName]) ? $intention[$filePocketName] : null, $types, $filePocketName);
      } else {
        $casteds[] = $this->intentionTypeCast(isset($intention[$paramName]) ? $intention[$paramName] : null, $types);
      }
    }

    return $casteds;
  }

  /** @param \ReflectionAttribute $attribut instance*/
  private function getAliasName(string $paramName, $attribut): string
  {
    $attributInst = $attribut->newInstance();

    $aliasList = $attributInst->alias;

    $alias = null;
    foreach ($aliasList as $_paramName => $aliasName) if ($_paramName == $paramName) {
      $alias = $aliasName;
      break;
    }

    if ($alias) return $alias;

    return $paramName;
  }

  /** @param mixed $methodPram
   * @param array<int, \ReflectionNamedType|null> $types
   */
  private function intentionTypeCast(mixed $value, $types, ?string $filePocket = null): mixed
  {
    $casted = null;

    if ($filePocket) {
      $request = request();
      if ($request->method() != QuestSpawMethod::POST->name) throw new \Exception(
        "The files pocket only support the quest method " . QuestSpawMethod::POST->name . ". You use " . $request->method()
      );

      if ($request->hasFile($filePocket))  $casted = $request->file($filePocket);

      else throw new \Exception("No file found for this pocket name '$filePocket'");
    } else {
      foreach ($types as $type) {
        $typeName = $type?->getName() ?? 'mixed';

        if ($type->allowsNull() && $value === null) $casted = null;
        else {
          $isOfThisType = match ($typeName) {
            'int' => is_numeric($value) ? (is_string($value) && str_contains($value, '.') ? false : true) : true,
            'float' => is_numeric($value),
            'string' => is_string($value),
            'bool' => true,
            'null' => true,
            'mixed' => true,
            'array' => true,
            default => false,
          };

          if ($isOfThisType) {
            $casted = match ($typeName) {
              'int' => intval($value),
              'float' => floatval($value),
              'bool' => boolval($value),
              'string' => $value,
              'null' => null,
              'mixed' => $value,
              'array' => is_array($value) ? $value : [],
            };
          }
        }

        if ($type->allowsNull() === false && $casted === null) throw new \Exception(
          "The spaw parameter '_paramName_' dont allow Null value. Expected type '$typeName', provided value : " . Json::encode($value),
        );
      }
    }

    return $casted;
  }

  /**
   * @param \ReflectionParameter[] $params
   * @param \ReflectionAttribute|null $attribut
   */
  private function compareParameters(?array $intention, $params, string $filePocket = null, $attribut = null): void
  {
    assert($attribut !== null, "The attribut parameter must not be null.");

    // loop in params.
    foreach ($params as $param) {
      $type = $param->getType();

      if ($type instanceof ReflectionNamedType) $type = $type->getName();

      // Check in intion.
      if ($intention === null) throw new Exception("Your quest has no parameter where " . count($params) . " parameter's are excepted.");

      $paramName = $this->getAliasName($param->getName(), $attribut);

      $intentionPropertyValue = isset($intention[$paramName]) ? $intention[$paramName] : null;

      if ($intentionPropertyValue !== null) {
        // check intention type.
        // $intentionType = $this->typeChecker($intentionPropertyValue);

        // if ($type != $intentionType) {
        //   throw new \Exception("The intention quest parameter '$paramName' has diferent parameter type with spaw method parameter '$$paramName'. Expected type '$type' found '$intentionType'");
        // }
      } else {
        $exceptionMessage = "The aimed parameter '$paramName' are not optional ";

        if ($filePocket) $filePocket = $this->getAliasName($filePocket, $attribut);

        if ($paramName !== $filePocket) {
          try {
            $param->getDefaultValue();

            // if ($paramHasDefaultValue == false) throw new \Exception($exceptionMessage);
          } catch (\Exception $e) {
            throw new \Exception($exceptionMessage);
          }
        }
      }
    }
  }

  /**
   * @param \ReflectionClass<object> $class
   * @param \ReflectionMethod $method
   * @param array $intention
   */
  public function call($class, $method): mixed
  {
    // Class construction.
    $classAttributs = $class->getAttributes(QuestSpawClass::class);
    $constructionParam = null;

    if (isset($classAttributs[0])) {
      $classAttributs = $classAttributs[0]->newInstance();
      $constructionParam = $classAttributs->constructWith;
    }

    $classInstance = null;

    if ($constructionParam !== null) {
      $classConstructor = $class->getConstructor();
      $classParams = $classConstructor->getParameters();

      if (count($classParams) !== count($constructionParam ?? [])) {
        throw new \Exception("The quest guid can't construct the class '" . $class->getName() . "', beacause parameters provided in QuestSpawClass are too less or to much.");
      }

      if (Arr::isList($constructionParam) == false) throw new \Exception("Only indexed array are allowed on `constructWith` parameter.");

      try {
        $classInstance = $class->newInstance(...$constructionParam);
      } catch (\Exception $e) {
        throw new \Exception("Parameter error from QuestSpawClass at '" . $class->getName() . "'. " . $e->__toString());
      }
    } else $classInstance = $class->newInstanceWithoutConstructor();

    // METHODS --- :
    $newMethodArgList = [];

    $newMethodArgList = $this->intentionTypeChecker($this->intentionRequest(), $method, $method->getAttributes(QuestSpaw::class)[0]);

    // try {
    // ! If wrapped in a Try blok, remember to rethrow the catched exception !
    $result = $method->invokeArgs($classInstance, $newMethodArgList);
    // } catch (\Exception $e) {
    // $methodName = $method->getName();

    // throw $e;

    // throw new \Exception(
    //   "Your quest intention arguments are wrong or are not match the QuestSpaw parameters types or numbers. Spaw method: [$methodName] \n\n" .
    //   $e->__toString(),
    // );
    // }

    // Controll response type.
    $methodAttribut = $method->getAttributes(QuestSpaw::class);

    $instanceOfSpaw = $methodAttribut[0]->newInstance();

    $responseAsjson = $instanceOfSpaw->jsonResponse;

    if ($responseAsjson) {
      return response()->json($result);
    } else
      return $result;
  }
}
