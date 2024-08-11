<?php

namespace App\mobile_v1\classes;

use App\Models\Sysdata;
use Illuminate\Database\Eloquent\Casts\Json;

class SysdataHandlerClass
{
  /**
   * Create a new class instance.
   */
  public function __construct()
  {
    //
  }

  public function miscDatas()
  {
    return SysdataHandlerClass::getArray('phone_code');
  }

  static public function get(string $dataType, string $key) : mixed
  {
    return null;
  }

  static public function getArray(string $key)
  {
    $data = Sysdata::where(['type'=> 'ARRAY', 'key'=> $key])->get(['data'])->first();
    $casted = Json::decode($data['data']);
    return $casted;
  }
}
