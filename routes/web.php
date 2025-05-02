<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\SeasonController;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('seasons/create', [SeasonController::class, 'create'])->name('seasons.create');
Route::get('seasons/{slug}', [SeasonController::class, 'show'])->name('seasons.show');
Route::post('/seasons/select-teams', [SeasonController::class, 'selectTeams'])->name('seasons.select-teams');
Route::post('seasons', [SeasonController::class, 'store'])->name('seasons.store');
Route::post('seasons/{slug}/start', [SeasonController::class, 'start'])->name('seasons.start');
Route::post('/seasons/{slug}/simulate-week', [SeasonController::class, 'simulateWeek'])->name('seasons.simulate-week');
Route::post('/seasons/{slug}/simulate-all', [SeasonController::class, 'simulateAll'])->name('seasons.simulate-all');
Route::post('/seasons/{slug}/reset', [SeasonController::class, 'reset'])->name('seasons.reset');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
