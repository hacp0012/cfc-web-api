<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
  // return Inertia::render('Welcome', [
  //     'canLogin' => Route::has('login'),
  //     'canRegister' => Route::has('register'),
  //     'laravelVersion' => Application::VERSION,
  //     'phpVersion' => PHP_VERSION,
  // ]);
  // return Inertia::render('Home/Login');
  return Inertia::render('Home/Home');
});

// Route::get('/login/test', function () {
//   return Inertia::render('Home/Home');
// });
Route::inertia('/temp/login', 'Home/Login');

// require __DIR__ . '/quest.php';

// Route::get('/dashboard', function () {
//   return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//   Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//   Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//   Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// require __DIR__ . '/auth.php';

