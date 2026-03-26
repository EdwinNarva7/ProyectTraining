<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Models\Penalty;
use App\Models\RecoveryRequest;
use App\Models\RecoverySession;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $pendingPenalties = Penalty::where('status', 'pending')->count();
        $activePenalties = Penalty::where('status', 'active')->count();
        
        $pendingRequests = RecoveryRequest::where('status', 'pending')->count();
        $approvedRequests = RecoveryRequest::where('status', 'approved')->count();
        
        $scheduledSessions = RecoverySession::where('status', 'scheduled')->count();
        $inProgressSessions = RecoverySession::where('status', 'in_progress')->count();

        // Obtener solicitudes recientes
        $recentRequests = RecoveryRequest::with('apprentice')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Sesiones activas (hoy)
        $activeSessionsToday = RecoverySession::with('apprentice')
            ->whereDate('date', Carbon::today())
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->get();

        return view('gerente.dashboard', compact(
            'pendingPenalties',
            'activePenalties',
            'pendingRequests',
            'approvedRequests',
            'scheduledSessions',
            'inProgressSessions',
            'recentRequests',
            'activeSessionsToday'
        ));
    }
}
