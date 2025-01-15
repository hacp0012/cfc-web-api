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
    Schema::create('users_datas', function (Blueprint $table) {
      $table->comment("Can contain favorites, draft, otp, data, ...");
      $table->uuid('id')->primary();

      // $table->enum('type', ['TEXT', 'ARRAY', 'ASSOC', 'INT', 'FLOAT', 'NULL'])->default('TEXT');
      $table->string('owner');
      $table->string('key')->comment("data key");
      $table->json('data');
      $table->bigInteger('expiration')->comment("unix timestemp");

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('users_datas');
  }
};
