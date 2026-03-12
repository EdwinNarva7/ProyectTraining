<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RecoverySession;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RecoverySessionController extends Controller
{
    /**
     * Listado de sesiones de recuperación
     */
    public function index()
    {
        $sessions = RecoverySession::with(['apprentice', 'recoveryRequest.penalty'])
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('admin.recovery_sessions.index', compact('sessions'));
    }

    /**
     * Cerrar sesión de recuperación manualmente
     */
    public function close(Request $request, RecoverySession $session)
    {
        $request->validate([
            'duration_minutes' => 'required|integer|min:1'
        ]);

        $session->update([
            'duration_minutes' => $request->duration_minutes,
            'status' => 'completed',
            'end_time' => Carbon::now()
        ]);

        // Actualizar la penalización asociada
        $penalty = $session->recoveryRequest->penalty;
        $newAttended = ($penalty->attended_hours * 60) + $request->duration_minutes;
        $newPenalty = max(0, ($penalty->scheduled_hours * 60) - $newAttended);

        $penalty->update([
            'attended_hours' => $newAttended / 60,
            'penalty_hours' => $newPenalty / 60,
            'status' => $newPenalty <= 0 ? 'closed' : 'in_recovery'
        ]);

        // Actualizar la solicitud
        $session->recoveryRequest->update(['status' => 'completed']);

        return back()->with('success', 'Sesión de recuperación cerrada exitosamente.');
    }
}
