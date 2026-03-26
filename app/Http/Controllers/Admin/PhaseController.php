<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Phase;
use Illuminate\Http\Request;

class PhaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $phases = Phase::withCount('apprentices')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.phases.index', compact('phases'));
    }
    public function create()
    {
        return view('admin.phases.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:phases,name',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:abierta,cerrada',
            'technologists.*.end_date' => 'nullable|date|after_or_equal:start_date|before_or_equal:end_date',
        ], [
            'name.unique' => 'Ya existe una fase con este nombre. Por favor, elija un nombre diferente.',
            'technologists.*.end_date.after_or_equal' => 'La fecha fin del tecnólogo no puede ser anterior al inicio de la fase.',
            'technologists.*.end_date.before_or_equal' => 'La fecha fin del tecnólogo no puede ser posterior al fin de la fase.',
        ]);

        $phase = Phase::create($request->all());

        // Manejar tecnólogos
        if ($request->has('technologists')) {
            foreach ($request->technologists as $techData) {
                if (!empty($techData['name'])) {
                    $phase->technologists()->create([
                        'name' => $techData['name'],
                        'start_date' => $phase->start_date,
                        'end_date' => $techData['end_date'] ?: $phase->end_date,
                        'is_active' => $phase->is_active && $phase->status === 'abierta',
                    ]);
                }
            }
        }

        // Si es la primera fase, activarla por defecto
        if (Phase::count() === 1) {
            $phase->update(['is_active' => true]);
            $phase->technologists()->update(['is_active' => true]);
        }

        return redirect()->route('admin.phases.index')->with('success', 'Fase creada exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Phase $phase)
    {
        return view('admin.phases.edit', compact('phase'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Phase $phase)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:phases,name,' . $phase->id,
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:abierta,cerrada',
            'technologists.*.end_date' => 'nullable|date|after_or_equal:start_date|before_or_equal:end_date',
        ], [
            'name.unique' => 'Ya existe una fase con este nombre. Por favor, elija un nombre diferente.',
            'technologists.*.end_date.after_or_equal' => 'La fecha fin del tecnólogo no puede ser anterior al inicio de la fase.',
            'technologists.*.end_date.before_or_equal' => 'La fecha fin del tecnólogo no puede ser posterior al fin de la fase.',
        ]);

        $phase->update($request->all());

        // Manejar tecnólogos (Actualizar existentes o crear nuevos)
        if ($request->has('technologists')) {
            $existingIds = [];
            foreach ($request->technologists as $techData) {
                $data = [
                    'name' => $techData['name'],
                    'start_date' => $phase->start_date,
                    'end_date' => $techData['end_date'] ?: $phase->end_date,
                    'is_active' => $phase->is_active && $phase->status === 'abierta',
                ];

                if (isset($techData['id']) && !empty($techData['id'])) {
                    $tech = $phase->technologists()->find($techData['id']);
                    if ($tech) {
                        $tech->update($data);
                        $existingIds[] = $tech->id;
                    }
                } else if (!empty($techData['name'])) {
                    $newTech = $phase->technologists()->create($data);
                    $existingIds[] = $newTech->id;
                }
            }
            // Eliminar tecnólogos que no vinieron en el request (si se desea)
            // $phase->technologists()->whereNotIn('id', $existingIds)->delete();
        }

        // Si la fase se cierra o desactiva, afectar a todos
        if (!$phase->is_active || $phase->status === 'cerrada') {
            $phase->technologists()->update(['is_active' => false]);
            $phase->apprentices()->join('users', 'users.id', '=', 'apprentice_profiles.user_id')
                  ->update(['users.status' => 'inactivo']);
        }

        return redirect()->route('admin.phases.index')->with('success', 'Fase actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Phase $phase)
    {
        if ($phase->apprentices()->count() > 0) {
            return back()->with('error', 'No se puede eliminar una fase que tiene aprendices vinculados.');
        }

        if ($phase->is_active) {
            return back()->with('error', 'No se puede eliminar la fase activa.');
        }

        $phase->delete();

        return redirect()->route('admin.phases.index')->with('success', 'Fase eliminada exitosamente.');
    }

    /**
     * Activate a phase and deactivate others.
     */
    public function activate(Phase $phase)
    {
        // Desactivar todas las fases
        $activePhases = Phase::where('is_active', true)->get();
        foreach ($activePhases as $ap) {
            $ap->update(['is_active' => false]);
            $ap->technologists()->update(['is_active' => false]);
            // Desactivar aprendices y sus horarios
            $apprentices = $ap->apprentices()->get();
            foreach ($apprentices as $apprentice) {
                // Desactivar usuario
                $apprentice->user->update(['status' => 'inactivo']);
                
                // Desactivar todos sus horarios activos
                \App\Models\Schedule::where('apprentice_id', $apprentice->user_id)
                    ->update(['status' => 'inactivo']);
            }
        }

        // Activar la fase seleccionada
        $phase->update(['is_active' => true, 'status' => 'abierta']);
        $phase->technologists()->update(['is_active' => true]);
        // Activar aprendices de la fase activa
        $phase->apprentices()->join('users', 'users.id', '=', 'apprentice_profiles.user_id')
            ->update(['users.status' => 'activo']);

        return back()->with('success', "La fase '{$phase->name}' ha sido activada y todos sus integrantes habilitados.");
    }
    /**
     * Duplicate a phase.
     */
    public function duplicate(Phase $phase)
    {
        $originalName = $phase->name;
        $newName = $originalName . ' (Copia)';
        
        $counter = 1;
        while (Phase::where('name', $newName)->exists()) {
            $newName = $originalName . " (Copia {$counter})";
            $counter++;
        }

        $newPhase = $phase->replicate();
        $newPhase->name = $newName;
        $newPhase->is_active = false;
        $newPhase->status = 'abierta'; 
        $newPhase->save();

        foreach ($phase->technologists as $tech) {
            $newTech = $tech->replicate();
            $newTech->phase_id = $newPhase->id;
            $newTech->is_active = false;
            $newTech->save();
        }

        return redirect()->route('admin.phases.edit', $newPhase)->with('success', 'Plantilla generada exitosamente. Ajuste los datos de la nueva fase.');
    }
}
