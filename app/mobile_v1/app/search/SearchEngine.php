<?php

namespace App\mobile_v1\app\search;

use Hacp0012\Quest\Attributs\QuestSpaw;
use Hacp0012\Quest\QuestSpawMethod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use stdClass;

enum Section
{
  case all;
  case teaching;
  case echo;
  case communication;
}

class SearchEngine
{
  /**
   * Create a new class instance.
   */
  public function __construct(protected string $keyphrase, protected Section $section = Section::all)
  {
    $this->engine = new Engine($keyphrase);
  }

  static int $return_max = 18;

  private Engine|null $engine = null;

  public function search(): array
  {
    $section = $this->section;

    $results = match ($section) {
      Section::all => $this->sAll(),
      Section::communication => $this->sCom(),
      Section::teaching => $this->sTeaching(),
      Section::echo => $this->sEcho(),
    };

    $sorteds = $this->descSort($results);

    $unrateds = Rater::unrate($sorteds);

    return $unrateds;
  }

  # --------------------------------------------- /

  private function descSort(array $results): array
  {
    usort($results, function ($a, $b) {
      if ($a['rate'] == $b['rate']) return 0;

      return ($a['rate'] > $b['rate']) ? -1 : 1;
    });

    return $results;
  }

  private function rater(Collection $results, Section $section): array
  {
    $rater = new Rater($this->keyphrase, $results);

    $rateds = match ($section) {
      Section::communication => $rater->com(),
      Section::echo => $rater->echo(),
      Section::teaching => $rater->teaching(),
      default => [],
    };

    return $rateds;
  }

  public static function next(array $results, int $count = 0)
  {
    $sliceds = array_slice($results, $count, SearchEngine::$return_max);

    return $sliceds;
  }

  # --------------------------------------------- /

  private function sAll(): array
  {
    $echo = $this->sEcho();
    $com = $this->sCom();
    $teaching = $this->sTeaching();

    $mergeds = array_merge($echo, $com, $teaching);

    $shufleds = Arr::shuffle($mergeds);

    return $shufleds;
  }

  private function sEcho(): array
  {
    $results = $this->engine->echo();

    return $this->rater($results, Section::echo);
  }

  private function sCom(): array
  {
    $results = $this->engine->com();

    return $this->rater($results, Section::communication);
  }

  private function sTeaching(): array
  {
    $results = $this->engine->teaching();

    return $this->rater($results, Section::teaching);
  }

  # [QUEST] --> ------------------------------------------- /

  #[QuestSpaw(ref: 'search-engine-571ca4f1-1e81-4934-a523-1721792a4660', method: QuestSpawMethod::GET)]
  public static function questSearch(string $keyphrase, string $section): stdClass
  {
    $return = new stdClass;
    $return->success = false;

    $selectedSection = match ($section) {
      'teachings'             => Section::teaching,
      'echos'                 => Section::echo,
      'coms'                  => Section::communication,
      '*'                     => Section::all,
      Section::all->name      => Section::all,
      default                 => null,
    };

    if ($selectedSection) {
      $se = new SearchEngine($keyphrase, $selectedSection);

      $results = $se->search();

      $sliceds = SearchEngine::next($results);

      $return->results = $sliceds;
      $return->success = true;
    } else {
      $return->results = [];
      $return->success = false;
    }

    return $return;
  }

  #[QuestSpaw('search-next-0a1f5c1c-4e1d-42a7-b626-3985f4356ee8', QuestSpawMethod::GET)]
  public static function questNext(string $keyphrase, string $section, int $at): stdClass
  {
    $results = SearchEngine::questSearch($keyphrase, $section);

    if ($results->success) {
      $apart = SearchEngine::next($results->results, $at);

      $return = new stdClass;
      $return->success = true;
      $return->results = $apart;

      return $return;
    } else {
      return $results;
    }
  }
}