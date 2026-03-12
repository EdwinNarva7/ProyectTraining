<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\CertificateController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Apprentice\DashboardController as ApprenticeDashboardController;
use App\Http\Controllers\Apprentice\AttendanceController as ApprenticeAttendanceController;
use App\Http\Controllers\Apprentice\CertificateController as ApprenticeCertificateController;
use App\Http\Controllers\Apprentice\PenaltyController as ApprenticePenaltyController;
use App\Http\Controllers\Apprentice\ScheduleController as ApprenticeScheduleController;
use App\Http\Controllers\Apprentice\RecoveryController as ApprenticeRecoveryController;
use App\Http\Controllers\Admin\PenaltyController as AdminPenaltyController;
use App\Http\Controllers\Admin\RecoverySessionController;
use App\Http\Controllers\Admin\FingerprintController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas del Panel de Aprendices
Route::middleware(['auth', 'verified'])->prefix('aprendiz')->name('apprentice.')->group(function () {
    // Dashboard
    Route::get('/', [ApprenticeDashboardController::class, 'index'])->name('dashboard');

    // Gestión de Asistencia
    Route::get('attendance', [ApprenticeAttendanceController::class, 'index'])->name('attendance.index');
    Route::get('attendance/logs', [ApprenticeAttendanceController::class, 'logs'])->name('attendance.logs');
    Route::get('attendance/sessions', [ApprenticeAttendanceController::class, 'sessions'])->name('attendance.sessions');

    // Registro de entrada/salida por el aprendiz
    Route::post('attendance/entry', [ApprenticeAttendanceController::class, 'registerEntry'])->name('attendance.entry');
    Route::post('attendance/exit', [ApprenticeAttendanceController::class, 'registerExit'])->name('attendance.exit');

    // Gestión de Horarios
    Route::get('schedules', [ApprenticeScheduleController::class, 'index'])->name('schedules.index');

    // Gestión de Certificados
    Route::get('certificates', [ApprenticeCertificateController::class, 'index'])->name('certificates.index');
    Route::get('certificates/{certificate}', [ApprenticeCertificateController::class, 'show'])->name('certificates.show');
    Route::get('certificates/{certificate}/download', [ApprenticeCertificateController::class, 'download'])->name('certificates.download');

    // Módulo de Recuperación de Horas
    Route::get('penalties', [ApprenticePenaltyController::class, 'index'])->name('penalties.index');
    Route::get('requests', [ApprenticePenaltyController::class, 'requests'])->name('penalties.requests');
    // Recuperación
    Route::get('recovery', [ApprenticeRecoveryController::class, 'index'])->name('recovery.index');
    Route::post('recovery/{session}/start', [ApprenticeRecoveryController::class, 'startSession'])->name('recovery.start');
    Route::get('recovery/{session}', [ApprenticeRecoveryController::class, 'show'])->name('recovery.show');
    Route::post('recovery/{session}/end', [ApprenticeRecoveryController::class, 'endSession'])->name('recovery.end');
    Route::get('penalties/{penalty}', [ApprenticePenaltyController::class, 'show'])->name('penalties.show');
    Route::post('penalties/{penalty}/request-recovery', [ApprenticePenaltyController::class, 'requestRecovery'])->name('penalties.request-recovery');
});

// Rutas del Panel de Administración
Route::middleware(['auth', 'verified', 'admin.only'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Gestión de Usuarios
    Route::resource('users', UserController::class);

    // Gestión de Horarios
    Route::resource('schedules', ScheduleController::class);
    Route::get('schedules/apprentice/{apprentice_id}', [ScheduleController::class, 'getApprenticeSchedules'])->name('schedules.apprentice');
    Route::post('schedules/{schedule}/toggle-status', [ScheduleController::class, 'toggleStatus'])->name('schedules.toggle-status');

    // Gestión de Asistencia
    Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('attendance/logs', [AttendanceController::class, 'logs'])->name('attendance.logs');
    Route::get('attendance/sessions', [AttendanceController::class, 'sessions'])->name('attendance.sessions');
    Route::get('attendance/detailed-report', [AttendanceController::class, 'detailedReport'])->name('attendance.detailed-report');
    Route::get('attendance/weekly-data', [AttendanceController::class, 'weeklyData'])->name('attendance.weekly-data');

    // Registro manual de asistencia
    Route::post('attendance/entry', [AttendanceController::class, 'registerEntry'])->name('attendance.entry');
    Route::post('attendance/exit', [AttendanceController::class, 'registerExit'])->name('attendance.exit');
    Route::post('attendance/sessions/{session}/close', [AttendanceController::class, 'closeSession'])->name('attendance.close-session');

    // Gestión de Certificados
    Route::resource('certificates', CertificateController::class);
    Route::post('certificates/{certificate}/generate', [CertificateController::class, 'generate'])->name('certificates.generate');
    Route::post('certificates/{certificate}/send', [CertificateController::class, 'send'])->name('certificates.send');
    Route::get('certificates/{certificate}/download', [CertificateController::class, 'download'])->name('certificates.download');

    // Reportes
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/attendance', [ReportController::class, 'attendance'])->name('reports.attendance');
    Route::get('reports/certificates', [ReportController::class, 'certificates'])->name('reports.certificates');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Módulo de Recuperación de Horas
    Route::get('penalties', [AdminPenaltyController::class, 'index'])->name('penalties.index');
    Route::get('recovery-requests', [AdminPenaltyController::class, 'recoveryRequests'])->name('recovery-requests.index');
    Route::post('recovery-requests/{recoveryRequest}/approve', [AdminPenaltyController::class, 'approveRecovery'])->name('recovery-requests.approve');
    Route::post('recovery-requests/{recoveryRequest}/reject', [AdminPenaltyController::class, 'rejectRecovery'])->name('recovery-requests.reject');

    Route::get('recovery-sessions', [RecoverySessionController::class, 'index'])->name('recovery-sessions.index');
    Route::post('recovery-sessions/{session}/close', [RecoverySessionController::class, 'close'])->name('recovery-sessions.close');

    // ── Módulo de Huella Digital ────────────────────────────────────────────
    Route::get('fingerprint/scanner', [FingerprintController::class, 'scannerPanel'])->name('fingerprint.scanner');
    Route::get('fingerprint/enroll', [FingerprintController::class, 'enrollPanel'])->name('fingerprint.enroll');
    // API interna (llamadas desde el Bridge y el JS del navegador)
    Route::post('fingerprint/enroll', [FingerprintController::class, 'enroll'])->name('fingerprint.enroll.save');
    Route::delete('fingerprint/{apprentice_id}', [FingerprintController::class, 'deleteEnrollment'])->name('fingerprint.delete');
    Route::get('fingerprint/templates', [FingerprintController::class, 'getTemplates'])->name('fingerprint.templates');
    Route::post('fingerprint/mark', [FingerprintController::class, 'mark'])->name('fingerprint.mark');
});

require __DIR__ . '/auth.php';
