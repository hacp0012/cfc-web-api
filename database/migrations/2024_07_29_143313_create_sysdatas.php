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
    Schema::create('sysdatas', function (Blueprint $table) {
      $table->comment("System Datas");
      $table->id();

      $table->enum('type', ['INT', 'TEXT', 'ARRAY', 'ASSOC', 'FLOAT'])->default('TEXT');

      $table->tinyText('key');
      $table->json('data');

      $table->string('label');

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('sysdatas');
  }
};
