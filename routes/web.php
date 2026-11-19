<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DocumentRequestController as AdminDocumentRequestController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\FeeScheduleController;
use App\Http\Controllers\Admin\GradeController as AdminGradeController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SchoolYearController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Student\AuthController as StudentAuthController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\DocumentRequestController as StudentDocumentRequestController;
use App\Http\Controllers\Student\EnrollController as StudentEnrollController;
use App\Http\Controllers\Student\ExamController;
use App\Http\Controllers\Student\GradeController;
use App\Http\Controllers\Student\PaymentController;
use App\Http\Controllers\Student\ProfileEditController;
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
        Route::get('/students',               [StudentController::class, 'index'])->name('students.index');
        Route::get('/students/{student}',     [StudentController::class, 'show'])->name('students.show');
        Route::get('/students/{student}/edit',[StudentController::class, 'edit'])->name('students.edit');
        Route::put('/students/{student}',     [StudentController::class, 'update'])->name('students.update');

        // Sections
        Route::get('/sections',             [SectionController::class, 'index'])->name('sections.index');
        Route::post('/sections',            [SectionController::class, 'store'])->name('sections.store');
        Route::put('/sections/{section}',   [SectionController::class, 'update'])->name('sections.update');
        Route::delete('/sections/{section}',[SectionController::class, 'destroy'])->name('sections.destroy');

        // School Years
        Route::get('/school-years',                          [SchoolYearController::class, 'index'])->name('school-years.index');
        Route::post('/school-years',                         [SchoolYearController::class, 'store'])->name('school-years.store');
        Route::patch('/school-years/{schoolYear}/activate',  [SchoolYearController::class, 'activate'])->name('school-years.activate');

        // Payments & Fees
        Route::get('/payments',                              [AdminPaymentController::class, 'index'])->name('payments.index');
        Route::patch('/payments/{payment}/mark-paid',        [AdminPaymentController::class, 'markPaid'])->name('payments.markPaid');
        Route::get('/fees',                                  [FeeScheduleController::class, 'index'])->name('fees.index');
        Route::post('/fees',                                 [FeeScheduleController::class, 'store'])->name('fees.store');
        Route::post('/fees/bulk',                            [FeeScheduleController::class, 'bulkStore'])->name('fees.bulk');

        // Grades
        Route::get('/grades',                                [AdminGradeController::class, 'index'])->name('grades.index');
        Route::get('/grades/{enrollment}',                   [AdminGradeController::class, 'show'])->name('grades.show');
        Route::post('/grades/{enrollment}',                  [AdminGradeController::class, 'store'])->name('grades.store');
        Route::patch('/grades/{enrollment}/publish',         [AdminGradeController::class, 'publishAll'])->name('grades.publish');

        // ── NEW FEATURES ────────────────────────────────────────

        // Admin User Management (super_admin only)
        Route::get('/users',              [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create',       [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users',             [AdminUserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit',  [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}',       [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}',    [AdminUserController::class, 'destroy'])->name('users.destroy');

        // Announcements
        Route::get('/announcements',                     [AnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('/announcements/create',              [AnnouncementController::class, 'create'])->name('announcements.create');
        Route::post('/announcements',                    [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::get('/announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');
        Route::put('/announcements/{announcement}',      [AnnouncementController::class, 'update'])->name('announcements.update');
        Route::delete('/announcements/{announcement}',   [AnnouncementController::class, 'destroy'])->name('announcements.destroy');

        // Document Requests
        Route::get('/document-requests',                            [AdminDocumentRequestController::class, 'index'])->name('document-requests.index');
        Route::patch('/document-requests/{documentRequest}/status', [AdminDocumentRequestController::class, 'updateStatus'])->name('document-requests.updateStatus');

        // Reports
        Route::get('/reports/enrollments',              [ReportController::class, 'enrollmentReport'])->name('reports.enrollments');
        Route::get('/reports/payments',                 [ReportController::class, 'paymentReport'])->name('reports.payments');
        Route::get('/reports/report-card/{enrollment}', [ReportController::class, 'reportCard'])->name('reports.report-card');

        // Activity Log
        Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');

        // System Settings
        Route::get('/settings',       [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings',      [SettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/logo', [SettingController::class, 'uploadLogo'])->name('settings.logo');
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

        // Payments
        Route::get('/payments',                              [PaymentController::class, 'index'])->name('payments.index');
        Route::post('/payments/{quarter}/pay',               [PaymentController::class, 'pay'])->name('payments.pay');
        Route::get('/payments/checkout/{payment}',           [PaymentController::class, 'checkout'])->name('payments.checkout');
        Route::post('/payments/checkout/{payment}',          [PaymentController::class, 'processCheckout'])->name('payments.process');
        Route::get('/payments/callback/{payment}',           [PaymentController::class, 'callback'])->name('payments.callback');

        // Grades
        Route::get('/grades', [GradeController::class, 'index'])->name('grades.index');

        // Examinations
        Route::get('/exams', [ExamController::class, 'index'])->name('exams.index');

        // ── NEW FEATURES ────────────────────────────────────────

        // Online Re-enrollment (for Regular / Returning students)
        Route::get('/re-enroll',         [StudentEnrollController::class, 'showReEnroll'])->name('re-enroll');
        Route::post('/re-enroll',        [StudentEnrollController::class, 'reEnroll'])->name('re-enroll.post');

        // Profile self-edit & password change
        Route::get('/profile/edit',      [ProfileEditController::class, 'edit'])->name('profile.edit');
        Route::put('/profile/edit',      [ProfileEditController::class, 'update'])->name('profile.update');
        Route::post('/profile/password', [ProfileEditController::class, 'changePassword'])->name('profile.password');

        // Enrollment Certificate
        Route::get('/certificate', [ProfileEditController::class, 'certificate'])->name('certificate');

        // Document Requests
        Route::get('/document-requests',  [StudentDocumentRequestController::class, 'index'])->name('document-requests.index');
        Route::post('/document-requests', [StudentDocumentRequestController::class, 'store'])->name('document-requests.store');
    });
});
