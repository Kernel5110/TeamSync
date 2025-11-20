<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\IndexController;

// Ruta principal usando IndexController
Route::get('/', [IndexController::class, 'index'])->name('index');

// Otras rutas
Route::get('/login', [PageController::class, 'login'])->name('login');
Route::get('/start', [PageController::class, 'start'])->name('start');
Route::get('/event', [PageController::class, 'event'])->name('event');
Route::get('/team', [PageController::class, 'team'])->name('team');
