<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ParticipationController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Public Routes
Route::get('/', [IndexController::class, 'index'])->name('index');
Route::redirect('/home', '/')->name('home');
Route::get('/start', [PageController::class, 'start'])->name('start');
Route::get('/event', [EventController::class, 'index'])->name('event');
Route::get('/team', [TeamController::class, 'index'])->name('team');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [PageController::class, 'login'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::get('/registrar', [RegisterController::class, 'showRegistrationForm'])->name('registrar');
    Route::post('/registrar', [RegisterController::class, 'register'])->name('register.post');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated User Routes
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/perfil', [ProfileController::class, 'show'])->name('perfil');
    Route::post('/perfil/update', [ProfileController::class, 'update'])->name('perfil.update');

    // Event Management
    Route::post('/event/store', [EventController::class, 'store'])->name('event.store');
    Route::put('/event/{id}', [EventController::class, 'update'])->name('event.update');
    Route::delete('/event/{id}', [EventController::class, 'destroy'])->name('event.delete');

    // Team Management
    Route::post('/team', [TeamController::class, 'store'])->name('team.store');
    Route::put('/team/{id}', [TeamController::class, 'update'])->name('team.update');
    Route::delete('/team/{id}', [TeamController::class, 'destroy'])->name('team.destroy');
    Route::post('/team/member', [TeamController::class, 'addMember'])->name('team.addMember');
    Route::post('/team/{id}/remove-member', [TeamController::class, 'removeMember'])->name('team.removeMember');
    Route::get('/team/search', [TeamController::class, 'search'])->name('team.search');
    Route::post('/team/{id}/join', [TeamController::class, 'requestJoin'])->name('team.requestJoin');
    Route::post('/solicitud/{id}/accept', [TeamController::class, 'acceptJoin'])->name('team.acceptJoin');
    Route::post('/solicitud/{id}/reject', [TeamController::class, 'rejectJoin'])->name('team.rejectJoin');

    // Participation
    Route::get('/evento/{id}/participar', [ParticipationController::class, 'show'])->name('participation.show');
    Route::post('/evento/{id}/participar', [ParticipationController::class, 'upload'])->name('participation.upload');
    
    // Evaluation & Certificates
    Route::get('/evento/{id}/ranking', [EvaluationController::class, 'ranking'])->name('event.ranking');
    Route::get('/evento/{evento_id}/certificado/{equipo_id}', [EvaluationController::class, 'certificate'])->name('event.certificate');
});

// Role-based Routes (Juez)
Route::middleware(['auth', 'role:juez'])->group(function () {
    Route::get('/evento/{id}/evaluar', [EvaluationController::class, 'show'])->name('event.evaluate');
    Route::get('/evento/{evento_id}/evaluar/{equipo_id}', [EvaluationController::class, 'evaluateTeam'])->name('event.evaluate.team');
    Route::post('/evento/{id}/evaluar', [EvaluationController::class, 'store'])->name('event.evaluate.store');
});

// Role-based Routes (Admin)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::post('/admin/create-judge', [AdminController::class, 'createJudge'])->name('admin.createJudge');
    Route::post('/evento/{id}/assign-judge', [AdminController::class, 'assignJudge'])->name('event.assignJudge');
    Route::get('/admin/teams', [PageController::class, 'adminTeams'])->name('admin.teams');
});