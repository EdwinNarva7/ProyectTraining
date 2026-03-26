<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Phase;
use App\Models\Technologist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Schedule::with(['technologist.phase', 'apprentice', 'createdBy']);

        // Filtros
        if ($request->filled('technologist_id')) {
            $query->where('technologist_id', $request->technologist_id);
        }

        if ($request->filled('apprentice_id')) {
            $query->where('apprentice_id', $request->apprentice_id);
        }

        if ($request->filled('weekday')) {
            $query->where('weekday', $request->weekday);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $schedules = $query->orderBy('created_at', 'desc')->paginate(15);
        $phases = Phase::with('technologists')->get();
        $apprentices = User::whereHas('role', function($q) {
            $q->where('name', 'Aprendiz');
        })->where('status', 'activo')->get();

        return view('admin.schedules.index', compact('schedules', 'phases', 'apprentices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $phases = Phase::with('technologists')->get();
        // Mantener aprendices por si se requiere un horario individual (opcional)
        $apprentices = User::whereHas('role', function($q) {
            $q->where('name', 'Aprendiz');
        })->where('status', 'activo')->with('apprenticeProfile')->get();

        return view('admin.schedules.create', compact('phases', 'apprentices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'technologist_id' => 'required_without:apprentice_id|exists:technologists,id',
            'apprentice_id' => 'nullable|exists:users,id',
            'weekday' => 'required|in:1,2,3,4,5,6,7,all',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'status' => 'required|in:activo,inactivo',
        ], [
            'technologist_id.required_without' => 'Debe seleccionar un tecnólogo.',
            'technologist_id.exists' => 'El tecnólogo seleccionado no existe.',
            'weekday.required' => 'Debe seleccionar un día de la semana.',
            'weekday.in' => 'Día de la semana no válido.',
            'start_time.required' => 'Debe especificar la hora de inicio.',
            'start_time.date_format' => 'El formato de hora de inicio no es válido.',
            'end_time.required' => 'Debe especificar la hora de fin.',
            'end_time.date_format' => 'El formato de hora de fin no es válido.',
            'end_time.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
            'status.required' => 'Debe seleccionar un estado.',
            'status.in' => 'El estado debe ser activo o inactivo.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $weekdays = $request->weekday === 'all' ? [1, 2, 3, 4, 5] : [$request->weekday];

        foreach ($weekdays as $day) {
            // Verificar colisión de horarios activos
            $query = Schedule::where('weekday', $day)
                ->where('status', 'activo');
                
            if ($request->technologist_id) {
                $query->where('technologist_id', $request->technologist_id);
            } else {
                $query->where('apprentice_id', $request->apprentice_id);
            }

            if ($query->exists()) {
                $dayName = \App\Models\Schedule::make(['weekday' => $day])->weekday_name;
                return redirect()->back()
                    ->withErrors(['weekday' => 'Ya existe un horario activo para este ' . ($request->technologist_id ? 'tecnólogo' : 'aprendiz') . ' el día ' . $dayName . '.'])
                    ->withInput();
            }
        }

        try {
            foreach ($weekdays as $day) {
                Schedule::create([
                    'technologist_id' => $request->technologist_id,
                    'apprentice_id' => $request->apprentice_id,
                    'weekday' => $day,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'status' => $request->status,
                    'created_by' => Auth::id(),
                ]);
            }

            return redirect()->route('admin.schedules.index')
                ->with('success', 'Horario creado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Error al crear el horario: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        $schedule->load(['apprentice.apprenticeProfile', 'createdBy']);
        
        return view('admin.schedules.show', compact('schedule'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule)
    {
        $phases = Phase::with('technologists')->get();
        $apprentices = User::whereHas('role', function($q) {
            $q->where('name', 'Aprendiz');
        })->where('status', 'activo')->with('apprenticeProfile')->get();

        return view('admin.schedules.edit', compact('schedule', 'phases', 'apprentices'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        $validator = Validator::make($request->all(), [
            'technologist_id' => 'required_without:apprentice_id|exists:technologists,id',
            'apprentice_id' => 'nullable|exists:users,id',
            'weekday' => 'required|integer|between:1,7',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'status' => 'required|in:activo,inactivo',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Verificar colisión
        $query = Schedule::where('weekday', $request->weekday)
            ->where('status', 'activo')
            ->where('id', '!=', $schedule->id);
            
        if ($request->technologist_id) {
            $query->where('technologist_id', $request->technologist_id);
        } else {
            $query->where('apprentice_id', $request->apprentice_id);
        }

        if ($query->exists()) {
            return redirect()->back()
                ->withErrors(['weekday' => 'Ya existe un horario activo para este ' . ($request->technologist_id ? 'tecnólogo' : 'aprendiz') . ' en este día.'])
                ->withInput();
        }

        try {
            $schedule->update([
                'technologist_id' => $request->technologist_id,
                'apprentice_id' => $request->apprentice_id,
                'weekday' => $request->weekday,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'status' => $request->status,
            ]);

            return redirect()->route('admin.schedules.index')
                ->with('success', 'Horario actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Error al actualizar el horario: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        try {
            $schedule->delete();
            return redirect()->route('admin.schedules.index')
                ->with('success', 'Horario eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Error al eliminar el horario: ' . $e->getMessage()]);
        }
    }

    /**
     * Obtener horarios de un aprendiz específico (AJAX)
     */
    public function getApprenticeSchedules(Request $request)
    {
        $apprenticeId = $request->apprentice_id;
        $apprentice = User::with('apprenticeProfile')->find($apprenticeId);

        if (!$apprentice) return response()->json([]);
        
        $schedules = Schedule::where(function($q) use ($apprentice) {
                $q->where('apprentice_id', $apprentice->id);
                if ($apprentice->apprenticeProfile?->technologist_id) {
                    $q->orWhere('technologist_id', $apprentice->apprenticeProfile->technologist_id);
                }
            })
            ->where('status', 'activo')
            ->orderBy('weekday')
            ->get();

        return response()->json($schedules);
    }

    /**
     * Cambiar estado del horario
     */
    public function toggleStatus(Schedule $schedule)
    {
        try {
            $schedule->update([
                'status' => $schedule->status === 'activo' ? 'inactivo' : 'activo'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado exitosamente',
                'new_status' => $schedule->status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado'
            ], 500);
        }
    }
}
