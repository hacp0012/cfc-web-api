<?php

namespace App\mobile_v1\admin;

use App\mobile_v1\app\search\SearchEngine;
use App\Models\Pcn;
use Hacp0012\Quest\Attributs\QuestSpaw;
use Hacp0012\Quest\QuestResponse;
use Hacp0012\Quest\QuestSpawMethod;
use Illuminate\Database\Eloquent\Collection;

class PCNMan
{
  /**
   * Create a new class instance.
   */
  public function __construct() {}

  # METHODS -----------------------------------------------------------------------------------:
  #[QuestSpaw(ref: 'O4KOJLTC5Y7CpW0Lm7oBcxVuYqPkuIHSZiMh', method: QuestSpawMethod::GET)]
  public function getPools(): Collection
  {
    $pools = Pcn::whereType('POOL')->get();

    $list = [];
    foreach ($pools as $pool) $list[] = $pool->childresCounter = $this->countChildrenOf('POOL', $pool->id);

    return $pools;
  }

  #[QuestSpaw(ref: '9zq5szI64lMmhVR0XAixLSV9cOOt05SAVQDr', method: QuestSpawMethod::GET)]
  public function getComs(string $pool): Collection
  {
    $coms = Pcn::where(['type' => 'COM', 'parent' => $pool])->get();

    $list = [];
    foreach ($coms as $com) $list[] = $com->childresCounter = $this->countChildrenOf('COM', $com->id);

    return $coms;
  }

  #[QuestSpaw(ref: 'SpIeq26hjJzeIqZuYF7UVKfv5KhUv2XySQcS', method: QuestSpawMethod::GET)]
  public function getNodes(string $com): Collection
  {
    $nodes = Pcn::where(['type' => 'NA', 'parent' => $com])->get();

    return $nodes;
  }

  #[QuestSpaw(ref: 'pJS836gW8WQunuQNCoaWMF8rMBoxY1ZabCp9', method: QuestSpawMethod::GET)]
  public function homeDash(): array
  {
    QuestResponse::setForJson(ref: 'pJS836gW8WQunuQNCoaWMF8rMBoxY1ZabCp9', dataName: 'counts');
    return [
      'pools' => $this->countOf('POOL'),
      'coms' => $this->countOf('COM'),
      'nodes' => $this->countOf('NA'),
    ];
  }

  public function countOf(string $section): int
  {
    $count = Pcn::whereType($section)->count();
    return $count;
  }

  public function countChildrenOf(string $section, string $id = null): int
  {
    if ($section == 'POOL') {
      $count = Pcn::where(['type' => 'COM', 'parent' => $id])->count();
      return $count;
    } elseif ($section == 'COM') {
      $count = Pcn::where(['type' => 'NA', 'parent' => $id])->count();
      return $count;
    }

    return 0;
  }

  #[QuestSpaw(ref: 'xXstZjuI6nggUdjrl49OOnoe8YbIINAyLrY4', method: QuestSpawMethod::GET)]
  public function getMy(string $id)
  {
    $state = false;

    $item = Pcn::find($id);

    if ($item) {
      $state = true;

      switch ($item->type) {
        case 'POOL':
          $item->childresCounter = $this->countChildrenOf('POOL', $item->id);
          break;
        case 'COM':
          $item->childresCounter = $this->countChildrenOf('COM', $item->id);
          break;
      }
    }

    QuestResponse::setForJson(ref: 'xXstZjuI6nggUdjrl49OOnoe8YbIINAyLrY4', model: ['success' => $state]);

    return $item;
  }

  // POOLS HANDLERS ---------------------------------------------------------------------------:
  /**
   * @param array<string,float> $gps {lat:double, lon:double}
   */
  #[QuestSpaw(ref: 'Azom44yAWwzRielFsy48NZ3zljDJJt1UhdcP', method: QuestSpawMethod::POST)]
  public function poolAdd(string $name, string $adress, string $label = null, array $gps = null): bool
  {
    $model = [
      'type'    => 'POOL',
      'nom'     => $name,
      'adresse' => $adress,
      'label'   => $label,
      'gps'     => $gps,
    ];

    $state = Pcn::create($model);

    QuestResponse::setForJson(ref: 'Azom44yAWwzRielFsy48NZ3zljDJJt1UhdcP', dataName: 'success');

    if ($state) return true;

    return false;
  }

