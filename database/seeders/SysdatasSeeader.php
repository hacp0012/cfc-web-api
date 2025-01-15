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
      'key'   => 'phone_codes',
      'data'  => Json::encode(["data" => [
        ['code' => '243', 'country' => 'CD', 'country_name' => 'DRC'],
      ]]),
    ]);

    DB::table('sysdatas')->insert([
      'label' => "Roles of users",
      'type'  => 'ARRAY',
      'key'   => 'users_roles',
      'data'  => Json::encode(["data" => [
        ['state' => "ACTIVE", 'name' => "Utilisateur standard",        'level' => null, 'role' => "STANDARD_USER",          'can' => []],
        ['state' => "ACTIVE", 'name' => "Chargé de communication",     'level' => null, 'role' => "COMMUNICATION_MANAGER",  'can' => []],
        ['state' => "ACTIVE", 'name' => "Responsable Évangélisation",  'level' => null, 'role' => "EVANGELISM_MANAGER",     'can' => []],
      ]]),
    ]);
  }
}
