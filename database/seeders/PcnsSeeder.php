<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class PcnsSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $poolId = Uuid::uuid4();
    DB::table('pcns')->insert([
      'id'=> $poolId,
      'type'=> 'POOL',
      'parent'=> null,
      'nom'=> 'Bukavu',
      'label'=> fake()->sentence(),
    ]);

    # --------------------------------------------------------- >
    DB::table('pcns')->insert([
      'id'=> Uuid::uuid4(),
      'type'=> 'COM',
      'parent'=> $poolId,
      'nom'=> 'Saint Pierre-Claver',
      'label'=> fake()->sentence(),
    ]);

    $comId = Uuid::uuid4();
    DB::table('pcns')->insert([
      'id'=> $comId,
      'type'=> 'COM',
      'parent'=> $poolId,
      'nom'=> 'Notre-Dame',
      'label'=> fake()->sentence(),
    ]);
    DB::table('pcns')->insert([
      'id' => Uuid::uuid4(),
      'type'=> 'COM',
      'parent'=> $poolId,
      'nom'=> 'François Xavier',
      'label'=> fake()->sentence(),
    ]);
    DB::table('pcns')->insert([
      'id' => Uuid::uuid4(),
      'type'=> 'COM',
      'parent'=> $poolId,
      'nom'=> 'Mater Dei',
      'label'=> fake()->sentence(),
    ]);
    DB::table('pcns')->insert([
      'id' => Uuid::uuid4(),
      'type'=> 'COM',
      'parent'=> $poolId,
      'nom'=> 'Sainte Thérèse',
      'label'=> fake()->sentence(),
    ]);
    DB::table('pcns')->insert([
      'id' => Uuid::uuid4(),
      'type'=> 'COM',
      'parent'=> $poolId,
      'nom'=> 'Sainte Famille',
      'label'=> fake()->sentence(),
    ]);

    # -------------------------------------------------------- >
    DB::table('pcns')->insert([
      'id' => Uuid::uuid4(),
      'type'=> 'NA',
      'parent'=> $comId,
      'nom'=> 'Bukavu',
      'label'=> fake()->sentence(),
    ]);
    DB::table('pcns')->insert([
      'id' => Uuid::uuid4(),
      'type'=> 'NA',
      'parent'=> $comId,
      'nom'=> 'Notre-Dame',
      'label'=> fake()->sentence(),
    ]);
    DB::table('pcns')->insert([
      'id' => Uuid::uuid4(),
      'type'=> 'NA',
      'parent'=> $comId,
      'nom'=> "Sainte Thérèse de l'enfant Jésus",
      'label'=> fake()->sentence(),
    ]);
    DB::table('pcns')->insert([
      'id' => Uuid::uuid4(),
      'type'=> 'NA',
      'parent'=> $comId,
      'nom'=> "Sainte Marie-Madeleine",
      'label'=> fake()->sentence(),
    ]);
    DB::table('pcns')->insert([
      'id' => Uuid::uuid4(),
      'type'=> 'NA',
      'parent'=> $comId,
      'nom'=> "Saint Antoine de Padou",
      'label'=> fake()->sentence(),
    ]);
    DB::table('pcns')->insert([
      'id' => Uuid::uuid4(),
      'type'=> 'NA',
      'parent'=> $comId,
      'nom'=> "Munzihirwa",
      'label'=> fake()->sentence(),
    ]);
    DB::table('pcns')->insert([
      'id' => Uuid::uuid4(),
      'type'=> 'NA',
      'parent'=> $comId,
      'nom'=> "Pierre Vivante",
      'label'=> fake()->sentence(),
    ]);
  }
}