  #[QuestSpaw(ref: 'i5ah6sSfwzGMpd928YVSUqxaMs0LWnSrKHrX', method: QuestSpawMethod::DELETE)]
  public function poolRemove(string $poolId): bool
  {
    $pool = Pcn::find($poolId);

    QuestResponse::setForJson(ref: 'i5ah6sSfwzGMpd928YVSUqxaMs0LWnSrKHrX', dataName: 'success');

    if ($pool) {
      $this->comRemoveAllof($pool->id);

      $pool->delete();

      return true;
    }
    return false;
  }

  #[QuestSpaw(ref: 'lrpsVhjuqdZ2d3BGd8A2j885MsBXqQ086Ger', method: QuestSpawMethod::DELETE)]
  public function poolRemoveAll(): bool
  {
    $req = Pcn::where(['type' => 'POOL']);

    $pools = $req->get();

    foreach ($pools as $pool) {
      $this->comRemoveAllOf($pool->id);
    }

    $state = $req->delete();

    return $state;
  }

  #[QuestSpaw(ref: '3w0iEP56j7GHJQWlDCsxYCwGctg3a02ZNbiP', method: QuestSpawMethod::POST)]
  public function poolUpdate(string $poolId, string $name = null, string $adress = null, string $label = null, array $gps = []): bool
  {
    $model = [];

    if ($name) $model['nom'] = $name;
    if ($adress) $model['adresse'] = $adress;
    if ($label) $model['label'] = $label;
    if ($gps) $model['gps'] = $gps;

    if (count($model) > 0) {
      $state = Pcn::where(['id' => $poolId, 'type' => 'POOL'])->update($model);

      QuestResponse::setForJson(ref: '3w0iEP56j7GHJQWlDCsxYCwGctg3a02ZNbiP', dataName: 'success');

      return $state;
    }

    return false;
  }

  // COMS HANDLERS ----------------------------------------------------------------------------:
  /**
   * @param array<string,float> $gps {lat:double, lon:double}
   */
  #[QuestSpaw(ref: '0KVX1JKPu5dpnBV4Y06pNTct816ZW218aui4', method: QuestSpawMethod::POST)]
  public function comAdd(string $poolId, string $name, string $adress, string $label = null, array $gps = null): bool
  {
    $model = [
      'type'    => 'COM',
      'parent'  => $poolId,
      'nom'     => $name,
      'adresse' => $adress,
      'label'   => $label,
      'gps'     => $gps,
    ];

    $state = Pcn::create($model);

    QuestResponse::setForJson(ref: '0KVX1JKPu5dpnBV4Y06pNTct816ZW218aui4', dataName: 'success');

    if ($state) return true;

    return false;
  }

  #[QuestSpaw(ref: 'GPdXpR0GElTwIJBbj1IPrCYMKKTUdQcEIDoT', method: QuestSpawMethod::DELETE)]
  public function comRemove(string $comId): bool
  {
    $com = Pcn::find($comId);

    QuestResponse::setForJson(ref: 'GPdXpR0GElTwIJBbj1IPrCYMKKTUdQcEIDoT', dataName: 'success');

    if ($com) {
      $this->nodeRemoveAllOf($com->id);

      $com->delete();

      return true;
    }
    return false;
  }

  #[QuestSpaw(ref: 'Z70hRLrkPu4g4i6syPWnDjIjgOsJNrUCzHCn', method: QuestSpawMethod::DELETE)]
  public function comRemoveAllOf(string $poolId): bool
  {
    $req = Pcn::where(['parent' => $poolId, 'type' => 'COM']);

    // Get all coms.
    $coms = $req->get();

    // Loop on all.
    foreach ($coms as $com) {
      // Remove all children.
      $this->nodeRemoveAllOf($com->id);
    }

    // Remove all coms.
    $state = $req->delete();

    return $state;
  }

