<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\LoginController;

// Ruta principal usando IndexController
Route::get('/', [IndexController::class, 'index'])->name('index');
Route::redirect('/home', '/')->name('home');

// Otras rutas
Route::get('/login', [PageController::class, 'login'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/start', [PageController::class, 'start'])->name('start');
Route::get('/event', [PageController::class, 'event'])->name('event');
Route::get('/team', [TeamController::class, 'index'])->name('team');
Route::post('/team', [TeamController::class, 'store'])->name('team.store');
Route::post('/team/member', [TeamController::class, 'addMember'])->name('team.addMember');
Route::get('/registrar', [RegisterController::class, 'showRegistrationForm'])->name('registrar');
Route::post('/registrar', [RegisterController::class, 'register'])->name('register.post');
Route::get('/perfil', [PageController::class, 'perfil'])->name('perfil');

Route::get('/login-demo', [PageController::class, 'login'])->name('login');