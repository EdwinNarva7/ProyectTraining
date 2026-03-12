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

        // Obtener los horarios del aprendiz logueado
        $schedules = Schedule::where('apprentice_id', $user->id)
            ->orderBy('weekday', 'asc')
            ->get();

        // Agrupar por día de la semana
        $schedulesByDay = $schedules->groupBy('weekday');

        return view('aprendiz.schedules.index', compact('schedules', 'schedulesByDay'));
    }
}
