<?php

use App\Http\Middleware\SanctumCustomMiddleware;
use App\mobile_v1\app\favorites\FavoritesHandler;
use Hacp0012\Quest\Quest;

Quest::spawn('favorites', FavoritesHandler::class)->middleware(SanctumCustomMiddleware::class);
