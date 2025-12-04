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
Route::get('/login', [PageController::class, 'login'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/start', [PageController::class, 'start'])->name('start');
Route::get('/event', [PageController::class, 'event'])->name('event');
Route::post('/event/store', [PageController::class, 'storeEvent'])->name('event.store')->middleware('auth');
Route::put('/event/{id}', [PageController::class, 'updateEvent'])->name('event.update')->middleware('auth');
Route::delete('/event/{id}', [PageController::class, 'deleteEvent'])->name('event.delete')->middleware('auth');
Route::get('/team', [TeamController::class, 'index'])->name('team');
Route::post('/team', [TeamController::class, 'store'])->name('team.store')->middleware('auth');
Route::put('/team/{id}', [TeamController::class, 'update'])->name('team.update')->middleware('auth');
Route::delete('/team/{id}', [TeamController::class, 'destroy'])->name('team.destroy')->middleware('auth');
Route::post('/team/member', [TeamController::class, 'addMember'])->name('team.addMember')->middleware('auth');
Route::post('/team/{id}/join', [TeamController::class, 'requestJoin'])->name('team.join')->middleware('auth');
Route::post('/solicitud/{id}/accept', [TeamController::class, 'acceptJoin'])->name('team.acceptJoin')->middleware('auth');
Route::post('/solicitud/{id}/reject', [TeamController::class, 'rejectJoin'])->name('team.rejectJoin')->middleware('auth');
Route::get('/registrar', [RegisterController::class, 'showRegistrationForm'])->name('registrar')->middleware('guest');
Route::post('/registrar', [RegisterController::class, 'register'])->name('register.post')->middleware('guest');
Route::get('/perfil', [PageController::class, 'perfil'])->name('perfil')->middleware('auth');
Route::post('/perfil/update', [PageController::class, 'updateProfile'])->name('perfil.update')->middleware('auth');
Route::post('/admin/create-judge', [PageController::class, 'createJudge'])->name('admin.createJudge')->middleware(['auth', 'role:admin']);
Route::get('/login-demo', [PageController::class, 'login'])->name('login');

// Participation Routes
use App\Http\Controllers\ParticipationController;
Route::get('/evento/{id}/participar', [ParticipationController::class, 'show'])->name('participation.show')->middleware('auth');
Route::post('/evento/{id}/participar', [ParticipationController::class, 'upload'])->name('participation.upload')->middleware('auth');

// Evaluation Routes
use App\Http\Controllers\EvaluationController;
Route::get('/evento/{id}/evaluar', [EvaluationController::class, 'show'])->name('event.evaluate')->middleware(['auth', 'role:juez']);
Route::get('/evento/{evento_id}/evaluar/{equipo_id}', [EvaluationController::class, 'evaluateTeam'])->name('event.evaluate.team')->middleware(['auth', 'role:juez']);
Route::post('/evento/{id}/evaluar', [EvaluationController::class, 'store'])->name('event.evaluate.store')->middleware(['auth', 'role:juez']);
Route::get('/evento/{id}/ranking', [EvaluationController::class, 'ranking'])->name('event.ranking')->middleware('auth');
Route::get('/evento/{evento_id}/certificado/{equipo_id}', [EvaluationController::class, 'certificate'])->name('event.certificate')->middleware('auth');

// Admin Routes
Route::post('/evento/{id}/assign-judge', [PageController::class, 'assignJudge'])->name('event.assignJudge')->middleware(['auth', 'role:admin']);