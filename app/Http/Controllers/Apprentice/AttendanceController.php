<?php

namespace App\Http\Controllers\Apprentice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\AttendanceLog;
use App\Models\AttendanceSession;
use App\Models\Schedule;
use App\Models\Penalty;
use App\Models\RecoverySession;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Verificar si tiene una sesión activa que debería haberse cerrado
        $activeSession = AttendanceSession::where('apprentice_id', $user->id)
            ->whereNull('end_at')
            ->first();

        // Obtener horario del día actual y sesión de recuperación
        $todaySchedule = $this->getTodaySchedule($user->id);
        $recoverySession = RecoverySession::where('apprentice_id', $user->id)
            ->where('date', '=', Carbon::today()->toDateString())
            ->where('status', 'scheduled')
            ->first();

        if ($activeSession) {
            $scheduledEnd = null;
            $type = '';

            if ($todaySchedule) {
                $scheduledEnd = Carbon::today()->setTimeFrom($todaySchedule->end_time);
                $type = 'Cierre automático por fin de jornada';
            } elseif ($recoverySession) {
                $scheduledEnd = Carbon::parse($recoverySession->scheduled_end_time);
                $type = 'Cierre automático por fin de recuperación';
            }

            if ($scheduledEnd && Carbon::now()->gt($scheduledEnd)) {
                $this->processExit($user, $activeSession, $todaySchedule, $scheduledEnd, $type);
            }
        }

        // Obtener historial de asistencia
        $attendanceLogs = AttendanceLog::where('apprentice_id', $user->id)
            ->orderBy('occurred_at', 'desc')
            ->paginate(10);

        // Obtener sesiones de asistencia
        $sessions = AttendanceSession::where('apprentice_id', $user->id)
            ->orderBy('start_at', 'desc')
            ->paginate(10);

        // Estadísticas
        $totalHours = AttendanceSession::where('apprentice_id', $user->id)
            ->whereNotNull('end_at')
            ->sum('duration_minutes') / 60;

        $totalSessions = AttendanceSession::where('apprentice_id', $user->id)->count();
        $completedSessions = AttendanceSession::where('apprentice_id', $user->id)
            ->whereNotNull('end_at')->count();

        // Verificar estado actual de asistencia
        $currentStatus = $this->getCurrentAttendanceStatus($user->id);

        // Calcular duración programada en minutos para la UI
        $scheduledMinutes = 0;
        $scheduledStart = null;
        $scheduledEnd = null;

        if ($todaySchedule) {
            $scheduledMinutes = $todaySchedule->start_time->diffInMinutes($todaySchedule->end_time);
            $scheduledStart = Carbon::today()->setTimeFrom($todaySchedule->start_time);
            $scheduledEnd = Carbon::today()->setTimeFrom($todaySchedule->end_time);
        } elseif ($recoverySession) {
            $scheduledStart = Carbon::parse($recoverySession->scheduled_start_time);
            $scheduledEnd = Carbon::parse($recoverySession->scheduled_end_time);
            $scheduledMinutes = $scheduledStart->diffInMinutes($scheduledEnd);
        }

        // Calcular duración real asistida hoy
        $todaySessions = AttendanceSession::where('apprentice_id', $user->id)
            ->whereDate('start_at', Carbon::today())
            ->whereNotNull('end_at')
            ->get();

        $attendedMinutes = $todaySessions->sum('duration_minutes');

        // Calcular progreso y horas pendientes
        $progressPercent = 0;
        $pendingMinutes = 0;
        if ($scheduledMinutes > 0) {
            $progressPercent = min(100, ($attendedMinutes / $scheduledMinutes) * 100);
            $pendingMinutes = max(0, $scheduledMinutes - $attendedMinutes);
        }

        return view('aprendiz.attendance.index', compact(
            'attendanceLogs',
            'sessions',
            'totalHours',
            'totalSessions',
            'completedSessions',
            'currentStatus',
            'todaySchedule',
            'scheduledMinutes',
            'scheduledStart',
            'scheduledEnd',
            'attendedMinutes',
            'progressPercent',
            'pendingMinutes'
        ));
    }

    public function logs()
    {
        $user = Auth::user();

        $logs = AttendanceLog::where('apprentice_id', $user->id)
            ->orderBy('occurred_at', 'desc')
            ->paginate(30);

        return view('aprendiz.attendance.logs', compact('logs'));
    }

    public function sessions()
    {
        $user = Auth::user();

        $sessions = AttendanceSession::where('apprentice_id', $user->id)
            ->orderBy('start_at', 'desc')
            ->paginate(30);

        // Calcular progreso diario para cada sesión completada
        foreach ($sessions as $session) {
            if ($session->end_at) {
                $sessionDate = $session->start_at->toDateString();

                // Obtener horario del día de la sesión
                $weekday = $session->start_at->dayOfWeek;
                $weekday = $weekday === 0 ? 7 : $weekday;

                $daySchedule = Schedule::where('apprentice_id', $user->id)
                    ->where('weekday', $weekday)
                    ->where('status', 'activo')
                    ->first();

                if ($daySchedule) {
                    $scheduledMinutes = $daySchedule->start_time->diffInMinutes($daySchedule->end_time);

                    // Calcular todas las sesiones completadas de ese día hasta el momento de esta sesión
                    $daySessions = AttendanceSession::where('apprentice_id', $user->id)
                        ->whereDate('start_at', $sessionDate)
                        ->whereNotNull('end_at')
                        ->where('end_at', '<=', $session->end_at)
                        ->get();

                    $attendedMinutes = $daySessions->sum('duration_minutes');
                    $pendingMinutes = max(0, $scheduledMinutes - $attendedMinutes);
                    $progressPercent = min(100, ($attendedMinutes / $scheduledMinutes) * 100);

                    $session->daily_progress = [
                        'scheduled_hours' => intdiv($scheduledMinutes, 60),
                        'scheduled_minutes' => $scheduledMinutes % 60,
                        'attended_hours' => intdiv($attendedMinutes, 60),
                        'attended_minutes' => $attendedMinutes % 60,
                        'pending_hours' => intdiv($pendingMinutes, 60),
                        'pending_minutes' => $pendingMinutes % 60,
                        'progress_percent' => round($progressPercent, 1),
                        'has_pending' => $pendingMinutes > 0
                    ];
                } else {
                    $session->daily_progress = null;
                }
            } else {
                $session->daily_progress = null;
            }
        }

        return view('aprendiz.attendance.sessions', compact('sessions'));
    }

    /**
     * Registrar entrada del aprendiz
     */
    public function registerEntry(Request $request)
    {
        $user = Auth::user();

        try {
            DB::beginTransaction();

            // Verificar si tiene horario asignado para hoy
            $todaySchedule = $this->getTodaySchedule($user->id);
            $recoverySession = RecoverySession::where('apprentice_id', $user->id)
                ->where('date', '=', Carbon::today()->toDateString())
                ->where('status', 'scheduled')
                ->first();

            if (!$todaySchedule && !$recoverySession) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes un horario programado ni sesión de recuperación para hoy.'
                ], 400);
            }

            // Validar si está dentro del horario permitido
            $now = Carbon::now();

            if ($todaySchedule) {
                $scheduledStart = Carbon::today()->setTimeFrom($todaySchedule->start_time);
                $scheduledEnd = Carbon::today()->setTimeFrom($todaySchedule->end_time);

                if ($now->lt($scheduledStart->copy()->subMinutes(30))) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Aún es muy temprano. Tu horario empieza a las ' . $scheduledStart->format('H:i') . '.'
                    ], 400);
                }

                if ($now->gt($scheduledEnd)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tu horario de jornada para hoy ya finalizó (' . $scheduledEnd->format('H:i') . ').'
                    ], 400);
                }
            } elseif ($recoverySession) {
                // Validar hora de inicio programada por el administrador para la recuperación
                $scheduledStart = Carbon::today()->setTimeFrom($recoverySession->scheduled_start_time);
                $scheduledEnd = Carbon::today()->setTimeFrom($recoverySession->scheduled_end_time);

                if ($now->lt($scheduledStart->copy()->subMinutes(15))) { // Pequeño margen de 15 minutos antes 
                    return response()->json([
                        'success' => false,
                        'message' => 'Aún no es hora de iniciar tu recuperación. Debes esperar al horario establecido por el administrador (' . $scheduledStart->format('H:i') . ').'
                    ], 400);
                }

                if ($now->gt($scheduledEnd)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El horario para esta sesión de recuperación ya finalizó (' . $scheduledEnd->format('H:i') . ').'
                    ], 400);
                }
            }

            // Verificar si ya tiene una sesión activa
            $activeSession = AttendanceSession::where('apprentice_id', $user->id)
                ->whereNull('end_at')
                ->first();

            if ($activeSession) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya tienes una sesión activa. Debes registrar salida primero.'
                ], 400);
            }

            // Verificar si ya registró entrada hoy
            $todayEntry = AttendanceLog::where('apprentice_id', $user->id)
                ->where('event_type', 'entrada')
                ->whereDate('occurred_at', Carbon::today())
                ->first();

            if ($todayEntry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya registraste entrada hoy. Si necesitas registrar salida, usa el botón correspondiente.'
                ], 400);
            }

            // Crear log de entrada
            $log = AttendanceLog::create([
                'apprentice_id' => $user->id,
                'event_type' => 'entrada',
                'occurred_at' => $now,
                'source' => 'web',
                'note' => 'Registro automático por aprendiz',
                'created_by' => $user->id,
            ]);

            $effectiveStart = $now->copy();
            if ($todaySchedule) {
                $scheduledStart = Carbon::today()->setTimeFrom($todaySchedule->start_time);
                if ($now->lt($scheduledStart)) {
                    $effectiveStart = $scheduledStart;
                }
            } elseif ($recoverySession) {
                $scheduledStart = Carbon::today()->setTimeFrom($recoverySession->scheduled_start_time);
                if ($now->lt($scheduledStart)) {
                    $effectiveStart = $scheduledStart;
                }
            }

            // Crear nueva sesión
            AttendanceSession::create([
                'apprentice_id' => $user->id,
                'start_at' => $effectiveStart,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Entrada registrada exitosamente a las ' . $now->format('H:i:s'),
                'timestamp' => $now->format('H:i:s'),
                'date' => $now->format('d/m/Y')
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
     * Registrar salida del aprendiz
     */
    public function registerExit(Request $request)
    {
        $user = Auth::user();

        try {
            DB::beginTransaction();

            $todaySchedule = $this->getTodaySchedule($user->id);
            if (!$todaySchedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes un horario programado para hoy.'
                ], 400);
            }

            $activeSession = AttendanceSession::where('apprentice_id', $user->id)
                ->whereNull('end_at')
                ->first();

            if (!$activeSession) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes una sesión activa.'
                ], 400);
            }

            $response = $this->processExit($user, $activeSession, $todaySchedule, Carbon::now(), 'Registro manual por aprendiz');

            DB::commit();

            return response()->json($response);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar salida: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lógica central de procesamiento de salida (Manual o Automática)
     */
    private function processExit($user, $activeSession, $todaySchedule, $now, $note = '')
    {
        // 1. Determinar el fin programado
        $scheduledEnd = null;
        if ($todaySchedule) {
            $scheduledEnd = Carbon::today()->setTimeFrom($todaySchedule->end_time);
        } else {
            $recoverySession = RecoverySession::where('apprentice_id', $user->id)
                ->where('date', '=', Carbon::today()->toDateString())
                ->where('status', 'scheduled')
                ->first();
            if ($recoverySession) {
                $scheduledEnd = Carbon::parse($recoverySession->scheduled_end_time);
            }
        }

        // 2. Crear log de salida
        AttendanceLog::create([
            'apprentice_id' => $user->id,
            'event_type' => 'salida',
            'occurred_at' => $now,
            'source' => 'web',
            'note' => $note ?: 'Registro automático de salida',
            'created_by' => $user->id,
        ]);

        // 3. Determinar la hora efectiva de fin (no puede superar la programada)
        $effectiveEnd = ($scheduledEnd && $now->gt($scheduledEnd)) ? $scheduledEnd : $now;
        $startTime = Carbon::parse($activeSession->start_at);
        $durationMinutes = $startTime->gt($effectiveEnd) ? 0 : $startTime->diffInMinutes($effectiveEnd);

        // 4. Cerrar sesión principal
        $activeSession->update([
            'end_at' => $effectiveEnd,
            'duration_minutes' => $durationMinutes,
        ]);

        // 5. Manejo de recuperación si aplica
        $recoverySession = RecoverySession::where('apprentice_id', $user->id)
            ->where('date', '=', Carbon::today()->toDateString())
            ->where('status', 'scheduled')
            ->first();

        if ($recoverySession) {
            $recoverySession->update([
                'status' => 'completed',
                'start_time' => $activeSession->start_at,
                'end_time' => $effectiveEnd,
                'duration_minutes' => $durationMinutes
            ]);

            $penalty = $recoverySession->recoveryRequest->penalty;
            $newAttended = ($penalty->attended_hours * 60) + $durationMinutes;
            $newPenalty = max(0, ($penalty->scheduled_hours * 60) - $newAttended);

            $penalty->update([
                'attended_hours' => $newAttended / 60,
                'penalty_hours' => $newPenalty / 60,
                'status' => $newPenalty <= 0 ? 'closed' : 'in_recovery'
            ]);

            $recoverySession->recoveryRequest->update(['status' => 'completed']);
        }

        // 6. Manejo de jornada regular: generar penalización si hay deuda
        $scheduledMinutes = 0;
        $attendedMinutes = $durationMinutes;
        $progressPercent = 100;

        if ($todaySchedule) {
            $scheduledMinutes = Carbon::parse($todaySchedule->start_time)->diffInMinutes(Carbon::parse($todaySchedule->end_time));
            $todaySessions = AttendanceSession::where('apprentice_id', $user->id)
                ->whereDate('start_at', Carbon::today())
                ->whereNotNull('end_at')
                ->get();

            $attendedMinutes = $todaySessions->sum('duration_minutes');
            $pendingMinutes = max(0, $scheduledMinutes - $attendedMinutes);

            if ($pendingMinutes > 0) {
                $existingPenalty = Penalty::where('apprentice_id', $user->id)
                    ->where('date', '=', Carbon::today()->toDateString())
                    ->first();

                if ($existingPenalty) {
                    $existingPenalty->update([
                        'attended_hours' => $attendedMinutes / 60,
                        'penalty_hours' => $pendingMinutes / 60,
                    ]);
                } else {
                    Penalty::create([
                        'apprentice_id' => $user->id,
                        'date' => Carbon::today(),
                        'scheduled_hours' => $scheduledMinutes / 60,
                        'attended_hours' => $attendedMinutes / 60,
                        'penalty_hours' => $pendingMinutes / 60,
                        'schedule_id' => $todaySchedule->id,
                        'status' => 'pending'
                    ]);
                }
            }
            $progressPercent = min(100, ($attendedMinutes / $scheduledMinutes) * 100);
        }

        return [
            'success' => true,
            'message' => 'Jornada finalizada correctamente.',
            'timestamp' => $now->format('H:i:s'),
            'date' => $now->format('d/m/Y'),
            'duration_hours' => round($durationMinutes / 60, 2),
            'progress_info' => [
                'scheduled_hours' => intdiv($scheduledMinutes, 60),
                'scheduled_minutes' => $scheduledMinutes % 60,
                'attended_hours' => intdiv($attendedMinutes, 60),
                'attended_minutes' => $attendedMinutes % 60,
                'pending_hours' => intdiv(max(0, $scheduledMinutes - $attendedMinutes), 60),
                'pending_minutes' => max(0, $scheduledMinutes - $attendedMinutes) % 60,
                'progress_percent' => round($progressPercent, 1),
                'has_pending' => $scheduledMinutes > $attendedMinutes
            ]
        ];
    }

    /**
     * Obtener estado actual de asistencia del aprendiz
     */
    private function getCurrentAttendanceStatus($apprenticeId)
    {
        $activeSession = AttendanceSession::where('apprentice_id', $apprenticeId)
            ->whereNull('end_at')
            ->first();

        $todaySchedule = $this->getTodaySchedule($apprenticeId);
        $recoverySession = RecoverySession::where('apprentice_id', $apprenticeId)
            ->where('date', '=', Carbon::today()->toDateString())
            ->where('status', 'scheduled')
            ->first();

        // Determinar hora de fin programada (prioridad a jornada regular, luego recuperación)
        $scheduledEnd = null;
        if ($todaySchedule) {
            $scheduledEnd = Carbon::today()->setTimeFrom($todaySchedule->end_time);
        } elseif ($recoverySession) {
            $scheduledEnd = Carbon::parse($recoverySession->scheduled_end_time);
        }

        if ($activeSession) {
            $now = Carbon::now();

            // Si ya terminó el horario programado, forzamos que se vea como completado
            if ($scheduledEnd && $now->gt($scheduledEnd)) {
                $startTime = Carbon::parse($activeSession->start_at);
                $durationMinutes = $startTime->diffInMinutes($scheduledEnd);

                return [
                    'status' => 'completed',
                    'entry_time' => $startTime,
                    'end_time' => $scheduledEnd,
                    'duration_hours' => round($durationMinutes / 60, 2),
                    'session_start' => $startTime,
                    'auto_closed' => true
                ];
            }

            return [
                'status' => 'active',
                'start_time' => $activeSession->start_at,
                'duration' => $now->gt($activeSession->start_at) ? $now->diffInMinutes($activeSession->start_at) : 0,
                'scheduled_end' => $scheduledEnd
            ];
        }

        // Verificar si ya completó la sesión hoy
        $todaySession = AttendanceSession::where('apprentice_id', $apprenticeId)
            ->whereDate('start_at', Carbon::today())
            ->whereNotNull('end_at')
            ->first();

        if ($todaySession) {
            $todayEntry = AttendanceLog::where('apprentice_id', $apprenticeId)
                ->where('event_type', 'entrada')
                ->whereDate('occurred_at', Carbon::today())
                ->first();

            return [
                'status' => 'completed',
                'entry_time' => $todayEntry ? $todayEntry->occurred_at : $todaySession->start_at,
                'end_time' => $todaySession->end_at,
                'duration_hours' => round($todaySession->duration_minutes / 60, 2),
                'session_start' => $todaySession->start_at
            ];
        }

        // Verificar si ya registró entrada hoy pero no completó
        $todayEntry = AttendanceLog::where('apprentice_id', $apprenticeId)
            ->where('event_type', 'entrada')
            ->whereDate('occurred_at', Carbon::today())
            ->first();

        if ($todayEntry) {
            return [
                'status' => 'entry_registered',
                'entry_time' => $todayEntry->occurred_at
            ];
        }

        return [
            'status' => 'no_entry'
        ];
    }

    /**
     * Obtener horario del día actual
     */
    private function getTodaySchedule($apprenticeId)
    {
        $weekday = Carbon::now()->dayOfWeek;
        // Carbon usa 0=Domingo, 1=Lunes, etc. Convertir a 1-7
        $weekday = $weekday === 0 ? 7 : $weekday;

        return Schedule::where('apprentice_id', $apprenticeId)
            ->where('weekday', $weekday)
            ->where('status', 'activo')
            ->first();
    }
}
