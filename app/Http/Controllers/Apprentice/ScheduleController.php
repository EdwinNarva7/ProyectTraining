<?php

namespace App\Http\Controllers\Apprentice;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Schedule;

class ScheduleController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $user->load('apprenticeProfile');

        // Obtener los horarios del aprendiz logueado o de su tecnólogo
        $schedules = Schedule::where(function($q) use ($user) {
                $q->where('apprentice_id', $user->id);
                if ($user->apprenticeProfile && $user->apprenticeProfile->technologist_id) {
                    $q->orWhere('technologist_id', $user->apprenticeProfile->technologist_id);
                }
            })
            ->orderBy('weekday', 'asc')
            ->get();

        // Agrupar por día de la semana
        $schedulesByDay = $schedules->groupBy('weekday');

        return view('aprendiz.schedules.index', compact('schedules', 'schedulesByDay'));
    }
}
