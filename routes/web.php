<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\SchoolYearController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Student\AuthController as StudentAuthController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\EnrollController as StudentEnrollController;
use Illuminate\Support\Facades\Route;

// Root — go to student portal by default
Route::get('/', fn () => redirect()->route('student.login'));

// ── Admin Auth ─────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

    // ── Protected Admin Routes ──────────────────────────────────
    Route::middleware(\App\Http\Middleware\AdminAuth::class)->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Enrollments
        Route::get('/enrollments',                         [EnrollmentController::class, 'index'])->name('enrollments.index');
        Route::get('/enrollments/create',                  [EnrollmentController::class, 'create'])->name('enrollments.create');
        Route::post('/enrollments',                        [EnrollmentController::class, 'store'])->name('enrollments.store');
        Route::get('/enrollments/{enrollment}',            [EnrollmentController::class, 'show'])->name('enrollments.show');
        Route::patch('/enrollments/{enrollment}/status',   [EnrollmentController::class, 'updateStatus'])->name('enrollments.updateStatus');

        // AJAX: sections by grade/year/strand
        Route::get('/api/sections', [EnrollmentController::class, 'sections'])->name('api.sections');

        // Students
        Route::get('/students',              [StudentController::class, 'index'])->name('students.index');
        Route::get('/students/{student}',    [StudentController::class, 'show'])->name('students.show');
        Route::get('/students/{student}/edit',[StudentController::class, 'edit'])->name('students.edit');
        Route::put('/students/{student}',    [StudentController::class, 'update'])->name('students.update');

        // Sections
        Route::get('/sections',             [SectionController::class, 'index'])->name('sections.index');
        Route::post('/sections',            [SectionController::class, 'store'])->name('sections.store');
        Route::put('/sections/{section}',   [SectionController::class, 'update'])->name('sections.update');
        Route::delete('/sections/{section}',[SectionController::class, 'destroy'])->name('sections.destroy');

        // School Years
        Route::get('/school-years',                          [SchoolYearController::class, 'index'])->name('school-years.index');
        Route::post('/school-years',                         [SchoolYearController::class, 'store'])->name('school-years.store');
        Route::patch('/school-years/{schoolYear}/activate',  [SchoolYearController::class, 'activate'])->name('school-years.activate');
    });
});

// ── Student Portal ──────────────────────────────────────────────
Route::prefix('student')->name('student.')->group(function () {
    Route::get('/',       fn () => redirect()->route('student.login'));
    Route::get('/login',  [StudentAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [StudentAuthController::class, 'login'])->name('login.post');
    Route::post('/logout',[StudentAuthController::class, 'logout'])->name('logout');

    // Public: Self-enrollment (new students)
    Route::get('/enroll',       [StudentEnrollController::class, 'showEnroll'])->name('enroll');
    Route::post('/enroll',      [StudentEnrollController::class, 'enroll'])->name('enroll.post');
    Route::get('/api/sections', [StudentEnrollController::class, 'sections'])->name('enroll.sections');

    // Public: Create account (existing students)
    Route::get('/register',  [StudentEnrollController::class, 'showRegister'])->name('register');
    Route::post('/register', [StudentEnrollController::class, 'register'])->name('register.post');

    Route::middleware(\App\Http\Middleware\StudentAuth::class)->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile',   [StudentDashboardController::class, 'profile'])->name('profile');
    });
});
