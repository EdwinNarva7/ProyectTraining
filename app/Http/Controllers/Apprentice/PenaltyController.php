<?php

namespace App\Http\Controllers\Apprentice;

use App\Http\Controllers\Controller;
use App\Models\Penalty;
use App\Models\RecoveryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenaltyController extends Controller
{
    /**
     * Ver listado de penalizaciones y solicitudes
     */
    public function index()
    {
        $apprenticeId = Auth::id();

        $penalties = Penalty::where('apprentice_id', $apprenticeId)
            ->with('schedule')
            ->orderBy('date', 'desc')
            ->paginate(10);

        $stats = [
            'total_penalty_hours' => Penalty::where('apprentice_id', $apprenticeId)->sum('penalty_hours'),
            'pending_hours' => Penalty::where('apprentice_id', $apprenticeId)->where('status', 'pending')->sum('penalty_hours'),
            'completed_hours' => Penalty::where('apprentice_id', $apprenticeId)->where('status', 'closed')->sum('penalty_hours'),
        ];

        return view('aprendiz.penalties.index', compact('penalties', 'stats'));
    }

    /**
     * Ver detalle de una penalización
     */
    public function show(Penalty $penalty)
    {
        $this->authorizeAccess($penalty);

        $penalty->load(['recoveryRequests.admin', 'schedule']);

        return view('aprendiz.penalties.show', compact('penalty'));
    }

    /**
     * Crear solicitud de recuperación
     */
    public function requestRecovery(Request $request, Penalty $penalty)
    {
        $this->authorizeAccess($penalty);

        if ($penalty->status === 'closed') {
            return back()->with('error', 'Esta penalización ya ha sido saldada.');
        }

        $request->validate([
            'requested_date' => 'required|date|after_or_equal:today',
            'hours_requested' => 'required|numeric|min:0.01|max:' . ($penalty->penalty_hours + 0.01),
        ]);

        RecoveryRequest::create([
            'penalty_id' => $penalty->id,
            'apprentice_id' => Auth::id(),
            'requested_date' => $request->requested_date,
            'hours_requested' => $request->hours_requested,
            'status' => 'pending'
        ]);

        return redirect()->route('apprentice.penalties.requests')
            ->with('success', 'Solicitud de recuperación enviada exitosamente.');
    }

    /**
     * Ver listado de solicitudes de recuperación
     */
    public function requests()
    {
        $apprenticeId = Auth::id();

        $requests = RecoveryRequest::where('apprentice_id', $apprenticeId)
            ->with(['penalty', 'admin'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('aprendiz.penalties.requests', compact('requests'));
    }

    private function authorizeAccess(Penalty $penalty)
    {
        if ($penalty->apprentice_id !== Auth::id()) {
            abort(403);
        }
    }
}
