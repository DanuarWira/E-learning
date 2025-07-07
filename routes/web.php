<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Superadmin\AdminDashboardController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\VocabularyController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\Supervisor\SpvDashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/modules/{module:slug}', [ModuleController::class, 'show'])
    ->middleware('auth')
    ->name('modules.show');

Route::get('/lessons/{lesson:slug}', [LessonController::class, 'show'])
    ->middleware('auth')
    ->name('lessons.show');

Route::post('/lessons/{lesson}/complete', [LessonController::class, 'markAsComplete'])
    ->middleware('auth')
    ->name('lessons.complete');

Route::get('/lessons/{material:slug}', [MaterialController::class, 'show'])
    ->middleware('auth')
    ->name('materials.show');

Route::post('/progress/store', [ProgressController::class, 'store'])
    ->middleware('auth')
    ->name('progress.store');

Route::get('/lessons/{lesson:slug}/practice/{vocabulary}', [LessonController::class, 'practice'])
    ->middleware('auth')
    ->name('lessons.practice');

Route::get('/lessons/{lesson:slug}/materials/{material}', [LessonController::class, 'material'])
    ->middleware('auth')
    ->name('lessons.material.show');

Route::get('/lessons/{lesson:slug}/exercise', [LessonController::class, 'practiceExercises'])
    ->middleware('auth')
    ->name('lessons.exercise.practice');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

Route::middleware(['auth', 'role:supervisor'])->prefix('supervisor')->name('supervisor.')->group(function () {
    Route::get('/dashboard', [SpvDashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {

    // Rute untuk dasbor
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('modules', ModuleController::class);

    Route::resource('lessons', LessonController::class);

    Route::resource('vocabularies', VocabularyController::class);

    Route::resource('materials', MaterialController::class);

    Route::resource('exercises', ExerciseController::class);
});
