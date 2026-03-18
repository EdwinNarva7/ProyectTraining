<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Phase;
use App\Models\AttendanceSession;
use App\Models\RecoverySession;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HoursReportController extends Controller
{
    /**
     * Reporte para el Administrador
     */
    public function adminIndex(Request $request)
    {
        $activePhase = Phase::where('is_active', true)->first();
        $selectedPhaseId = $request->input('phase_id', $activePhase?->id);

        $apprentices = User::whereHas('role', function ($q) {
                $q->where('name', 'Aprendiz');
            })
            ->when($selectedPhaseId, function ($query) use ($selectedPhaseId) {
                return $query->whereHas('apprenticeProfile', function ($p) use ($selectedPhaseId) {
                    $p->where('phase_id', $selectedPhaseId);
                });
            })
            ->get()
            ->map(function ($user) use ($selectedPhaseId) {
                $user->total_minutes = $user->getTotalWorkedMinutes($selectedPhaseId);
                $user->weekly_minutes = $user->getWeeklyWorkedMinutes();
                $user->daily_minutes = $user->getDailyWorkedMinutes();
                $user->recovered_minutes = $user->getTotalRecoveredMinutes();
                return $user;
            })
            ->sortByDesc('total_minutes');

        $phases = Phase::all();

        return view('admin.reports.hours', compact('apprentices', 'phases', 'activePhase', 'selectedPhaseId'));
    }

    /**
     * Vista de Progreso para el Aprendiz
     */
    public function apprenticeIndex()
    {
        $user = Auth::user();
        $activePhase = $user->apprenticeProfile?->phase;
        
        $stats = [
            'total_hours' => $user->formatMinutesToHours($user->getTotalWorkedMinutes($activePhase?->id)),
            'weekly_hours' => $user->formatMinutesToHours($user->getWeeklyWorkedMinutes()),
            'daily_hours' => $user->formatMinutesToHours($user->getDailyWorkedMinutes()),
            'recovered_hours' => $user->formatMinutesToHours($user->getTotalRecoveredMinutes()),
            'total_minutes' => $user->getTotalWorkedMinutes($activePhase?->id),
        ];

        // Meta semanal (ejemplo 40 horas = 2400 minutos)
        $weeklyMetaMinutes = 2400; 
        $weeklyProgress = min(100, round(($user->getWeeklyWorkedMinutes() / $weeklyMetaMinutes) * 100));

        // Obtener historial combinado (Asistencia regular + Recuperación)
        $attendanceHistory = AttendanceSession::where('apprentice_id', $user->id)
            ->where('start_at', '>=', Carbon::now()->subWeeks(2))
            ->get();

        $recoveryHistory = RecoverySession::where('apprentice_id', $user->id)
            ->where('status', 'completed')
            ->where('date', '>=', Carbon::now()->subWeeks(2))
            ->get();

        // Combinar ambas listas mapeándolas a una estructura común
        $history = collect()
            ->concat($attendanceHistory->map(fn($item) => (object)['date' => Carbon::parse($item->start_at)->format('Y-m-d'), 'duration_minutes' => $item->duration_minutes]))
            ->concat($recoveryHistory->map(fn($item) => (object)['date' => Carbon::parse($item->date)->format('Y-m-d'), 'duration_minutes' => $item->duration_minutes]))
            ->groupBy('date')
            ->map(function($day) {
                return $day->sum('duration_minutes') / 60;
            })
            ->sortKeys();

        return view('aprendiz.reports.hours', compact('stats', 'weeklyProgress', 'history', 'activePhase'));
    }
}
