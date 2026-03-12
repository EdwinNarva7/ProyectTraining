<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penalty;
use App\Models\RecoveryRequest;
use App\Models\RecoverySession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenaltyController extends Controller
{
    /**
     * Dashboard de penalizaciones
     */
    public function index(Request $request)
    {
        $query = Penalty::with('apprentice', 'schedule');

        if ($request->filled('search')) {
            $query->whereHas('apprentice', function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('apprentice_id')) {
            $query->where('apprentice_id', $request->apprentice_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $penalties = $query->orderBy('date', 'desc')->paginate(20);

        return view('admin.penalties.index', compact('penalties'));
    }

    /**
     * Listado de solicitudes de recuperación
     */
    public function recoveryRequests(Request $request)
    {
        $query = RecoveryRequest::with('apprentice', 'penalty');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.penalties.recovery_requests', compact('requests'));
    }

    /**
     * Aprobar solicitud de recuperación
     */
    public function approveRecovery(Request $request, RecoveryRequest $recoveryRequest)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:500',
            'scheduled_start_time' => 'required',
            'scheduled_end_time' => 'required'
        ]);

        if ($recoveryRequest->status !== 'pending') {
            return back()->with('error', 'Esta solicitud ya ha sido procesada.');
        }

        try {
            DB::beginTransaction();

            $recoveryRequest->update([
                'status' => 'approved',
                'admin_id' => Auth::id(),
                'admin_notes' => $request->admin_notes
            ]);

            // Al aprobar, creamos la sesión de recuperación programada
            RecoverySession::create([
                'recovery_request_id' => $recoveryRequest->id,
                'apprentice_id' => $recoveryRequest->apprentice_id,
                'date' => $recoveryRequest->requested_date,
                'scheduled_start_time' => $request->scheduled_start_time,
                'scheduled_end_time' => $request->scheduled_end_time,
                'status' => 'scheduled'
            ]);

            // Actualizar estado de la penalización
            $recoveryRequest->penalty->update(['status' => 'in_recovery']);

            DB::commit();

            return redirect()->route('admin.recovery-requests.index')
                ->with('success', 'Solicitud aprobada y sesión programada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar la aprobación: ' . $e->getMessage());
        }
    }

    /**
     * Rechazar solicitud de recuperación
     */
    public function rejectRecovery(Request $request, RecoveryRequest $recoveryRequest)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:500'
        ]);

        if ($recoveryRequest->status !== 'pending') {
            return back()->with('error', 'Esta solicitud ya ha sido procesada.');
        }

        try {
            DB::beginTransaction();

            $recoveryRequest->update([
                'status' => 'rejected',
                'admin_id' => Auth::id(),
                'admin_notes' => $request->admin_notes
            ]);

            // Si no hay más solicitudes aprobadas o en curso para esta penalización, 
            // y la penalización no tiene horas recuperadas, podríamos volverla a 'pending'
            // Pero por ahora lo dejamos como está ya que 'rejected' solo afecta a la solicitud.

            DB::commit();
            return redirect()->route('admin.recovery-requests.index')
                ->with('success', 'Solicitud rechazada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar el rechazo: ' . $e->getMessage());
        }
    }
}
