<?php

use App\Http\Middleware\SanctumCustomMiddleware;
use App\mobile_v1\app\calendar\CalendarEditHandler;
use App\mobile_v1\app\calendar\CalendarHomeHandler;
use App\mobile_v1\app\calendar\CalendarPostHandler;
use Hacp0012\Quest\Quest;
use Illuminate\Support\Facades\Route;

Route::middleware(SanctumCustomMiddleware::class)->prefix('calendar')->group(function () {
  Quest::spawn('home', CalendarHomeHandler::class);

  Quest::spawn('event', [CalendarEditHandler::class, CalendarPostHandler::class]);
});
