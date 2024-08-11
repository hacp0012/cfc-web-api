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
    Schema::create('communiques', function (Blueprint $table) {
      $table->comment("");
      $table->uuid('id')->primary();

      $table->string('pcn');
      $table->string('visibilite')->comment('[pcn]');
      $table->string('etat');
      $table->string('communiquer');
      $table->string('attached_files');
      $table->string('publier_par');

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('communiques');
  }
};
