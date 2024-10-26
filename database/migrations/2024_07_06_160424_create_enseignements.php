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
    Schema::create('enseignements', function (Blueprint $table) {
      $table->comment("Les enseignements de la CFC.");
      $table->uuid('id')->primary();

      $table->uuid('published_by')->comment("User (Publisher) UUID");

      $table->enum('state', ['PUBLIC', 'PRIVATE', 'PROTECTED'])->default('PUBLIC')->nullable()->comment("Teaching state.");
      $table->string('visibility')->comment('[level, level_id] - Ex. [level:pool, level_id:pool_id]');

      $table->text('title')->fulltext();
      $table->text('date')->nullable()->comment('Teaching date');
      $table->text('verse')->fulltext()->nullable()->comment('Teaching bible reference');
      $table->text('predicator')->fulltext()->nullable()->comment('Teaching predicator');
      $table->text('text')->fulltext()->nullable()->comment('Teaching description');

      $table->text('picture')->nullable()->comment('pid');
      $table->text('audio')->nullable()->comment('pid');
      $table->text('document')->nullable()->comment("public_id");

      $table->softDeletes();

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('enseignements');
  }
};
