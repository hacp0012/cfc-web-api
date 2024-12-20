<?php

use App\mobile_v1\app\search\SearchEngine;
use Hacp0012\Quest\Quest;

Quest::spawn('search', SearchEngine::class);
