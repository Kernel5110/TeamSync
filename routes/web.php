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
use App\Http\Controllers\AdminDataController;
use App\Http\Controllers\AuditLogController;

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



// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.store');
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
    Route::put('/teams/{id}/leader', [TeamController::class, 'changeLeader'])->name('teams.leader.change');
    
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
    Route::get('/events/{eventId}/certificates/{teamId}/download', [EvaluationController::class, 'downloadCertificate'])->name('events.certificate.download');

    Route::post('/events/{eventId}/certificates/{teamId}/email', [EvaluationController::class, 'emailCertificate'])->name('events.certificate.email');
    
    // Participant Feedback
    Route::get('/my-feedback', [EvaluationController::class, 'myFeedback'])->name('my.feedback');
});

// Role-based Routes (Judge)
Route::middleware(['auth', 'role:juez'])->group(function () {
    Route::get('/events/{id}/evaluate', [EvaluationController::class, 'show'])->name('events.evaluate.show');
    Route::get('/events/{eventId}/evaluate/{teamId}', [EvaluationController::class, 'evaluateTeam'])->name('events.evaluate.team');
    Route::post('/events/{id}/evaluate', [EvaluationController::class, 'store'])->name('events.evaluate.store');
    Route::post('/events/{id}/evaluate/{team}/conflict', [EvaluationController::class, 'declareConflict'])->name('events.evaluate.conflict');
    Route::post('/events/{id}/evaluate/{team}/finalize', [EvaluationController::class, 'finalize'])->name('events.evaluate.finalize');
});

// Role-based Routes (Admin)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::post('/admin/judges', [AdminController::class, 'createJudge'])->name('admin.judges.create');
    Route::post('/events/{id}/judges', [AdminController::class, 'assignJudge'])->name('events.judges.assign');
    Route::put('/events/{id}/status', [EventController::class, 'changeStatus'])->name('events.status.update');
    Route::get('/admin/teams', [PageController::class, 'adminTeams'])->name('admin.teams');
    
    // Reports
    Route::get('/events/{id}/reports/pdf', [EventController::class, 'generatePdfReport'])->name('events.reports.pdf');
    Route::get('/events/{id}/reports/csv', [EventController::class, 'generateCsvReport'])->name('events.reports.csv');
    Route::post('/events/{id}/announcement', [EventController::class, 'sendAnnouncement'])->name('events.announcement.send');
    Route::post('/events/{id}/reports/email', [EventController::class, 'emailReport'])->name('events.reports.email');

    // User Management
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::put('/admin/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');

    // Admin Settings (Instituciones & Carreras)
    Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::post('/admin/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
    Route::get('/admin/logs', [AuditLogController::class, 'index'])->name('admin.logs');
    Route::post('/admin/instituciones', [AdminDataController::class, 'storeInstitucion'])->name('admin.instituciones.store');
    Route::put('/admin/instituciones/{id}', [AdminDataController::class, 'updateInstitucion'])->name('admin.instituciones.update');
    Route::delete('/admin/instituciones/{id}', [AdminDataController::class, 'destroyInstitucion'])->name('admin.instituciones.destroy');
    
    Route::post('/admin/carreras', [AdminDataController::class, 'storeCarrera'])->name('admin.carreras.store');
    Route::put('/admin/carreras/{id}', [AdminDataController::class, 'updateCarrera'])->name('admin.carreras.update');
    Route::delete('/admin/carreras/{id}', [AdminDataController::class, 'destroyCarrera'])->name('admin.carreras.destroy');
    
    Route::post('/admin/evaluations/{id}/unlock', [EvaluationController::class, 'unlock'])->name('admin.evaluations.unlock');
});