<?php

use App\Http\Middleware\SanctumCustomMiddleware;
use App\mobile_v1\app\home\HomeContentsHandler;
use Hacp0012\Quest\Quest;

Quest::spawn('home/handler', routes: [HomeContentsHandler::class])->middleware(SanctumCustomMiddleware::class);
