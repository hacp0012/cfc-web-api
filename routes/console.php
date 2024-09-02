<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('unique', function() {
  $uuid = Uuid::uuid4();
  // $uuid = Str::random(54);
  $this->comment((string) $uuid);
});
