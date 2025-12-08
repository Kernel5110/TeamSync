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

// Legacy Redirects (to fix 404s from old links)
Route::redirect('/event', '/events');
Route::redirect('/team', '/teams');
Route::redirect('/perfil', '/profile');
Route::redirect('/registrar', '/register');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated User Routes
Route::middleware('auth')->group(function () {
    // Dashboard / Start
    Route::get('/start', [PageController::class, 'start'])->name('start');
    
    // Resources
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // Event Management
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::put('/events/{id}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{id}', [EventController::class, 'destroy'])->name('events.destroy');

    // Team Management
    Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
    Route::put('/teams/{id}', [TeamController::class, 'update'])->name('teams.update');
    Route::delete('/teams/{id}', [TeamController::class, 'destroy'])->name('teams.destroy');
    
    // Team Members
    Route::post('/teams/members', [TeamController::class, 'addMember'])->name('teams.members.add');
    Route::post('/teams/{id}/members/remove', [TeamController::class, 'removeMember'])->name('teams.members.remove');
    Route::get('/teams/search', [TeamController::class, 'search'])->name('teams.search');
    Route::post('/teams/{id}/join', [TeamController::class, 'requestJoin'])->name('teams.join');
    
    // Requests (Solicitudes)
    Route::post('/requests/{id}/accept', [TeamController::class, 'acceptJoin'])->name('requests.accept');
    Route::post('/requests/{id}/reject', [TeamController::class, 'rejectJoin'])->name('requests.reject');

    // Participation
    Route::get('/events/{id}/participate', [ParticipationController::class, 'show'])->name('events.participate.show');
    Route::post('/events/{id}/participate', [ParticipationController::class, 'upload'])->name('events.participate.upload');
    
    // Evaluation & Certificates
    Route::get('/events/{id}/ranking', [EvaluationController::class, 'ranking'])->name('events.ranking');
    Route::get('/events/{eventId}/certificates/{teamId}', [EvaluationController::class, 'certificate'])->name('events.certificate');
    Route::post('/events/{eventId}/certificates/{teamId}/email', [EvaluationController::class, 'emailCertificate'])->name('events.certificate.email');
});

// Role-based Routes (Judge)
Route::middleware(['auth', 'role:juez'])->group(function () {
    Route::get('/events/{id}/evaluate', [EvaluationController::class, 'show'])->name('events.evaluate.show');
    Route::get('/events/{eventId}/evaluate/{teamId}', [EvaluationController::class, 'evaluateTeam'])->name('events.evaluate.team');
    Route::post('/events/{id}/evaluate', [EvaluationController::class, 'store'])->name('events.evaluate.store');
});

// Role-based Routes (Admin)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::post('/admin/judges', [AdminController::class, 'createJudge'])->name('admin.judges.create');
    Route::post('/events/{id}/judges', [AdminController::class, 'assignJudge'])->name('events.judges.assign');
    Route::get('/admin/teams', [PageController::class, 'adminTeams'])->name('admin.teams');
    
    // Reports
    Route::get('/events/{id}/reports/pdf', [EventController::class, 'generatePdfReport'])->name('events.reports.pdf');
    Route::get('/events/{id}/reports/csv', [EventController::class, 'generateCsvReport'])->name('events.reports.csv');
    Route::post('/events/{id}/reports/email', [EventController::class, 'emailReport'])->name('events.reports.email');
});