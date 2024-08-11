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
    Schema::create('files', function (Blueprint $table) {
      $table->comment("File container of file type : IMAGE|DOC|AUDIO");
      $table->uuid("id")->primary();

      $table->enum("type", ['IMAGE', 'DOC', 'AUDIO', 'NONE'])->default('NONE')->comment("Content type");
      $table->text('pid')->comment("Public identifier");

      $table->string('size');
      $table->string('mime');
      $table->string('ext');

      $table->string('label')->nullable();

      $table->string('hashed_name');
      $table->string('folder_path');
      $table->string("original_name")->nullable();

      $table->uuid('owner');
      $table->string('owner_group')->comment("USER|ECHO|TEACHING|COM");
      $table->string('content_group')->comment("Content type of owner_group.");

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('files');
  }
};
