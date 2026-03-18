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

    /**
     * Show the form for creating a new resource.
     */
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
            'name' => 'required|string|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:abierta,cerrada',
        ]);

        $phase = Phase::create($request->all());

        // Si es la primera fase, activarla por defecto
        if (Phase::count() === 1) {
            $phase->update(['is_active' => true]);
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
            'name' => 'required|string|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:abierta,cerrada',
        ]);

        $phase->update($request->all());

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
        Phase::where('is_active', true)->update(['is_active' => false]);

        // Activar la fase seleccionada
        $phase->update(['is_active' => true]);

        return back()->with('success', "La fase '{$phase->name}' ha sido activada como la fase actual.");
    }
}
