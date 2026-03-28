<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\AttendanceSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display the attendance dashboard.
     */
    public function index()
    {
        // Estadísticas generales
        $totalApprentices = User::whereHas('role', function ($q) {
            $q->where('name', 'Aprendiz');
        })->where('status', 'activo')->count();

        // Registros de hoy
        $today = Carbon::today();
        $presentToday = AttendanceLog::where('event_type', 'entrada')
            ->whereDate('occurred_at', $today)
            ->distinct('apprentice_id')
            ->count('apprentice_id');

        $absentToday = $totalApprentices - $presentToday;

        // Horas acumuladas
        $totalHours = AttendanceSession::whereNotNull('end_at')
            ->sum('duration_minutes') / 60;

        // Porcentaje de asistencia
        $attendanceRate = $totalApprentices > 0 ? round(($presentToday / $totalApprentices) * 100, 1) : 0;

        // Tardanzas hoy
        $lateToday = AttendanceLog::where('event_type', 'entrada')
            ->whereDate('occurred_at', $today)
            ->whereTime('occurred_at', '>', '08:00:00')
            ->count();

        // Últimos registros de asistencia
        $recentLogs = AttendanceLog::with('apprentice')
            ->orderBy('occurred_at', 'desc')
            ->limit(10)
            ->get();

        // Sesiones activas
        $activeSessions = AttendanceSession::with('apprentice')
            ->whereNull('end_at')
            ->orderBy('start_at', 'desc')
            ->get();

        $phases = \App\Models\Phase::orderBy('is_active', 'desc')->get();

        return view('admin.attendance.index', compact(
            'totalApprentices',
            'presentToday',
            'absentToday',
            'totalHours',
            'attendanceRate',
            'lateToday',
            'recentLogs',
            'activeSessions',
            'phases'
        ));
    }

    /**
     * Weekly attendance data (last 7 days) for chart
     */
    public function weeklyData()
    {
        $totalApprentices = User::whereHas('role', function ($q) {
            $q->where('name', 'Aprendiz');
        })->where('status', 'activo')->count();

        $weekdays = [
            1 => 'Lun', 2 => 'Mar', 3 => 'Mié', 4 => 'Jue', 5 => 'Vie', 6 => 'Sáb', 7 => 'Dom'
        ];

        $labels = [];
        $present = [];
        $absent = [];
        $rates = [];

        $weekData = AttendanceLog::where('event_type', 'entrada')
            ->where('occurred_at', '>=', Carbon::today()->subDays(6))
            ->select(DB::raw('DATE(occurred_at) as date'), DB::raw('COUNT(DISTINCT apprentice_id) as present_count'))
            ->groupBy('date')
            ->get()
            ->pluck('present_count', 'date');

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateString = $date->toDateString();
            $weekday = $date->dayOfWeek === 0 ? 7 : $date->dayOfWeek;
            $labels[] = $weekdays[$weekday] ?? $date->format('D');

            $presentCount = $weekData[$dateString] ?? 0;
            $absentCount = max(0, $totalApprentices - $presentCount);
            $rate = $totalApprentices > 0 ? round(($presentCount / $totalApprentices) * 100, 1) : 0;

            $present[] = $presentCount;
            $absent[] = $absentCount;
            $rates[] = $rate;
        }

        return response()->json([
            'success' => true,
            'labels' => $labels,
            'present' => $present,
            'absent' => $absent,
            'rates' => $rates,
            'total_apprentices' => $totalApprentices
        ]);
    }


    /**
     * Display attendance sessions.
     */
    public function sessions(Request $request)
    {
        $query = AttendanceSession::with('apprentice');

        // Filtros
        if ($request->filled('apprentice_id')) {
            $query->where('apprentice_id', $request->apprentice_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'completed') {
                $query->whereNotNull('end_at');
            } elseif ($request->status === 'active') {
                $query->whereNull('end_at');
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('start_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('start_at', '<=', $request->date_to);
        }

        $sessions = $query->orderBy('start_at', 'desc')->paginate(20);

        // Estadísticas
        $totalSessions = AttendanceSession::count();
        $completedSessions = AttendanceSession::whereNotNull('end_at')->count();
        $activeSessions = AttendanceSession::whereNull('end_at')->count();
        $totalHours = AttendanceSession::whereNotNull('end_at')->sum('duration_minutes') / 60;

        // Top aprendices por horas
        $topApprentices = User::whereHas('role', function ($q) {
            $q->where('name', 'Aprendiz');
        })->where('status', 'activo')
            ->withCount([
                'attendanceSessions as sessions_count' => function ($q) {
                    $q->whereNotNull('end_at');
                }
            ])
            ->withSum([
                'attendanceSessions as hours_total' => function ($q) {
                    $q->whereNotNull('end_at');
                }
            ], 'duration_minutes')
            ->orderByDesc('hours_total')
            ->limit(10)
            ->get()
            ->map(function ($user) {
                $user->hours_total = round($user->hours_total / 60, 2);
                return $user;
            });

        // Lista de aprendices para filtros
        $apprentices = User::whereHas('role', function ($q) {
            $q->where('name', 'Aprendiz');
        })->where('status', 'activo')->get();

        return view('admin.attendance.sessions', compact(
            'sessions',
            'totalSessions',
            'completedSessions',
            'activeSessions',
            'totalHours',
            'topApprentices',
            'apprentices'
        ));
    }

    /**
     * Registrar entrada manual.
     */
    public function registerEntry(Request $request)
    {
        $request->validate([
            'apprentice_id' => 'required|exists:users,id',
            'occurred_at' => 'required|date',
            'source' => 'nullable|string|max:40',
            'note' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Crear el log de entrada
            $log = AttendanceLog::create([
                'apprentice_id' => $request->apprentice_id,
                'event_type' => 'entrada',
                'occurred_at' => $request->occurred_at,
                'source' => $request->source ?? 'admin',
                'note' => $request->note,
                'created_by' => Auth::id(),
            ]);

            // Crear o actualizar sesión
            $existingSession = AttendanceSession::where('apprentice_id', $request->apprentice_id)
                ->whereNull('end_at')
                ->first();

            if (!$existingSession) {
                AttendanceSession::create([
                    'apprentice_id' => $request->apprentice_id,
                    'start_at' => $request->occurred_at,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Entrada registrada exitosamente',
                'log' => $log
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar entrada: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Registrar salida manual.
     */
    public function registerExit(Request $request)
    {
        $request->validate([
            'apprentice_id' => 'required|exists:users,id',
            'occurred_at' => 'required|date',
            'source' => 'nullable|string|max:40',
            'note' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Crear el log de salida
            $log = AttendanceLog::create([
                'apprentice_id' => $request->apprentice_id,
                'event_type' => 'salida',
                'occurred_at' => $request->occurred_at,
                'source' => $request->source ?? 'admin',
                'note' => $request->note,
                'created_by' => Auth::id(),
            ]);

            // Cerrar sesión activa
            $activeSession = AttendanceSession::where('apprentice_id', $request->apprentice_id)
                ->whereNull('end_at')
                ->first();

            if ($activeSession) {
                $startTime = Carbon::parse($activeSession->start_at);
                $endTime = Carbon::parse($request->occurred_at);
                $durationMinutes = $startTime->diffInMinutes($endTime);

                $activeSession->update([
                    'end_at' => $request->occurred_at,
                    'duration_minutes' => $durationMinutes,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Salida registrada exitosamente',
                'log' => $log
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar salida: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cerrar sesión manualmente.
     */
    public function closeSession(Request $request, AttendanceSession $session)
    {
        if ($session->end_at) {
            return response()->json([
                'success' => false,
                'message' => 'La sesión ya está cerrada'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $endTime = Carbon::now();
            $startTime = Carbon::parse($session->start_at);
            $durationMinutes = $startTime->diffInMinutes($endTime);

            $session->update([
                'end_at' => $endTime,
                'duration_minutes' => $durationMinutes,
            ]);

            // Crear log de salida automático
            AttendanceLog::create([
                'apprentice_id' => $session->apprentice_id,
                'event_type' => 'salida',
                'occurred_at' => $endTime,
                'source' => 'admin',
                'note' => 'Sesión cerrada manualmente por administrador',
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sesión cerrada exitosamente',
                'duration_hours' => round($durationMinutes / 60, 2)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar sesión: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reporte detallado de entradas y salidas
     */
    public function detailedReport(Request $request)
    {
        $query = AttendanceLog::with(['apprentice.apprenticeProfile', 'createdBy']);

        // Filtros
        if ($request->filled('date_from')) {
            $query->whereDate('occurred_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('occurred_at', '<=', $request->date_to);
        } else {
            // Por defecto, mostrar último mes
            $query->whereDate('occurred_at', '>=', Carbon::now()->subMonth());
        }

        if ($request->filled('apprentice_id')) {
            $query->where('apprentice_id', $request->apprentice_id);
        }

        if ($request->filled('cohort')) {
            $query->whereHas('apprentice.apprenticeProfile', function ($q) use ($request) {
                $q->where('cohort', 'LIKE', '%' . $request->cohort . '%');
            });
        }

        $logs = $query->orderBy('occurred_at', 'desc')->get();

        // Agrupar por aprendiz y día
        $groupedLogs = $logs->groupBy(function ($log) {
            return $log->apprentice_id . '-' . $log->occurred_at->format('Y-m-d');
        });

        $reportData = [];
        foreach ($groupedLogs as $key => $dayLogs) {
            $apprentice = $dayLogs->first()->apprentice;
            $date = $dayLogs->first()->occurred_at->format('Y-m-d');

            $entries = $dayLogs->where('event_type', 'entrada');
            $exits = $dayLogs->where('event_type', 'salida');

            $firstEntry = $entries->sortBy('occurred_at')->first();
            $lastExit = $exits->sortByDesc('occurred_at')->first();

            $hoursWorked = 0;
            if ($firstEntry && $lastExit) {
                $hoursWorked = $firstEntry->occurred_at->diffInMinutes($lastExit->occurred_at);
            }

            $reportData[] = [
                'apprentice' => $apprentice,
                'date' => $date,
                'first_entry' => $firstEntry,
                'last_exit' => $lastExit,
                'total_entries' => $entries->count(),
                'total_exits' => $exits->count(),
                'hours_worked' => $hoursWorked,
                'is_complete' => $entries->count() > 0 && $exits->count() > 0,
                'logs' => $dayLogs->sortBy('occurred_at')
            ];
        }

        // Estadísticas generales
        $stats = [
            'total_apprentices' => $logs->pluck('apprentice_id')->unique()->count(),
            'total_logs' => $logs->count(),
            'total_entries' => $logs->where('event_type', 'entrada')->count(),
            'total_exits' => $logs->where('event_type', 'salida')->count(),
            'total_hours' => collect($reportData)->sum('hours_worked'),
            'average_hours' => collect($reportData)->where('hours_worked', '>', 0)->avg('hours_worked'),
        ];

        // Lista de aprendices para filtros
        $apprentices = User::whereHas('role', function ($q) {
            $q->where('name', 'Aprendiz');
        })->where('status', 'activo')->get();

        // Lista de cohortes
        $cohorts = User::whereHas('role', function ($q) {
            $q->where('name', 'Aprendiz');
        })->whereHas('apprenticeProfile')
            ->with('apprenticeProfile')
            ->get()
            ->pluck('apprenticeProfile.cohort')
            ->filter()
            ->unique()
            ->values();

        return view('admin.attendance.detailed-report', compact(
            'reportData',
            'stats',
            'apprentices',
            'cohorts'
        ));
    }
}
