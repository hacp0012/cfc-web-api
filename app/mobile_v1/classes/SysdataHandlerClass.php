<?php

namespace App\mobile_v1\classes;

use App\Models\Sysdata;
use Illuminate\Database\Eloquent\Casts\Json;

class SysdataHandlerClass
{
  /**
   * Create a new class instance.
   */
  public function __construct() {}

  // --------------------------------------------------------------------- /

  public function miscDatas()
  {
    return [
      'phone_codes' => SysdataHandlerClass::getArray('phone_codes'),
      'roles'      => SysdataHandlerClass::getArray('users_roles'),
    ];
  }

  /** @return mixed can be NULL */
  static public function get(SysDataType $type, string $key): mixed
  {
    $data = Sysdata::where(['type' => $type->name, 'key' => $key])->first();

    $casted = null;

    if ($data) {
      $casted = match ($type) {
        SysDataType::ARRAY  => $data->data['data'],
        SysDataType::ASSOC  => $data->data['data'],
        SysDataType::FLOAT  => floatval($data->data['data']),
        SysDataType::INT    => intVal($data->data['data']),
        SysDataType::TEXT   => $data->data['data'],
        default => NULL,
      };
    }

    return $casted;
  }

  static public function getArray(string $key): array
  {
    $data = Sysdata::where(['type' => 'ARRAY', 'key' => $key])->get(['data'])->first();
    $casted = $data['data']['data'];
    return $casted;
  }

  static public function contain(string $key): bool
  {
    $isContain = Sysdata::where('key', $key)->first();
    return $isContain != null ? true : false;
  }

  static public function set(SysDataType $type, string $key, mixed $data, string $label = null): bool
  {
    $state = false;

    if (SysdataHandlerClass::contain($key)) {
      $state = Sysdata::where(['type' => $type->name, 'key' => $key])->update(['data' => ['data' => $data]]);
    } else {
      $created = Sysdata::create([
        'type' => $type->name,
        'key' => $key,
        'data' => ['data' => $data],
        'label' => $label ? $label : "Undefined",
      ]);

      $state = $created ? true : false;
    }

    return $state;
  }
}

enum SysDataType
{
  case ARRAY;
  case INT;
  case ASSOC;
  case TEXT;
  case FLOAT;
}
