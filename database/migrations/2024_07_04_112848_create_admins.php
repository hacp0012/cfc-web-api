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
    Schema::create('admins', function (Blueprint $table) {
      $table->comment("Aministrors");
      $table->id();

      $table->boolean('is_master')->default(false)->comment("Define is user is master admin. Master admin can't be deleted.");
      $table->uuid('user_ref')->nullable()->comment("A users table ref : UUID. Set this field when the admin is already app user.");
      $table->string('can')->nullable();

      $table->string('name');
      $table->string('uname')->nullable();
      $table->string('pswd');

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('admins');
  }
};
