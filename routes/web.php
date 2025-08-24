<?php

use App\Enums\TypeUserEnum;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\pages\MiscError;
use App\Http\Controllers\UserController;


use Illuminate\Support\Facades\Route;


// Handle redirection
Route::middleware([
  'auth:sanctum',
  config('jetstream.auth_session'),
  'verified',
])->group(function () {

  Route::get('/', [DashboardController::class, 'admin'])->name('index');

  // Supervisor Dashboard
  Route::prefix(TypeUserEnum::SUPERVISOR->value)
    ->middleware(['role:' . TypeUserEnum::SUPERVISOR->value])
    ->name(TypeUserEnum::SUPERVISOR->value . '.')
    ->group(function () {
      Route::get('/', [DashboardController::class, TypeUserEnum::SUPERVISOR->value])->name('index');
      Route::resource('pages', PageController::class)->except(['update', 'create']);
      Route::resource('/contact-us', ContactUsController::class);
    });

  // Admin Dashboard
  Route::prefix(TypeUserEnum::ADMIN->value)
    ->middleware(['role:' . TypeUserEnum::ADMIN->value])
    ->name(TypeUserEnum::ADMIN->value . '.')
    ->group(function () {
      Route::get('user/{id}', [UserController::class, 'userDetails'])->name('user.details');
      Route::get('/', [DashboardController::class, TypeUserEnum::ADMIN->value])->name('index');
      Route::resource('users', UserController::class);
      Route::resource('countries', CountryController::class);
      Route::resource('/contact-us', ContactUsController::class);
      Route::resource('pages', PageController::class);
    });

  // User Dashboard
  Route::prefix(TypeUserEnum::USER->value)
    ->middleware(['role:' . TypeUserEnum::USER->value])
    ->name(TypeUserEnum::USER->value . '.')
    ->group(function () {
      Route::get('/', [DashboardController::class, TypeUserEnum::USER->value])->name('index');
    });

  // locale
  Route::get('/lang/{locale}', [LanguageController::class, 'swap']);
  Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('pages-misc-error');
});
