<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SysdatasSeeader extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    DB::table('sysdatas')->insert([
      'label' => "Country phone codes",
      'type'  => 'ARRAY',
      'key'   => 'phone_code',
      'data'  => Json::encode([
        ['code' => '243', 'country' => 'CD', 'country_name' => 'DRC'],
      ]),
    ]);
  }
}
