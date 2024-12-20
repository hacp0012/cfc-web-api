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
    Schema::create('calendar_events', function (Blueprint $table) {
      $table->comment("Event calendar");
      $table->uuid('id')->primary();

      $table->uuid('created_by')->comment("User tha create this event.");
      $table->string('visibility')->nullable()->comment('[level, level_id] - Ex. [level:pool, level_id:pool_id]');

      $table->dateTime('start')->comment("Event start date time.");
      $table->dateTime('end')->comment("Event end date time");
      $table->boolean('done')->default(false)->comment("Event is done or not yet done.");

      $table->string('summary')->fulltext()->comment("Event title.");
      $table->text('description')->fulltext()->nullable()->comment("Event description.");

      $table->bigInteger('color')->nullable()->comment("Event color in (int) Hex.");
      $table->json('metadata')->nullable()->comment("App or Event meta data. {time_line: [{time:string, title:string, description:string}]}");

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('calendar_events');
  }
};
