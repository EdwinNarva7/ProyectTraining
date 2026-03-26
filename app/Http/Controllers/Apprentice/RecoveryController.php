<?php

namespace App\Http\Controllers\Apprentice;

use App\Http\Controllers\Controller;
use App\Models\RecoverySession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RecoveryController extends Controller
{
    public function index()
    {
        $sessions = RecoverySession::with('recoveryRequest.penalty')
            ->where('apprentice_id', Auth::id())
            ->orderBy('date', 'desc')
            ->paginate(10);

        $activeSession = RecoverySession::where('apprentice_id', Auth::id())
            ->where('status', 'in_progress')
            ->first();

        return view('aprendiz.recovery.index', compact('sessions', 'activeSession'));
    }

    public function startSession(RecoverySession $session)
    {
        if ($session->apprentice_id !== Auth::id()) {
            abort(403);
        }

        if ($session->status !== 'scheduled') {
            return back()->with('error', 'Esta sesión no puede ser iniciada.');
        }

        // Check for other sessions
        $activeSession = RecoverySession::where('apprentice_id', Auth::id())
            ->where('status', 'in_progress')
            ->first();

        if ($activeSession) {
            return back()->with('error', 'Cierre primero la sesión actual.');
        }

        if (!Carbon::parse($session->date)->isToday()) {
            return back()->with('error', 'Solo puede iniciar en la fecha programada.');
        }

        $now = Carbon::now();
        $scheduledStart = Carbon::parse($session->date)->setTimeFrom($session->scheduled_start_time);
        $scheduledEnd = Carbon::parse($session->date)->setTimeFrom($session->scheduled_end_time);

        // 0. Verificar estado del aprendiz (si ya está inactivo no puede operar)
        if (Auth::user()->status !== 'activo') {
            return back()->with('error', 'Tu cuenta se encuentra INACTIVA. Por favor contacta al administrador para reactivarla antes de iniciar jornadas.');
        }

        // 1. Validar ventana de tiempo (espera de hasta 15 minutos similar a jornada normal)
        if ($now->lt($scheduledStart->copy()->subMinutes(15))) {
            return back()->with('info_alert', 'Aún no es hora de iniciar tu recuperación. El horario programado es de ' . $scheduledStart->format('h:i A') . ' a ' . $scheduledEnd->format('h:i A') . '. Por favor, espera a que llegue la hora establecida.');
        }

        // 2. Validar si la jornada ya terminó completamente
        if ($now->gt($scheduledEnd)) {
            return back()->with('error', 'Esta jornada de recuperación ya ha finalizado (' . $scheduledEnd->format('h:i A') . '). No puedes iniciarla ahora.');
        }

        // 3. Límite de latencia (Tolerancia de 4 horas para flexibilidad)
        if ($now->gt($scheduledStart->copy()->addHours(4))) {
            $session->update(['status' => 'canceled']);

            return redirect()->route('apprentice.recovery.index')
                ->with('error', '⚠️ JORNADA CANCELADA: Has superado el límite de 4 horas de retraso para iniciar tu jornada (límite: ' . $scheduledStart->copy()->addHours(4)->format('h:i A') . '). Debes solicitar una nueva programación con el administrador.');
        }

        // Determinar hora de inicio efectiva (espera hasta la hora programada si es temprano)
        $effectiveStart = $now->copy();
        if ($now->lt($scheduledStart)) {
            $effectiveStart = $scheduledStart;
        }

        $session->update([
            'status' => 'in_progress',
            'start_time' => $effectiveStart
        ]);

        return redirect()->route('apprentice.recovery.show', $session)
            ->with('success', 'Sesión iniciada. Debes cumplir la duración total programada.');
    }

    public function show(RecoverySession $session)
    {
        if ($session->apprentice_id !== Auth::id()) {
            abort(403);
        }

        if ($session->status !== 'in_progress' && $session->status !== 'completed') {
            return redirect()->route('apprentice.recovery.index');
        }

        return view('aprendiz.recovery.show', compact('session'));
    }

    public function endSession(RecoverySession $session)
    {
        if ($session->apprentice_id !== Auth::id()) {
            abort(403);
        }

        if ($session->status !== 'in_progress') {
            return back()->with('error', 'Esta sesión no está en curso.');
        }

        $now = Carbon::now();
        $startTime = Carbon::parse($session->start_time);
        $elapsedMinutes = $startTime->diffInMinutes($now);

        // Match the exact programmed duration
        $scheduledStartDT = \Carbon\Carbon::parse($session->date)->setTimeFrom($session->scheduled_start_time);
        $scheduledEndDT = \Carbon\Carbon::parse($session->date)->setTimeFrom($session->scheduled_end_time);
        $requiredMinutes = $scheduledStartDT->diffInMinutes($scheduledEndDT);

        if ($elapsedMinutes < $requiredMinutes) {
            return back()->with('error', 'No puedes terminar la sesión aún. Te faltan ' . ceil($requiredMinutes - $elapsedMinutes) . ' minutos para cumplir tu penalización.');
        }

        // Cap the minutes to the required duration (prevent overcounting)
        $compensatedMinutes = min($elapsedMinutes, $requiredMinutes);

        $session->update([
            'status' => 'completed',
            'end_time' => $now,
            'duration_minutes' => $compensatedMinutes
        ]);

        // Update penalty hours accurately
        $penalty = $session->recoveryRequest->penalty;
        $prevAttendedMinutes = $penalty->attended_hours * 60;
        $newAttendedMinutes = $prevAttendedMinutes + $compensatedMinutes;

        $newAttendedHours = $newAttendedMinutes / 60;
        $remainingHours = max(0, $penalty->scheduled_hours - $newAttendedHours);

        $penalty->update([
            'attended_hours' => $newAttendedHours,
            'penalty_hours' => $remainingHours,
            'status' => $remainingHours <= 0.01 ? 'closed' : 'in_recovery'
        ]);

        $session->recoveryRequest->update(['status' => 'completed']);

        return redirect()->route('apprentice.recovery.index')
            ->with('success', 'Sesión completada exitosamente. Se han descontado ' . number_format($elapsedMinutes / 60, 1) . ' horas.');
    }
}
