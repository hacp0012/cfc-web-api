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
    Schema::create('echos', function (Blueprint $table) {
      $table->comment("");
      $table->uuid('id')->primary();

      $table->string('titre');
      $table->string('photo');
      $table->string('visibilite')->comment("pcn");
      $table->string('publier_par');
      $table->string('description');

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('echos');
  }
};
