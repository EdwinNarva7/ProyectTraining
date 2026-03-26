<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AttendanceLog;
use App\Models\Certificate;
use App\Models\AttendanceSession;
use App\Models\Phase;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Obtener la fase activa
        $activePhase = Phase::where('is_active', true)->first();
        $activePhaseId = $activePhase ? $activePhase->id : null;

        // Estadísticas generales (filtradas por fase activa si existe)
        $stats = [
            'total_users' => User::count(),
            'total_apprentices' => User::whereHas('role', function($query) {
                $query->where('name', 'Aprendiz');
            })->when($activePhaseId, function($q) use ($activePhaseId) {
                $q->whereHas('apprenticeProfile', function($p) use ($activePhaseId) {
                    $p->where('phase_id', $activePhaseId);
                });
            })->count(),
            'active_apprentices' => User::whereHas('role', function($query) {
                $query->where('name', 'Aprendiz');
            })->where('status', 'activo')
            ->when($activePhaseId, function($q) use ($activePhaseId) {
                $q->whereHas('apprenticeProfile', function($p) use ($activePhaseId) {
                    $p->where('phase_id', $activePhaseId);
                });
            })->count(),
            'total_certificates' => Certificate::when($activePhaseId, function($q) use ($activePhaseId) {
                $q->whereHas('apprentice.apprenticeProfile', function($p) use ($activePhaseId) {
                    $p->where('phase_id', $activePhaseId);
                });
            })->count(),
            'pending_certificates' => Certificate::where('status', 'generado')
            ->when($activePhaseId, function($q) use ($activePhaseId) {
                $q->whereHas('apprentice.apprenticeProfile', function($p) use ($activePhaseId) {
                    $p->where('phase_id', $activePhaseId);
                });
            })->count(),
        ];

        // Asistencia de hoy (filtrada por fase activa)
        $today = Carbon::today();
        $todayAttendance = [
            'entries' => AttendanceLog::where('event_type', 'entrada')
                ->whereDate('occurred_at', $today)
                ->when($activePhaseId, function($q) use ($activePhaseId) {
                    $q->whereHas('apprentice.apprenticeProfile', function($p) use ($activePhaseId) {
                        $p->where('phase_id', $activePhaseId);
                    });
                })->count(),
            'exits' => AttendanceLog::where('event_type', 'salida')
                ->whereDate('occurred_at', $today)
                ->when($activePhaseId, function($q) use ($activePhaseId) {
                    $q->whereHas('apprentice.apprenticeProfile', function($p) use ($activePhaseId) {
                        $p->where('phase_id', $activePhaseId);
                    });
                })->count(),
            'active_sessions' => AttendanceSession::whereNull('end_at')
                ->when($activePhaseId, function($q) use ($activePhaseId) {
                    $q->whereHas('apprentice.apprenticeProfile', function($p) use ($activePhaseId) {
                        $p->where('phase_id', $activePhaseId);
                    });
                })->count(),
        ];

        // Últimos registros de asistencia (filtrados por fase activa)
        $recentAttendance = AttendanceLog::with(['apprentice'])
            ->when($activePhaseId, function($q) use ($activePhaseId) {
                $q->whereHas('apprentice.apprenticeProfile', function($p) use ($activePhaseId) {
                    $p->where('phase_id', $activePhaseId);
                });
            })
            ->orderBy('occurred_at', 'desc')
            ->limit(10)
            ->get();

        // Certificados recientes (filtrados por fase activa)
        $recentCertificates = Certificate::with(['apprentice'])
            ->when($activePhaseId, function($q) use ($activePhaseId) {
                $q->whereHas('apprentice.apprenticeProfile', function($p) use ($activePhaseId) {
                    $p->where('phase_id', $activePhaseId);
                });
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'todayAttendance',
            'recentAttendance',
            'recentCertificates',
            'activePhase'
        ));
    }
}
