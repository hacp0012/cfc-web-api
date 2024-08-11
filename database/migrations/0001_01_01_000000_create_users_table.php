<?php

use Illuminate\Database\Eloquent\Casts\Json;
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
    Schema::create('users', function (Blueprint $table) {
      $table->comment("Users table");
      $table->uuid('id')->primary();

      $table->enum('state', ['INVALIDE', 'VALIDATED'])->default('INVALIDE');

      $table->json('role')
        ->nullable()
        ->default(Json::encode(['state' => null, 'nom' => null, 'role' => null, 'can'=> []]))
        ->comment("Le role qu'il joue au sein de la communaute. (role: null|ACTIVE|INVALIDATE) si INVALIDATE : donc en attente de la validation admin.");

      $table->string('name')->nullable();
      $table->string('fullname')->nullable();

      $table->enum('civility', ['F', 'S'])->default('F')->comment("F: frere, S: soeur");
      $table->date('d_naissance')->nullable();
      $table->enum('genre', ['M', 'F'])->default('M')->comment("remplacer par la civilite");

      $table->uuid('pool')->nullable();
      $table->uuid('com_loc')->nullable();
      $table->uuid('noyau_af')->nullable();
      $table->string('pcn_in_waiting_validation')->nullable()->comment("{} : The PCN wait until admin validate it.");

      $table->uuid('parain')->nullable()->comment("Le parain de l'enfant");
      $table->uuid('parent')->nullable()->comment("Parent de l'utilisateur");
      $table->uuid('couple')->nullable()->comment("Couple dans lequel l'utilisateur est l'epoue ou l'epouse.");

      $table->json('telephone')->nullable()->default('[]')->comment("[243, 987654321]");
      $table->string('password')->nullable();

      $table->string('email')->unique()->nullable();
      $table->timestamp('email_verified_at')->nullable();

      $table->rememberToken();

      $table->softDeletes();

      $table->timestamps();
    });

    Schema::create('password_reset_tokens', function (Blueprint $table) {
      $table->string('email')->primary();
      $table->string('token');
      $table->timestamp('created_at')->nullable();
    });

    Schema::create('sessions', function (Blueprint $table) {
      $table->string('id')->primary();
      $table->foreignId('user_id')->nullable()->index();
      $table->string('ip_address', 45)->nullable();
      $table->text('user_agent')->nullable();
      $table->longText('payload');
      $table->integer('last_activity')->index();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('users');
    Schema::dropIfExists('password_reset_tokens');
    Schema::dropIfExists('sessions');
  }
};
