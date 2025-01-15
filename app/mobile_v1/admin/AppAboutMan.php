<?php

namespace App\mobile_v1\admin;

use App\mobile_v1\classes\SysdataHandlerClass;
use App\mobile_v1\classes\SysDataType;
use Hacp0012\Quest\Attributs\QuestSpaw;
use Hacp0012\Quest\QuestResponse;
use Hacp0012\Quest\SpawMethod;

class AppAboutMan
{
  /**
   * Create a new class instance.
   */
  public function __construct() {}

  // ------------------------------------------ /

  private string $storeKey = 'HgHqBjCpo8NL0oj8Xv';

  #[QuestSpaw(ref: 'gGzFrZLJoQxerUu4i7kd1Simohg1JReOWIpLd8CHE90na', method: SpawMethod::GET)]
  function getText(): string|null
  {
    $data = SysdataHandlerClass::get(type: SysDataType::TEXT, key: $this->storeKey);

    if ($data) {
      QuestResponse::setForJson(ref: 'gGzFrZLJoQxerUu4i7kd1Simohg1JReOWIpLd8CHE90na', model: ['success' => true]);
      return $data;
    }

    QuestResponse::setForJson(ref: 'gGzFrZLJoQxerUu4i7kd1Simohg1JReOWIpLd8CHE90na', model: ['success' => false]);
    return null;
  }

  #[QuestSpaw(ref: '0rb7A7AyuNjngtQCMP586NIn6jnZnUEQDIq9aoTwgWeVY')]
  function update(string $data): bool
  {
    $state = SysdataHandlerClass::set(type: SysDataType::TEXT, key: $this->storeKey, data: $data, label: "Description de la CFC");

    QuestResponse::setForJson(ref: '0rb7A7AyuNjngtQCMP586NIn6jnZnUEQDIq9aoTwgWeVY', dataName: 'success');

    return $state;
  }
}
