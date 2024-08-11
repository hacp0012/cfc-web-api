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
    Schema::create('inner_messages', function (Blueprint $table) {
      $table->comment("Simple and liteweight text message.");
      $table->uuid('id')->primary();

      $table->string('message');
      $table->string('sender');
      $table->string('receiver');
      $table->string('sender_group');
      $table->string('read_at');

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('inner_messages');
  }
};
