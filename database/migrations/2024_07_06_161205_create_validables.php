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
    Schema::create('validables', function (Blueprint $table) {
      $table->comment("Contain user validable notifications : logic, action");
      $table->uuid('id')->primary();

      $table->string('type')->comment("Validable type");

      $table->string('sender')->comment("Sender");
      $table->string('receiver')->comment("IDs");

      $table->string('key')->nullable();

      $table->json('datas')->nullable(); //->default('{}');
      $table->string('expiration')->nullable()->comment("Life time");

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('validables');
  }
};
