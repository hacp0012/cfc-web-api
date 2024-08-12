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
    Schema::create('otps', function (Blueprint $table) {
      $table->comment("Otp handler");
      $table->id();

      $table->string('otp');
      $table->string('ref')->nullable()->comment("Owner reference. can be phone number or ...");
      $table->json('data')->nullable(); //->default('{}');

      $table->bigInteger('expire_at')->comment("Expiration dalay in seconds.");

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('otps');
  }
};