  #[QuestSpaw(ref: '8qLPSm7gh06wqVpPqvJwB4QYuvC5kb3tfrQl', method: QuestSpawMethod::POST)]
  public function comUpdate(string $comId, string $name = null, string $adress = null, string $label = null, array $gps = []): bool
  {
    $model = [];

    if ($name) $model['nom'] = $name;
    if ($adress) $model['adresse'] = $adress;
    if ($label) $model['label'] = $label;
    if ($gps) $model['gps'] = $gps;

    if (count($model) > 0) {
      $state = Pcn::where(['id' => $comId, 'type' => 'COM'])->update($model);

      QuestResponse::setForJson(ref: '8qLPSm7gh06wqVpPqvJwB4QYuvC5kb3tfrQl', dataName: 'success');

      return $state;
    }

    return false;
  }

  // NA HANDLERS ------------------------------------------------------------------------------:
  /**
   * @param array<string,float> $gps {lat:double, lon:double}
   */
  #[QuestSpaw(ref: 'tkL6aJfppk9joAPKxdCVTWNTSuDAatHOuVti', method: QuestSpawMethod::POST)]
  public function nodeAdd(string $comId, string $name, string $adress, string $label = null, array $gps = null): bool
  {
    $model = [
      'type'    => 'NA',
      'parent'  => $comId,
      'nom'     => $name,
      'adresse' => $adress,
      'label'   => $label,
      'gps'     => $gps,
    ];

    $state = Pcn::create($model);

    QuestResponse::setForJson(ref: 'tkL6aJfppk9joAPKxdCVTWNTSuDAatHOuVti', dataName: 'success');

    if ($state) return true;

    return false;
  }

  #[QuestSpaw(ref: 'EWD2ZJpjJIX6FRwXweCnJfDc52ogHQu0xanL', method: QuestSpawMethod::DELETE)]
  public function nodeRemove(string $nodeId): bool
  {
    $node = Pcn::find($nodeId);

    QuestResponse::setForJson(ref: 'EWD2ZJpjJIX6FRwXweCnJfDc52ogHQu0xanL', dataName: 'success');

    if ($node) {
      $state = $node->delete();

      return $state;
    }
    return false;
  }

  #[QuestSpaw(ref: 'MXTM8vyJisFsVXsN8IKABOD3pEo80dT5zhwg', method: QuestSpawMethod::DELETE)]
  public function nodeRemoveAllOf(string $comId): bool
  {
    $req = Pcn::where(['parent' => $comId, 'type' => 'NA']);

    // Remove all nodes.
    $state = $req->delete();

    return $state;
  }

  #[QuestSpaw(ref: 'NjstkEVtkB2UjTZ1ZaACMBt6DCLf9s4JEceG', method: QuestSpawMethod::POST)]
  public function nodeUpdate(string $nodeId, string $name, string $adress, string $label = null, array $gps = null): bool
  {
    $model = [];

    if ($name) $model['nom'] = $name;
    if ($adress) $model['adresse'] = $adress;
    if ($label) $model['label'] = $label;
    if ($gps) $model['gps'] = $gps;

    if (count($model) > 0) {
      $state = Pcn::where(['id' => $nodeId, 'type' => 'NA'])->update($model);

      QuestResponse::setForJson(ref: 'NjstkEVtkB2UjTZ1ZaACMBt6DCLf9s4JEceG', dataName: 'success');

      return $state;
    }

    return false;
  }

  // SEARCH -----------------------------------------------------------------------------------:
  /** @param string $section POOL | COM | NA */
  #[QuestSpaw(ref: '6IWomX3HS1mSkRKpQ0AKnfjjHRgKB7pRXD1U', method: QuestSpawMethod::GET)]
  public function search(string $name, string $section): array
  {
    $searchEngine = new SearchEngine(keyphrase: $name);

    $results = $searchEngine->customSearch(tableModel: new Pcn, fields: ['nom', 'type']);

    // Filter.
    $filtreds = [];
    foreach ($results as $result) {
      if ($result->type == $section) {
        $result->childresCounter = $this->countChildrenOf($section, $result->id);
        $filtreds[] = $result;
      }
    }

    return $filtreds;
  }
}
