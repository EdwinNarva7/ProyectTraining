@extends('layouts.master')

@section('title', 'Reporte de Horas - Admin')
@section('page-title', 'Control de Horas de Aprendices')

@section('breadcrumb')
    <li class="breadcrumb-item active">Reporte de Horas</li>
@endsection

@section('content')
<div class="space-y-10 animate-fade-in pb-12">
    <!-- Header & Filter -->
    <div class="bg-white rounded-[2.5rem] shadow-premium border border-slate-100 p-8 overflow-hidden relative group">
        <div class="absolute top-0 right-0 w-32 h-32 bg-sena/5 rounded-full blur-3xl opacity-50 -mr-10 -mt-10 group-hover:bg-sena/10 transition-all duration-700"></div>
        
        <div class="flex flex-col lg:flex-row items-center justify-between gap-8 relative z-10">
            <div>
                <h2 class="text-3xl font-bold text-slate-800 font-outfit mb-2">Seguimiento de Tiempos</h2>
                <p class="text-slate-500 font-medium max-w-md">Consulte el progreso acumulado, semanal y diario de todos los aprendices por fase.</p>
            </div>
            
            <form action="{{ route('admin.reports.hours') }}" method="GET" class="flex flex-col md:flex-row items-end gap-4">
                <div class="w-full md:w-64">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Fase Seleccionada</label>
                    <select name="phase_id" class="w-full pl-5 pr-10 py-3.5 rounded-2xl bg-slate-50 border-slate-100 text-sm font-bold text-slate-700 appearance-none cursor-pointer focus:bg-white transition-all">
                        @foreach($phases as $phase)
                            <option value="{{ $phase->id }}" {{ $selectedPhaseId == $phase->id ? 'selected' : '' }}>
                                {{ $phase->name }} {{ $phase->is_active ? '(Activa)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="sena-gradient text-white px-8 py-4 rounded-2xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-sena/20 hover:opacity-90 transition-all flex items-center gap-2">
                    <i class="fas fa-filter"></i> Filtrar Reporte
                </button>
            </form>
        </div>
    </div>

    <!-- Ranking Table -->
    <div class="bg-white rounded-[2.5rem] shadow-premium border border-slate-100 overflow-hidden">
        <div class="p-8 border-b border-slate-50 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                <span class="w-1.5 h-6 bg-sena rounded-full"></span>
                Ranking de Aprendices por Horas
            </h3>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-slate-50 px-3 py-1.5 rounded-full">Ordenado por Total</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50/50 text-slate-400 text-[10px] uppercase tracking-[0.2em] font-bold">
                    <tr>
                        <th class="px-8 py-5">#</th>
                        <th class="px-8 py-5">Aprendiz</th>
                        <th class="px-8 py-5">Horas Totales</th>
                        <th class="px-8 py-5">Esta Semana</th>
                        <th class="px-8 py-5">Recuperadas</th>
                        <th class="px-8 py-5">Hoy</th>
                        <th class="px-8 py-5 text-right">Progreso</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($apprentices as $index => $apprentice)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-6">
                                <span class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs {{ $index < 3 ? 'bg-amber-100 text-amber-600' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $index + 1 }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl sena-gradient flex items-center justify-center text-white font-bold text-xs shadow-md shadow-sena/20 overflow-hidden">
                                        @if($apprentice->profile_photo_path)
                                            <img src="{{ Storage::url($apprentice->profile_photo_path) }}" class="w-full h-full object-cover">
                                        @else
                                            {{ strtoupper(substr($apprentice->full_name, 0, 2)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800">{{ $apprentice->full_name }}</p>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Ficha: {{ $apprentice->apprenticeProfile?->cohort }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-sm font-black text-slate-700 font-outfit">{{ $apprentice->formatMinutesToHours($apprentice->total_minutes) }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-sm font-bold text-blue-600 font-outfit">{{ $apprentice->formatMinutesToHours($apprentice->weekly_minutes) }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-sm font-bold text-amber-500 font-outfit">{{ $apprentice->formatMinutesToHours($apprentice->recovered_minutes) }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-sm font-bold text-emerald-500 font-outfit">{{ $apprentice->formatMinutesToHours($apprentice->daily_minutes) }}</span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                @php
                                    $progreso = min(100, round(($apprentice->weekly_minutes / 2400) * 100));
                                @endphp
                                <div class="flex flex-col items-end gap-1.5">
                                    <span class="text-[10px] font-black {{ $progreso >= 80 ? 'text-emerald-500' : 'text-slate-400' }} uppercase tracking-widest">{{ $progreso }}% Semanal</span>
                                    <div class="w-24 bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                        <div class="h-full {{ $progreso >= 80 ? 'bg-emerald-500' : 'bg-sena' }} transition-all duration-1000" style="width: {{ $progreso }}%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-300">
                                        <i class="fas fa-users-slash text-3xl"></i>
                                    </div>
                                    <p class="text-slate-400 font-medium">No se encontraron aprendices en esta fase.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
