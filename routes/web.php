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

Route::get('/', [IndexController::class, 'index'])->name('index');
Route::redirect('/home', '/')->name('home');


Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.store');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/start', [PageController::class, 'start'])->name('start');
    
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/teams', [TeamController::class, 'index'])->name('teams.index');
    
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::put('/events/{eventId}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{eventId}', [EventController::class, 'destroy'])->name('events.destroy');

    Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
    Route::put('/teams/{teamId}', [TeamController::class, 'update'])->name('teams.update');
    Route::delete('/teams/{teamId}', [TeamController::class, 'destroy'])->name('teams.destroy');
    Route::put('/teams/{teamId}/leader', [TeamController::class, 'changeLeader'])->name('teams.leader.change');
    
    Route::post('/teams/members', [TeamController::class, 'addMember'])->name('teams.members.add');
    Route::post('/teams/{teamId}/members/remove', [TeamController::class, 'removeMember'])->name('teams.members.remove');
    Route::get('/teams/search', [TeamController::class, 'search'])->name('teams.search');
    Route::post('/teams/{teamId}/join', [TeamController::class, 'requestJoin'])->name('teams.join');
    Route::post('/teams/{teamId}/leave', [TeamController::class, 'leave'])->name('teams.leave');
    
    Route::post('/requests/{id}/accept', [TeamController::class, 'acceptJoin'])->name('requests.accept');
    Route::post('/requests/{id}/reject', [TeamController::class, 'rejectJoin'])->name('requests.reject');
    
    Route::post('/invitations/{id}/accept', [TeamController::class, 'acceptInvitation'])->name('invitations.accept');
    Route::post('/invitations/{id}/reject', [TeamController::class, 'rejectInvitation'])->name('invitations.reject');

    Route::get('/events/{eventId}/participate', [ParticipationController::class, 'show'])->name('events.participate.show');
    Route::post('/events/{eventId}/participate', [ParticipationController::class, 'upload'])->name('events.participate.upload');
    
    Route::get('/events/{eventId}/ranking', [EvaluationController::class, 'ranking'])->name('events.ranking');
    Route::get('/events/{eventId}/certificates/{teamId}', [EvaluationController::class, 'certificate'])->name('events.certificate');
    Route::get('/events/{eventId}/certificates/{teamId}/download', [EvaluationController::class, 'downloadCertificate'])->name('events.certificate.download');

    Route::post('/events/{eventId}/certificates/{teamId}/email', [EvaluationController::class, 'emailCertificate'])->name('events.certificate.email');
    
    Route::get('/my-feedback', [EvaluationController::class, 'myFeedback'])->name('my.feedback');
});

Route::middleware(['auth', 'role:juez'])->group(function () {
    Route::get('/events/{eventId}/evaluate', [EvaluationController::class, 'show'])->name('events.evaluate.show');
    Route::get('/events/{eventId}/evaluate/{teamId}', [EvaluationController::class, 'evaluateTeam'])->name('events.evaluate.team');
    Route::post('/events/{eventId}/evaluate', [EvaluationController::class, 'store'])->name('events.evaluate.store');
    Route::post('/events/{eventId}/evaluate/{teamId}/conflict', [EvaluationController::class, 'declareConflict'])->name('events.evaluate.conflict');
    Route::post('/events/{eventId}/evaluate/{teamId}/finalize', [EvaluationController::class, 'finalize'])->name('events.evaluate.finalize');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::post('/admin/judges', [AdminController::class, 'createJudge'])->name('admin.judges.create');
    Route::post('/events/{eventId}/judges', [AdminController::class, 'assignJudge'])->name('events.judges.assign');
    Route::delete('/events/{eventId}/judges/{userId}', [AdminController::class, 'removeJudge'])->name('events.judges.remove');
    Route::put('/events/{eventId}/status', [EventController::class, 'changeStatus'])->name('events.status.update');
    Route::get('/admin/teams', [AdminController::class, 'teams'])->name('admin.teams');
    
    Route::get('/events/{eventId}/reports/pdf', [\App\Http\Controllers\ReportController::class, 'generatePdfReport'])->name('events.reports.pdf');
    Route::get('/events/{eventId}/reports/csv', [\App\Http\Controllers\ReportController::class, 'generateCsvReport'])->name('events.reports.csv');
    Route::post('/events/{eventId}/announcement', [\App\Http\Controllers\EventController::class, 'sendAnnouncement'])->name('events.announcement.send');
    Route::post('/events/{eventId}/reports/email', [\App\Http\Controllers\ReportController::class, 'emailReport'])->name('events.reports.email');

    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::put('/admin/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');

    Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::post('/admin/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
    Route::get('/admin/logs', [AuditLogController::class, 'index'])->name('admin.logs');
    
    Route::post('/admin/instituciones', [\App\Http\Controllers\AdminDataController::class, 'storeInstitution'])->name('admin.instituciones.store');
    Route::put('/admin/instituciones/{id}', [\App\Http\Controllers\AdminDataController::class, 'updateInstitution'])->name('admin.instituciones.update');
    Route::delete('/admin/instituciones/{id}', [\App\Http\Controllers\AdminDataController::class, 'destroyInstitution'])->name('admin.instituciones.destroy');
    
    Route::post('/admin/carreras', [\App\Http\Controllers\AdminDataController::class, 'storeCareer'])->name('admin.carreras.store');
    Route::put('/admin/carreras/{id}', [\App\Http\Controllers\AdminDataController::class, 'updateCareer'])->name('admin.carreras.update');
    Route::delete('/admin/carreras/{id}', [\App\Http\Controllers\AdminDataController::class, 'destroyCareer'])->name('admin.carreras.destroy');
    
    Route::post('/admin/evaluations/{id}/unlock', [EvaluationController::class, 'unlock'])->name('admin.evaluations.unlock');
});

Route::get('/teams/{id}', function () {
    abort(404);
});

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});