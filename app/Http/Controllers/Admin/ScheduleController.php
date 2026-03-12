<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\User;
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
        $query = Schedule::with(['apprentice', 'createdBy']);

        // Filtros
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
        $apprentices = User::whereHas('role', function($q) {
            $q->where('name', 'Aprendiz');
        })->where('status', 'activo')->get();

        return view('admin.schedules.index', compact('schedules', 'apprentices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $apprentices = User::whereHas('role', function($q) {
            $q->where('name', 'Aprendiz');
        })->where('status', 'activo')->with('apprenticeProfile')->get();

        return view('admin.schedules.create', compact('apprentices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'apprentice_id' => 'required|exists:users,id',
            'weekday' => 'required|integer|between:1,7',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'status' => 'required|in:activo,inactivo',
        ], [
            'apprentice_id.required' => 'Debe seleccionar un aprendiz.',
            'apprentice_id.exists' => 'El aprendiz seleccionado no existe.',
            'weekday.required' => 'Debe seleccionar un día de la semana.',
            'weekday.between' => 'El día debe estar entre 1 y 7.',
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

        // Verificar si ya existe un horario para este aprendiz en este día
        $existingSchedule = Schedule::where('apprentice_id', $request->apprentice_id)
            ->where('weekday', $request->weekday)
            ->where('status', 'activo')
            ->first();

        if ($existingSchedule) {
            return redirect()->back()
                ->withErrors(['weekday' => 'Ya existe un horario activo para este aprendiz en este día de la semana.'])
                ->withInput();
        }

        try {
            $schedule = Schedule::create([
                'apprentice_id' => $request->apprentice_id,
                'weekday' => $request->weekday,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'status' => $request->status,
                'created_by' => Auth::id(),
            ]);

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
        $apprentices = User::whereHas('role', function($q) {
            $q->where('name', 'Aprendiz');
        })->where('status', 'activo')->with('apprenticeProfile')->get();

        return view('admin.schedules.edit', compact('schedule', 'apprentices'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        $validator = Validator::make($request->all(), [
            'apprentice_id' => 'required|exists:users,id',
            'weekday' => 'required|integer|between:1,7',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'status' => 'required|in:activo,inactivo',
        ], [
            'apprentice_id.required' => 'Debe seleccionar un aprendiz.',
            'apprentice_id.exists' => 'El aprendiz seleccionado no existe.',
            'weekday.required' => 'Debe seleccionar un día de la semana.',
            'weekday.between' => 'El día debe estar entre 1 y 7.',
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

        // Verificar si ya existe otro horario para este aprendiz en este día
        $existingSchedule = Schedule::where('apprentice_id', $request->apprentice_id)
            ->where('weekday', $request->weekday)
            ->where('status', 'activo')
            ->where('id', '!=', $schedule->id)
            ->first();

        if ($existingSchedule) {
            return redirect()->back()
                ->withErrors(['weekday' => 'Ya existe un horario activo para este aprendiz en este día de la semana.'])
                ->withInput();
        }

        try {
            $schedule->update([
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
        
        $schedules = Schedule::where('apprentice_id', $apprenticeId)
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
