<?php

namespace App\mobile_v1\app\search;

use App\Models\Communique;
use App\Models\Echos;
use App\Models\Enseignement;
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

  static array $teachingFields = ['title', 'text', 'verse', 'predicator'];
  static array $echoFields = ['title', 'text'];
  static array $comFields = ['title', 'text'];

  private string $escapedKeyprase = '';
  private array $wrapedsKeyphrases = [];

  public function teaching()
  {
    $fields = Engine::$teachingFields;

    $where = Enseignement::where($fields[0], 'LIKE', '%' . $this->escapedKeyprase . '%');
    for ($index = 0; $index < count($fields); $index++) {
      foreach ($this->wrapedsKeyphrases as $item) {
        $where->orWhere($fields[$index], 'LIKE', $item);
      }
    }

    $results = $where->get();

    return $results;
  }

  public function echo()
  {
    $fields = Engine::$echoFields;

    $where = Echos::where($fields[0], 'LIKE', '%' . $this->escapedKeyprase . '%');
    for ($index = 0; $index < count($fields); $index++) {
      foreach ($this->wrapedsKeyphrases as $item) {
        $where->orWhere($fields[$index], 'LIKE', $item);
      }
    }

    $results = $where->get();

    return $results;
  }

  public function com()
  {
    $fields = Engine::$comFields;

    $where = Communique::where($fields[0], 'LIKE', '%' . $this->escapedKeyprase . '%');
    for ($index = 0; $index < count($fields); $index++) {
      foreach ($this->wrapedsKeyphrases as $item) {
        $where->orWhere($fields[$index], 'LIKE', $item);
      }
    }

    $results = $where->get();

    return $results;
  }
}
