<?php

use App\Http\Middleware\SanctumCustomMiddleware;
use App\mobile_v1\admin\AdminMan;
use App\mobile_v1\admin\AppAboutMan;
use App\mobile_v1\admin\AvisMan;
use App\mobile_v1\admin\CarousselMan;
use App\mobile_v1\admin\ContactsMan;
use App\mobile_v1\admin\DonationMan;
use App\mobile_v1\admin\MembershipDemandes;
use App\mobile_v1\admin\PCNMan;
use App\mobile_v1\admin\RecomandationsMan;
use App\mobile_v1\admin\ResponsablesMan;
use App\mobile_v1\admin\UsersMan;
use Hacp0012\Quest\Quest;
use Illuminate\Support\Facades\Route;

// Quest::spawn(uri: 'admin', routes: 'app/mobile_v1/admin')->middleware(SanctumCustomMiddleware::class);
Quest::spawn(uri: 'admin', routes: [
  AdminMan::class,
  CarousselMan::class,
  ContactsMan::class,
  DonationMan::class,
  MembershipDemandes::class,
  PCNMan::class,
  RecomandationsMan::class,
  ResponsablesMan::class,
  UsersMan::class,
  AppAboutMan::class,
  AvisMan::class,
])->middleware(SanctumCustomMiddleware::class);
