<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApprenticeProfile;
use App\Models\AttendanceLog;
use App\Models\AttendanceSession;
use App\Models\Schedule;
use App\Models\RecoverySession;
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
     * Panel de marcación de asistencia por huella (acceso del operario/admin).
     */
    public function scannerPanel()
    {
        $apprentices = User::whereHas('role', fn($q) => $q->where('name', 'Aprendiz'))
            ->where('status', 'activo')
            ->with('apprenticeProfile')
            ->get();

        return view('admin.fingerprint.scanner', compact('apprentices'));
    }

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
        // Verificar horario del día
        $weekday = $now->dayOfWeek === 0 ? 7 : $now->dayOfWeek;
        $todaySchedule = Schedule::where('apprentice_id', $apprentice->id)
            ->where('weekday', $weekday)
            ->where('status', 'activo')
            ->first();

        $recoverySession = RecoverySession::where('apprentice_id', $apprentice->id)
            ->where('date', $now->toDateString())
            ->where('status', 'scheduled')
            ->first();

        if (!$todaySchedule && !$recoverySession) {
            return [
                'success' => false,
                'message' => "No hay jornada programada para {$apprentice->full_name} hoy.",
                'apprentice' => $apprentice->full_name,
                'event_type' => null,
            ];
        }

        // Validar ventana de tiempo
        if ($todaySchedule) {
            $scheduledStart = Carbon::today()->setTimeFrom($todaySchedule->start_time);
            $scheduledEnd = Carbon::today()->setTimeFrom($todaySchedule->end_time);

            if ($now->lt($scheduledStart)) {
                return [
                    'success' => false,
                    'message' => "Aún no es hora. La jornada de {$apprentice->full_name} empieza a las {$scheduledStart->format('H:i')}.",
                    'apprentice' => $apprentice->full_name,
                    'event_type' => null,
                ];
            }

            if ($now->gt($scheduledEnd)) {
                return [
                    'success' => false,
                    'message' => "La jornada de {$apprentice->full_name} ya terminó ({$scheduledEnd->format('H:i')}).",
                    'apprentice' => $apprentice->full_name,
                    'event_type' => null,
                ];
            }
        } elseif ($recoverySession) {
            $scheduledStart = Carbon::parse($recoverySession->scheduled_start_time);
            $scheduledEnd = Carbon::parse($recoverySession->scheduled_end_time);

            if ($now->lt($scheduledStart->copy()->subMinutes(5))) {
                return [
                    'success' => false,
                    'message' => "Aún no es hora para la recuperación de {$apprentice->full_name} (desde {$scheduledStart->format('H:i')}).",
                    'apprentice' => $apprentice->full_name,
                    'event_type' => null,
                ];
            }
        }

        // Verificar si ya tiene entrada hoy
        $todayEntry = AttendanceLog::where('apprentice_id', $apprentice->id)
            ->where('event_type', 'entrada')
            ->whereDate('occurred_at', Carbon::today())
            ->first();

        if ($todayEntry) {
            return [
                'success' => false,
                'message' => "{$apprentice->full_name} ya tiene entrada registrada hoy a las {$todayEntry->occurred_at->format('H:i')}.",
                'apprentice' => $apprentice->full_name,
                'event_type' => 'entrada',
            ];
        }

        // ── Registrar entrada ────────────────────────────────────────────────
        AttendanceLog::create([
            'apprentice_id' => $apprentice->id,
            'event_type' => 'entrada',
            'occurred_at' => $now,
            'source' => 'huella',
            'note' => 'Entrada registrada por lector biométrico',
            'created_by' => Auth::id() ?? $apprentice->id,
        ]);

        AttendanceSession::create([
            'apprentice_id' => $apprentice->id,
            'start_at' => $now,
        ]);

        return [
            'success' => true,
            'message' => "✅ Entrada registrada para {$apprentice->full_name}",
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
        $weekday = $now->dayOfWeek === 0 ? 7 : $now->dayOfWeek;
        $todaySchedule = Schedule::where('apprentice_id', $apprentice->id)
            ->where('weekday', $weekday)
            ->where('status', 'activo')
            ->first();

        // Determinar fin programado (para no superar el horario)
        $scheduledEnd = null;
        if ($todaySchedule) {
            $scheduledEnd = Carbon::today()->setTimeFrom($todaySchedule->end_time);
        } else {
            $recoverySession = RecoverySession::where('apprentice_id', $apprentice->id)
                ->where('date', $now->toDateString())
                ->where('status', 'scheduled')
                ->first();
            if ($recoverySession) {
                $scheduledEnd = Carbon::parse($recoverySession->scheduled_end_time);
            }
        }

        // Hora efectiva de salida (no superar horario programado)
        $effectiveEnd = ($scheduledEnd && $now->gt($scheduledEnd)) ? $scheduledEnd : $now;
        $startTime = Carbon::parse($activeSession->start_at);
        $durationMinutes = $startTime->gt($effectiveEnd) ? 0 : $startTime->diffInMinutes($effectiveEnd);

        // Cerrar sesión
        $activeSession->update([
            'end_at' => $effectiveEnd,
            'duration_minutes' => $durationMinutes,
        ]);

        // Log de salida
        AttendanceLog::create([
            'apprentice_id' => $apprentice->id,
            'event_type' => 'salida',
            'occurred_at' => $effectiveEnd,
            'source' => 'huella',
            'note' => 'Salida registrada por lector biométrico',
            'created_by' => Auth::id() ?? $apprentice->id,
        ]);

        $hours = intdiv($durationMinutes, 60);
        $minutes = $durationMinutes % 60;

        return [
            'success' => true,
            'message' => "✅ Salida registrada para {$apprentice->full_name}. Duración: {$hours}h {$minutes}m",
            'apprentice' => $apprentice->full_name,
            'event_type' => 'salida',
            'timestamp' => $effectiveEnd->format('H:i:s'),
            'date' => $effectiveEnd->format('d/m/Y'),
            'duration_hours' => round($durationMinutes / 60, 2),
            'duration_display' => "{$hours}h {$minutes}m",
        ];
    }
}
