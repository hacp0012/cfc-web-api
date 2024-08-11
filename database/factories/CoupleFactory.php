<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Couple>
 */
class CoupleFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'nom'=> fake()->name(),
      'epoue'=> fake()->randomElement([fake()->uuid(), null]),
      'epouse'=> fake()->randomElement([fake()->uuid(), null]),
      'd_mariage'=> fake()->date(),
      'phone'=> fake()->phoneNumber(),
      'adresse'=> fake()->address(),
    ];
  }
}
