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
    Schema::create('couples', function (Blueprint $table) {
      $table->comment("Liste of couples and her children");
      $table->uuid('id')->primary();

      $table->string('nom')->fulltext();

      $table->uuid('epoue')->nullable();
      $table->uuid('epouse')->nullable();

      $table->json('enfants')->nullable()/* ->default('[]') */->comment("nom, genre, d_naissance, id, uuid, photo_pid");

      $table->date('d_mariage')->nullable();
      $table->string('adresse')->nullable();
      $table->tinyText('phone')->nullable();
      $table->tinyText('photo')->nullable()->comment("photo_pid");

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('couples');
  }
};
