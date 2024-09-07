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
    Schema::create('comments', function (Blueprint $table) {
      $table->comment("Commentaires");
      $table->uuid('id')->primary();

      $table->uuid('user');
      $table->tinyText('for')->comment("Model namespace");
      $table->uuid('for_id');

      $table->foreignUuid('parent')->nullable()->constrained(table: 'comments')->cascadeOnDelete();
      $table->text('comment');

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('comments');
  }
};
