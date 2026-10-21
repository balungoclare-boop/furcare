<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForcePasswordController;
use App\Http\Controllers\HospitalizationController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VaccinationController;
use App\Models\Announcement;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $schemaReady = Schema::hasTable('announcements')
        && Schema::hasColumns('announcements', ['is_pinned', 'priority', 'publish_at', 'expires_at']);

    if (! $schemaReady) {
        return view('welcome', [
            'latestAnnouncements' => collect(),
            'allAnnouncements' => collect(),
        ]);
    }

    $baseQuery = Announcement::query()
        ->with('creator:id,name')
        ->visibleOnWelcome()
        ->orderedForWelcome();

    $latestAnnouncements = (clone $baseQuery)
        ->take(5)
        ->get();

    $allAnnouncements = (clone $baseQuery)
        ->get();

    return view('welcome', compact('latestAnnouncements', 'allAnnouncements'));
})->name('home');

Route::middleware(['preventBackHistory', 'auth'])->group(function (): void {
    Route::get('/force-password-change', [ForcePasswordController::class, 'edit'])
        ->name('password.force.edit');
    Route::put('/force-password-change', [ForcePasswordController::class, 'update'])
        ->name('password.force.update');

    Route::get('/dashboard', [DashboardController::class, 'redirectByRole'])->name('dashboard');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'read'])
        ->name('notifications.read');

    Route::prefix('admin')
        ->name('admin.')
        ->middleware('role:admin')
        ->group(function (): void {
            Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
            Route::resource('users', UserController::class)->except(['show', 'destroy']);
            Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        });

    Route::prefix('staff')
        ->name('staff.')
        ->middleware('role:veterinary_staff')
        ->group(function (): void {
            Route::get('/dashboard', [DashboardController::class, 'staff'])->name('dashboard');
        });

    Route::prefix('client')
        ->name('client.')
        ->middleware('role:client')
        ->group(function (): void {
            Route::get('/dashboard', [DashboardController::class, 'client'])->name('dashboard');
        });

    Route::middleware('role:admin|veterinary_staff|client')
        ->group(function (): void {
            Route::get('appointments/availability', [AppointmentController::class, 'availability'])
                ->name('appointments.availability');
            Route::resource('pets', PetController::class)->except('destroy');
            Route::resource('appointments', AppointmentController::class)->except(['show', 'destroy']);
            Route::get('appointments/completed', [AppointmentController::class, 'completed'])
                ->name('appointments.completed');
            Route::get('appointments/accepted', [AppointmentController::class, 'accepted'])
                ->name('appointments.accepted');
            Route::match(['post', 'patch'], 'appointments/{appointment}/approve', [AppointmentController::class, 'approve'])
                ->name('appointments.approve');
            Route::match(['post', 'patch'], 'appointments/{appointment}/complete', [AppointmentController::class, 'complete'])
                ->name('appointments.complete');
            Route::match(['post', 'patch'], 'appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])
                ->name('appointments.cancel');

            Route::get('vaccinations', [VaccinationController::class, 'index'])
                ->name('vaccinations.index');
            Route::get('vaccinations/overdue', [VaccinationController::class, 'overdue'])
                ->name('vaccinations.overdue');
        });

    Route::middleware('role:client')
        ->group(function (): void {
            Route::get('appointments/grooming/create', [AppointmentController::class, 'createGrooming'])
                ->name('appointments.grooming.create');
        });

    Route::middleware('role:admin|veterinary_staff')
        ->group(function (): void {
            Route::resource('hospitalizations', HospitalizationController::class)->except('destroy');
            Route::post('hospitalizations/{hospitalization}/progress-notes', [HospitalizationController::class, 'storeProgressNote'])
                ->name('hospitalizations.progress-notes.store');
            Route::resource('medicines', MedicineController::class)->except('destroy');
            Route::resource('announcements', AnnouncementController::class)
                ->except(['show', 'destroy']);

            Route::resource('vaccinations', VaccinationController::class)
                ->except(['index', 'destroy']);

            Route::get('reports/print', [ReportController::class, 'generateReport'])
                ->name('reports.print');
            Route::resource('reports', ReportController::class)->except('destroy');

            Route::post('medical-records', [MedicalRecordController::class, 'store'])
                ->name('medical-records.store');
        });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';
