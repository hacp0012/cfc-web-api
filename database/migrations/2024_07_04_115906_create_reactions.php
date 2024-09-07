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
    Schema::create('reactions', function (Blueprint $table) {
      $table->comment("Likes and Views");
      $table->uuid('id')->primary();

      $table->enum('type', ['LIKE', 'VIEW']);
      $table->tinyText('for')->comment("Model namespace");
      $table->uuid('for_id');
      $table->uuid('by')->nullable()->comment("The user uuid.");

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('reactions');
  }
};
