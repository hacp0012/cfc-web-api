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
    Schema::create('children_of', function (Blueprint $table) {
      $table->comment("Children of couples.");
      $table->uuid('id')->primary();

      $table->enum('type', ['VIRTUAL', 'CONCRET'])->default('VIRTUAL')->comment("Child type: VIRTUAL when the parent add a child. CONCRET when child want to join family.");
      $table->enum('parent_type', ['PARRAIN', 'PARENT'])->default('PARENT')->comment("Parent type of this child.");

      $table->uuid('couple');
      $table->uuid('child')->nullable()->comment("A child uuid");

      $table->json('data')->default('{}')->comment("Child data : nom, genre, d_naissance, photo_pid");

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('children_of');
  }
};
