<?php

namespace App\mobile_v1\classes;

use App\Models\UsersData;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class UserDataHandlerClass
{
  /**
   * Create a new class instance.
   */
  public function __construct(private string $userId) {}

  public function allOfThisUser(): SupportCollection
  {
    $list = UsersData::whereOwner($this->userId)->get(['data', 'key']);

    $datas = collect();
    foreach ($list as $item) {
      $item->data = $item->data['data'];

      $datas->add($item);
    }

    return $datas;
  }

  public function set(string $key, int|float|string|bool|array|null $data): bool
  {
    $created = UsersData::updateOrCreate(['key' => $key, 'owner' => $this->userId], ['data' => ['data' => $data]]);

    return !is_null($created); // $userData->save();
  }

  public function get(string $key): int|float|string|bool|array|null
  {
    $found = UsersData::firstWhere(['owner' => $this->userId, 'key' => $key]);

    $data = null;
    if ($found) $data = $found->data['data'];

    return $data;
  }

  public function contain(string $key): bool
  {
    $found = UsersData::firstWhere(['owner' => $this->userId, 'key' => $key]);

    return ($found != null) ? true : false;
  }
}
