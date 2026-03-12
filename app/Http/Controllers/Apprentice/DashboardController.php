<?php

namespace App\Http\Controllers\Apprentice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\AttendanceSession;
use App\Models\AttendanceLog;
use App\Models\Certificate;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Obtener estadísticas del aprendiz
        $totalHours = AttendanceSession::where('apprentice_id', $user->id)
            ->whereNotNull('end_at')
            ->sum('duration_minutes') / 60;
            
        $totalSessions = AttendanceSession::where('apprentice_id', $user->id)->count();
        $completedSessions = AttendanceSession::where('apprentice_id', $user->id)
            ->whereNotNull('end_at')->count();
            
        // Sesión activa actual
        $activeSession = AttendanceSession::where('apprentice_id', $user->id)
            ->whereNull('end_at')
            ->first();
            
        // Últimos registros de asistencia
        $recentLogs = AttendanceLog::where('apprentice_id', $user->id)
            ->orderBy('occurred_at', 'desc')
            ->limit(10)
            ->get();
            
        // Certificados del aprendiz
        $certificates = Certificate::where('apprentice_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        // Asistencia de hoy
        $today = Carbon::today();
        $todayEntry = AttendanceLog::where('apprentice_id', $user->id)
            ->where('event_type', 'entrada')
            ->whereDate('occurred_at', $today)
            ->first();
            
        $todayExit = AttendanceLog::where('apprentice_id', $user->id)
            ->where('event_type', 'salida')
            ->whereDate('occurred_at', $today)
            ->first();

        return view('aprendiz.dashboard', compact(
            'totalHours',
            'totalSessions',
            'completedSessions',
            'activeSession',
            'recentLogs',
            'certificates',
            'todayEntry',
            'todayExit'
        ));
    }
}
