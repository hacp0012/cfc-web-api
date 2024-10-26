<?php

namespace App\mobile_v1\app\search;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class Rater
{
  public function __construct(protected string $keyphrase, protected Collection $results) {}

  private function engine(array $fields)
  {
    $splitedKeyphrase = preg_split("/[ ]+/", Str::lower($this->keyphrase));

    $rateds = [];

    foreach ($this->results as $result) {
      $count = 0;

      foreach ($fields as $field) {
        $fieldData = Str::lower($result->{$field});

        foreach ($splitedKeyphrase as $key) {
          $count += Str::substrCount($fieldData, $key);
        }
      }

      $rateds[] = ['rate' => $count, 'item' => $result];
    }

    return $rateds;
  }

  public function echo()
  {
    $data = $this->engine(Engine::$echoFields);

    return $data;
  }

  public function com()
  {
    $data = $this->engine(Engine::$comFields);

    return $data;
  }

  public function teaching()
  {
    $data = $this->engine(Engine::$teachingFields);

    return $data;
  }

  static public function unrate(array $rateds)
  {
    $unrateds = [];

    foreach($rateds as $rated) $unrateds[] = $rated['item'];

    return $unrateds;
  }
}
