<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AttendanceLog;
use App\Models\Certificate;
use App\Models\AttendanceSession;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas generales
        $stats = [
            'total_users' => User::count(),
            'total_apprentices' => User::whereHas('role', function($query) {
                $query->where('name', 'Aprendiz');
            })->count(),
            'active_apprentices' => User::whereHas('role', function($query) {
                $query->where('name', 'Aprendiz');
            })->where('status', 'activo')->count(),
            'total_certificates' => Certificate::count(),
            'pending_certificates' => Certificate::where('status', 'generado')->count(),
        ];

        // Asistencia de hoy
        $today = Carbon::today();
        $todayAttendance = [
            'entries' => AttendanceLog::where('event_type', 'entrada')
                ->whereDate('occurred_at', $today)
                ->count(),
            'exits' => AttendanceLog::where('event_type', 'salida')
                ->whereDate('occurred_at', $today)
                ->count(),
            'active_sessions' => AttendanceSession::whereNull('end_at')->count(),
        ];

        // Últimos registros de asistencia
        $recentAttendance = AttendanceLog::with(['apprentice'])
            ->orderBy('occurred_at', 'desc')
            ->limit(10)
            ->get();

        // Certificados recientes
        $recentCertificates = Certificate::with(['apprentice'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'todayAttendance',
            'recentAttendance',
            'recentCertificates'
        ));
    }
}
