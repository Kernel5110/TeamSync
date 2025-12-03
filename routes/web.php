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
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/start', [PageController::class, 'start'])->name('start');
Route::get('/event', [PageController::class, 'event'])->name('event');
Route::post('/event/store', [PageController::class, 'storeEvent'])->name('event.store')->middleware('auth');
Route::put('/event/{id}', [PageController::class, 'updateEvent'])->name('event.update')->middleware('auth');
Route::delete('/event/{id}', [PageController::class, 'deleteEvent'])->name('event.delete')->middleware('auth');
Route::get('/team', [TeamController::class, 'index'])->name('team');
Route::post('/team', [TeamController::class, 'store'])->name('team.store');
Route::put('/team/{id}', [TeamController::class, 'update'])->name('team.update')->middleware('auth');
Route::delete('/team/{id}', [TeamController::class, 'destroy'])->name('team.destroy')->middleware('auth');
Route::post('/team/member', [TeamController::class, 'addMember'])->name('team.addMember');
Route::get('/registrar', [RegisterController::class, 'showRegistrationForm'])->name('registrar');
Route::post('/registrar', [RegisterController::class, 'register'])->name('register.post');
Route::get('/perfil', [PageController::class, 'perfil'])->name('perfil')->middleware('auth');
Route::post('/perfil/update', [PageController::class, 'updateProfile'])->name('perfil.update')->middleware('auth');
Route::get('/login-demo', [PageController::class, 'login'])->name('login');

// Participation Routes
use App\Http\Controllers\ParticipationController;
Route::get('/evento/{id}/participar', [ParticipationController::class, 'show'])->name('participation.show')->middleware('auth');
Route::post('/evento/{id}/participar', [ParticipationController::class, 'upload'])->name('participation.upload')->middleware('auth');