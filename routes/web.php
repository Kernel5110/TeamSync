<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

Route::get('/', [PageController::class, 'index'])->name('index');
Route::get('/login', [PageController::class, 'login'])->name('login');
Route::get('/start', [PageController::class, 'start'])->name('start');
Route::get('/event', [PageController::class, 'event'])->name('event');
Route::get('/team', [PageController::class, 'team'])->name('team');
