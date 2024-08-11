<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('pcns', function (Blueprint $table) {
      $table->comment("Pool, noyeau et communaute d'afermissement");
      $table->uuid('id')->primary();
      $table->uuid('parent')->nullable();

      $table->string('nom');
      $table->enum('type', ['POOL', 'COM', 'NA'])->comment("POOL | COM | AN");
      $table->string('photo')->nullable();
      $table->string('adresse')->nullable();
      $table->text('label')->nullable();

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('pcns');
  }
};
