<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApprenticeProfile;
use App\Models\AttendanceLog;
use App\Models\AttendanceSession;
use App\Models\Schedule;
use App\Models\RecoverySession;
use App\Models\Penalty;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FingerprintController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // VISTAS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Panel de gestión de huellas (enrolamiento, eliminación).
     */
    public function enrollPanel()
    {
        $apprentices = User::whereHas('role', fn($q) => $q->where('name', 'Aprendiz'))
            ->where('status', 'activo')
            ->with('apprenticeProfile')
            ->orderBy('full_name')
            ->get();

        $enrolledCount = ApprenticeProfile::whereNotNull('fingerprint_template')->count();
        $totalCount = ApprenticeProfile::count();

        return view('admin.fingerprint.enroll', compact('apprentices', 'enrolledCount', 'totalCount'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // API — ENROLAMIENTO
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Guarda (enrola) la plantilla de huella de un aprendiz.
     * Recibe el template en Base64 desde el Bridge C#.
     *
     * POST /admin/fingerprint/enroll
     * Body JSON: { "apprentice_id": 5, "template_base64": "AAAA..." }
     */
    public function enroll(Request $request)
    {
        $request->validate([
            'apprentice_id' => 'required|exists:users,id',
            'template_base64' => 'required|string|min:10',
        ]);

        try {
            $profile = ApprenticeProfile::where('user_id', $request->apprentice_id)->first();

            if (!$profile) {
                return response()->json([
                    'success' => false,
                    'message' => 'El aprendiz no tiene perfil registrado.',
                ], 404);
            }

            $profile->update([
                'fingerprint_template' => $request->template_base64,
                'fingerprint_enrolled_at' => Carbon::now(),
            ]);

            $apprentice = User::find($request->apprentice_id);

            return response()->json([
                'success' => true,
                'message' => "Huella de {$apprentice->full_name} enrolada correctamente.",
                'enrolled_at' => Carbon::now()->format('d/m/Y H:i:s'),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la huella: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Elimina la huella enrolada de un aprendiz.
     *
     * DELETE /admin/fingerprint/{apprentice_id}
     */
    public function deleteEnrollment(int $apprenticeId)
    {
        $profile = ApprenticeProfile::where('user_id', $apprenticeId)->first();

        if (!$profile) {
            return response()->json(['success' => false, 'message' => 'Perfil no encontrado.'], 404);
        }

        $profile->update([
            'fingerprint_template' => null,
            'fingerprint_enrolled_at' => null,
        ]);

        return response()->json(['success' => true, 'message' => 'Huella eliminada correctamente.']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // API — VERIFICACIÓN Y MARCADO DE ASISTENCIA
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Devuelve TODOS los templates enrolados para que el bridge C# compare en cliente.
     * El bridge hace la comparación biométrica localmente (más seguro y rápido).
     *
     * GET /admin/fingerprint/templates
     */
    public function getTemplates()
    {
        $profiles = ApprenticeProfile::whereNotNull('fingerprint_template')
            ->with('user:id,full_name,status')
            ->get(['user_id', 'fingerprint_template']);

        $templates = $profiles
            ->filter(fn($p) => $p->user && $p->user->status === 'activo')
            ->map(fn($p) => [
                'apprentice_id' => $p->user_id,
                'full_name' => $p->user->full_name,
                'template_base64' => $p->fingerprint_template,
            ])
            ->values();

        return response()->json([
            'success' => true,
            'templates' => $templates,
        ]);
    }

    /**
     * Registra entrada o salida automática por huella.
     * El bridge ya identificó al aprendiz y envía solo el ID.
     *
     * POST /admin/fingerprint/mark
     * Body JSON: { "apprentice_id": 5 }
     */
    public function mark(Request $request)
    {
        $request->validate([
            'apprentice_id' => 'required|exists:users,id',
        ]);

        $apprentice = User::with('apprenticeProfile')->find($request->apprentice_id);

        if (!$apprentice || $apprentice->status !== 'activo') {
            return response()->json([
                'success' => false,
                'message' => 'El aprendiz no está activo en el sistema.',
            ], 403);
        }

        // Verificar que tiene huella enrolada
        if (!$apprentice->apprenticeProfile || !$apprentice->apprenticeProfile->hasFingerprint()) {
            return response()->json([
                'success' => false,
                'message' => 'Este aprendiz no tiene huella registrada. Contacta al administrador.',
            ], 400);
        }

        try {
            DB::beginTransaction();

            $now = Carbon::now();
            $result = $this->resolveMarkType($apprentice, $now);

            DB::commit();

            return response()->json($result);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // LÓGICA INTERNA
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Determina si el marcado es una ENTRADA o una SALIDA según el estado actual,
     * ejecuta el registro y devuelve la respuesta.
     */
    private function resolveMarkType(User $apprentice, Carbon $now): array
    {
        $activeSession = AttendanceSession::where('apprentice_id', $apprentice->id)
            ->whereNull('end_at')
            ->first();

        // ── Si ya tiene sesión activa → registrar SALIDA ─────────────────────
        if ($activeSession) {
            return $this->registerExit($apprentice, $activeSession, $now);
        }

        // ── Si no tiene sesión activa → registrar ENTRADA ────────────────────
        return $this->registerEntry($apprentice, $now);
    }

    /**
     * Valida el horario y registra la ENTRADA del aprendiz.
     */
    private function registerEntry(User $apprentice, Carbon $now): array
    {
        // Detectar posibles jornadas para hoy
        $weekday = $now->dayOfWeek === 0 ? 7 : $now->dayOfWeek;
        
        // 1. Buscar horario individual
        $todaySchedule = Schedule::where('apprentice_id', $apprentice->id)
            ->where('weekday', $weekday)
            ->where('status', 'activo')
            ->first();

        // 2. Si no hay individual, buscar por tecnólogo
        if (!$todaySchedule && $apprentice->apprenticeProfile?->technologist_id) {
            $todaySchedule = Schedule::where('technologist_id', $apprentice->apprenticeProfile->technologist_id)
                ->where('weekday', $weekday)
                ->where('status', 'activo')
                ->first();
        }

        // Buscar recuperación programada para HOY
        $recoverySession = RecoverySession::where('apprentice_id', $apprentice->id)
            ->where('date', $now->toDateString())
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->first();

        // Determinar qué jornada está activa o por empezar
        $activeTarget = null;
        $type = null;

        // 1. PRIORIDAD: Evaluar si la RECUPEACIÓN es la válida para este momento (desde 15m antes hasta el fin)
        if ($recoverySession) {
            $startRec = Carbon::parse($recoverySession->scheduled_start_time);
            $endRec = Carbon::parse($recoverySession->scheduled_end_time);

            if ($now->gte($startRec->copy()->subMinutes(15)) && $now->lte($endRec)) {
                $activeTarget = $recoverySession;
                $type = 'recovery';
            }
        }

        // 2. Si no es recuperación, evaluar si la jornada NORMAL es la válida
        if (!$activeTarget && $todaySchedule) {
            $startNormal = Carbon::today()->setTimeFrom($todaySchedule->start_time);
            $endNormal = Carbon::today()->setTimeFrom($todaySchedule->end_time);
            
            if ($now->gte($startNormal->copy()->subMinutes(30)) && $now->lte($endNormal)) {
                $activeTarget = $todaySchedule;
                $type = 'normal';
            }
        }

        // 3. Validaciones de Errores Específicos (cuando no se encontró jornada activa)
        if (!$activeTarget) {
            // Si la jornada normal ya pasó (y no estamos en rango de recuperación)
            if ($todaySchedule && $now->gt(Carbon::today()->setTimeFrom($todaySchedule->end_time))) {
                return [
                    'success' => false,
                    'message' => "Tu jornada de hoy ya expiró a las " . Carbon::parse($todaySchedule->end_time)->format('H:i') . ". Contacta al instructor.",
                    'apprentice' => $apprentice->full_name,
                ];
            }

            // Si es muy temprano
            if ($todaySchedule && $now->lt(Carbon::today()->setTimeFrom($todaySchedule->start_time)->subMinutes(30))) {
                return [
                    'success' => false,
                    'message' => "Aún es muy temprano para tu jornada. Empieza a las " . Carbon::parse($todaySchedule->start_time)->format('H:i'),
                    'apprentice' => $apprentice->full_name,
                ];
            }

            if ($recoverySession && $now->lt(Carbon::parse($recoverySession->scheduled_start_time)->subMinutes(15))) {
                return [
                    'success' => false,
                    'message' => "La recuperación inicia a las " . Carbon::parse($recoverySession->scheduled_start_time)->format('H:i') . ". Por favor espera.",
                    'apprentice' => $apprentice->full_name,
                ];
            }
            
            return [
                'success' => false,
                'message' => "No tienes una jornada o recuperación programada en este momento.",
                'apprentice' => $apprentice->full_name,
            ];
        }

        // Verificar si ya tiene entrada para ESTA jornada específica
        $logNote = $type === 'recovery' 
            ? "Entrada a Recuperación #{$activeTarget->id}" 
            : "Entrada automática a jornada normal";

        $todayEntry = AttendanceLog::where('apprentice_id', $apprentice->id)
            ->where('event_type', 'entrada')
            ->whereDate('occurred_at', Carbon::today())
            ->where('note', $logNote)
            ->first();

        if ($todayEntry) {
            return [
                'success' => false,
                'message' => "Ya registraste tu entrada para esta " . ($type === 'recovery' ? 'recuperación' : 'jornada') . " hoy a las {$todayEntry->occurred_at->format('H:i')}.",
                'apprentice' => $apprentice->full_name,
            ];
        }

        // Determinar hora de inicio efectiva (espera hasta la hora programada si es temprano)
        $effectiveStart = $now->copy();
        
        if ($type === 'normal') {
            $scheduledStart = Carbon::today()->setTimeFrom($activeTarget->start_time);
            if ($now->lt($scheduledStart)) $effectiveStart = $scheduledStart;
        } else {
            $scheduledStart = Carbon::parse($activeTarget->scheduled_start_time);
            if ($now->lt($scheduledStart)) $effectiveStart = $scheduledStart;
        }

        // ── Registrar entrada ────────────────────────────────────────────────
        AttendanceLog::create([
            'apprentice_id' => $apprentice->id,
            'event_type' => 'entrada',
            'occurred_at' => $now,
            'source' => 'huella',
            'note' => $logNote,
            'created_by' => Auth::id() ?? $apprentice->id,
        ]);

        if ($type === 'recovery') {
            $activeTarget->update([
                'status' => 'in_progress',
                'start_time' => $now
            ]);
        }

         AttendanceSession::create([
            'apprentice_id' => $apprentice->id,
            'start_at' => $effectiveStart,
        ]);

        return [
            'success' => true,
            'message' => "¡Hola {$apprentice->full_name}! Tu ENTRADA ha sido registrada correctamente.",
            'apprentice' => $apprentice->full_name,
            'event_type' => 'entrada',
            'timestamp' => $now->format('H:i:s'),
            'date' => $now->format('d/m/Y'),
        ];
    }

    /**
     * Registra la SALIDA del aprendiz y cierra su sesión activa.
     */
    private function registerExit(User $apprentice, AttendanceSession $activeSession, Carbon $now): array
    {
        // 1. Determinar cuál jornada se está operando hoy según el horario actual
        $scheduledEnd = null;
        $isRecovery = false;

        // Buscar jornada normal (individual o por tecnólogo)
        $weekday = $now->dayOfWeek === 0 ? 7 : $now->dayOfWeek;
        $todaySchedule = Schedule::where('apprentice_id', $apprentice->id)
            ->where('weekday', $weekday)
            ->where('status', 'activo')
            ->first();

        if (!$todaySchedule && $apprentice->apprenticeProfile?->technologist_id) {
            $todaySchedule = Schedule::where('technologist_id', $apprentice->apprenticeProfile->technologist_id)
                ->where('weekday', $weekday)
                ->where('status', 'activo')
                ->first();
        }

        // Buscar recuperación (dar prioridad a una que esté en proceso)
        $recoverySession = RecoverySession::where('apprentice_id', $apprentice->id)
            ->where('date', $now->toDateString())
            ->whereIn('status', ['in_progress', 'scheduled'])
            ->first();

        // Priorizar la jornada donde el "ahora" encaje mejor con el fin programado
        if ($recoverySession && $recoverySession->status === 'in_progress') {
            $scheduledEnd = Carbon::parse($recoverySession->scheduled_end_time);
            $isRecovery = true;
        } elseif ($todaySchedule) {
            $endNormal = Carbon::today()->setTimeFrom($todaySchedule->end_time);
            $scheduledEnd = $endNormal;
            $isRecovery = false;
        } elseif ($recoverySession) {
            $scheduledEnd = Carbon::parse($recoverySession->scheduled_end_time);
            $isRecovery = true;
        }

        // 2. Hora efectiva de salida (no superar horario programado)
        $effectiveEnd = ($scheduledEnd && $now->gt($scheduledEnd)) ? $scheduledEnd : $now;
        
        // 3. Calcular tiempo faltante si sale antes de lo programado
        $missingMinutes = ($scheduledEnd && $now->lt($scheduledEnd)) ? $now->diffInMinutes($scheduledEnd) : 0;
        
        $startTime = Carbon::parse($activeSession->start_at);
        $durationMinutes = $startTime->gt($effectiveEnd) ? 0 : $startTime->diffInMinutes($effectiveEnd);

        // 4. Cerrar sesión
        $activeSession->update([
            'end_at' => $effectiveEnd,
            'duration_minutes' => $durationMinutes,
        ]);

        // 5. Log de salida
        AttendanceLog::create([
            'apprentice_id' => $apprentice->id,
            'event_type' => 'salida',
            'occurred_at' => $effectiveEnd,
            'source' => 'huella',
            'note' => 'Salida registrada por lector biométrico',
            'created_by' => Auth::id() ?? $apprentice->id,
        ]);

        // 6. Manejo específico de RECUPEARCION si aplica
        if ($isRecovery && $recoverySession) {
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

        // 7. Manejo de JORNADA regular: generar penalización si hay deuda
        $scheduledMinutes = 0;
        $attendedMinutes = $durationMinutes;
        
        if (!$isRecovery && $todaySchedule) {
            $scheduledMinutes = Carbon::parse($todaySchedule->start_time)->diffInMinutes(Carbon::parse($todaySchedule->end_time));
            
            // Obtener todas las sesiones completadas hoy para calcular total asistido
            $todaySessions = AttendanceSession::where('apprentice_id', $apprentice->id)
                ->whereDate('start_at', $now->toDateString())
                ->whereNotNull('end_at')
                ->get();

            $attendedMinutes = $todaySessions->sum('duration_minutes');
            $pendingMinutes = max(0, $scheduledMinutes - $attendedMinutes);

            if ($pendingMinutes > 0) {
                Penalty::updateOrCreate(
                    [
                        'apprentice_id' => $apprentice->id,
                        'date' => $now->toDateString(),
                    ],
                    [
                        'scheduled_hours' => $scheduledMinutes / 60,
                        'attended_hours' => $attendedMinutes / 60,
                        'penalty_hours' => $pendingMinutes / 60,
                        'schedule_id' => $todaySchedule->id,
                        'status' => 'pending'
                    ]
                );
            }
        }

        $hours = intdiv($durationMinutes, 60);
        $minutes = $durationMinutes % 60;
        
        $missingHours = intdiv($missingMinutes, 60);
        $missingMins = $missingMinutes % 60;
        $missingDisplay = $missingMinutes > 0 ? "{$missingHours}h {$missingMins}m" : null;

        return [
            'success' => true,
            'message' => "✅ Salida registrada para {$apprentice->full_name}. Duración: {$hours}h {$minutes}m",
            'apprentice' => $apprentice->full_name,
            'event_type' => 'salida',
            'timestamp' => $effectiveEnd->format('H:i:s'),
            'date' => $effectiveEnd->format('d/m/Y'),
            'duration_hours' => round($durationMinutes / 60, 2),
            'duration_display' => "{$hours}h {$minutes}m",
            'missing_minutes' => $missingMinutes,
            'missing_display' => $missingDisplay,
        ];
    }
}
