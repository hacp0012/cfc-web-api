<?php

use App\Http\Middleware\SanctumCustomMiddleware;
use App\mobile_v1\admin\AdminMan;
use Hacp0012\Quest\Quest;
use Illuminate\Support\Facades\Route;

Quest::spawn(uri: 'admin', routes: 'app/mobile_v1/admin')->middleware(SanctumCustomMiddleware::class);
