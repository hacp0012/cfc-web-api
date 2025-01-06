<?php

namespace App\mobile_v1\app\search;

use App\Models\Communique;
use App\Models\Echos;
use App\Models\Enseignement;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class Engine
{
  /**
   * Create a new class instance.
   */
  public function __construct(protected string $keyphrase)
  {
    $keyphrase = "__" . DB::escape($this->keyphrase) . "__";
    $keyphrase = str_replace("__'", '', $keyphrase);
    $keyphrase = str_replace("'__", '', $keyphrase);

    $this->escapedKeyprase = $keyphrase;

    $spliteds = preg_split("/[ ]+/", $keyphrase);
    $wrapeds = [];

    foreach ($spliteds as $item) $wrapeds[] = '%' . $item . '%';

    $this->wrapedsKeyphrases = $wrapeds;
  }

  static array $teachingFields = ['title', 'text', 'verse', 'predicator', 'date'];
  static array $echoFields = ['title', 'text', 'created_at'];
  static array $comFields = ['title', 'text', 'created_at'];

  private string $escapedKeyprase = '';
  private array $wrapedsKeyphrases = [];

  public function teaching()
  {
    $tableName = 'teachings';

    $fields = Engine::$teachingFields;

    $where = Enseignement::where($fields[0], 'LIKE', '%' . $this->escapedKeyprase . '%');
    for ($index = 0; $index < count($fields); $index++) {
      foreach ($this->wrapedsKeyphrases as $item) {
        $where->orWhere($fields[$index], 'LIKE', $item);
      }
    }

    $results = $where->get();

    foreach ($results as $key => $item) $results[$key]->table = $tableName;

    return $results;
  }

  public function echo()
  {
    $tableName = 'echos';
    $fields = Engine::$echoFields;

    $where = Echos::where($fields[0], 'LIKE', '%' . $this->escapedKeyprase . '%');
    for ($index = 0; $index < count($fields); $index++) {
      foreach ($this->wrapedsKeyphrases as $item) {
        $where->orWhere($fields[$index], 'LIKE', $item);
      }
    }

    $results = $where->get();

    foreach ($results as $key => $item) $results[$key]->table = $tableName;

    return $results;
  }

  public function com()
  {
    $tableName = 'coms';
    $fields = Engine::$comFields;

    $where = Communique::where($fields[0], 'LIKE', '%' . $this->escapedKeyprase . '%');
    for ($index = 0; $index < count($fields); $index++) {
      foreach ($this->wrapedsKeyphrases as $item) {
        $where->orWhere($fields[$index], 'LIKE', $item);
      }
    }

    $results = $where->get();

    foreach ($results as $key => $item) $results[$key]->table = $tableName;

    return $results;
  }

  // Custom engine.
  public function customable(mixed $tableModel, array $fields, ?CustomSearchEngineModelRequestMode $mode = null): Collection|null
  {
    $tableName = $tableModel::class;

    if (count($fields) == 0) return null;

    $where = match($mode) {
      CustomSearchEngineModelRequestMode::ONLY_TRASHED => $tableModel::onlyTrashed()->where($fields[0], 'LIKE', '%' . $this->escapedKeyprase . '%'),
      CustomSearchEngineModelRequestMode::WITH_TRASHED => $tableModel::withTrashed()->where($fields[0], 'LIKE', '%' . $this->escapedKeyprase . '%'),
      // CustomSearchEngineModelRequestMode::WITHOUT_TRASHED => $tableModel::where($fields[0], 'LIKE', '%' . $this->escapedKeyprase . '%'),
      default => $tableModel::where($fields[0], 'LIKE', '%' . $this->escapedKeyprase . '%'),
    };

    for ($index = 0; $index < count($fields); $index++) {
      foreach ($this->wrapedsKeyphrases as $item) {
        $where->orWhere($fields[$index], 'LIKE', $item);
      }
    }

    /** @var \Illuminate\Database\Eloquent\Collection */
    $results = $where->get();

    foreach ($results as $key => $item) $results[$key]->table = $tableName;

    return $results;
  }
}

enum CustomSearchEngineModelRequestMode {
  case WITH_TRASHED;
  case ONLY_TRASHED;
  case WITHOUT_TRASHED;
}
