@extends('layouts.masteraprendiz')

@section('title', 'Mi Progreso de Horas')
@section('page-title', 'Mi Reloj de Horas')

@section('breadcrumb')
    <span class="text-slate-400 font-bold mx-2">/</span>
    <span class="text-slate-600 font-bold uppercase tracking-widest text-[10px]">Progreso de Horas</span>
@endsection

@section('content')
<div class="space-y-8 animate-fade-in pb-12">
    <!-- Main Stats Header -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Acumulado -->
        <div class="bg-white p-6 rounded-[2rem] shadow-premium border border-slate-100 group hover:shadow-xl transition-all duration-500 overflow-hidden relative">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-sena/5 rounded-full blur-2xl group-hover:bg-sena/10 transition-colors"></div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 sena-gradient rounded-xl flex items-center justify-center text-white shadow-lg shadow-sena/20">
                    <i class="fas fa-hourglass-half text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Total Acumulado</p>
                    <h3 class="text-2xl font-black text-slate-800 font-outfit">{{ $stats['total_hours'] }}</h3>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 font-medium">Horas totales en la fase actual: <span class="text-sena font-bold">{{ $activePhase ? $activePhase->name : 'N/A' }}</span></p>
        </div>

        <!-- Progreso de la Fase -->
        <div class="bg-white p-6 rounded-[2rem] shadow-premium border border-slate-100 group hover:shadow-xl transition-all duration-500 overflow-hidden relative">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-blue-50 rounded-full blur-2xl group-hover:bg-blue-100 transition-colors"></div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                    <i class="fas fa-bullseye text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Meta de la Fase</p>
                    <h3 class="text-2xl font-black text-slate-800 font-outfit">{{ $stats['expected_hours'] }}</h3>
                </div>
            </div>
            <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden mb-1">
                <div class="bg-blue-600 h-full transition-all duration-1000" style="width: {{ min(100, $phaseProgress) }}%"></div>
            </div>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">{{ $phaseProgress }}% completado vs lo esperado</p>
        </div>

        <!-- Hoy -->
        <div class="bg-white p-6 rounded-[2rem] shadow-premium border border-slate-100 group hover:shadow-xl transition-all duration-500 overflow-hidden relative">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-50 rounded-full blur-2xl group-hover:bg-emerald-100 transition-colors"></div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-emerald-100">
                    <i class="fas fa-bolt text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Laborado Hoy</p>
                    <h3 class="text-2xl font-black text-slate-800 font-outfit">{{ $stats['daily_hours'] }}</h3>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 font-medium">Tiempo registrado el día de hoy.</p>
        </div>

        <!-- Recuperadas -->
        <div class="bg-white p-6 rounded-[2rem] shadow-premium border border-slate-100 group hover:shadow-xl transition-all duration-500 overflow-hidden relative">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-amber-50 rounded-full blur-2xl group-hover:bg-amber-100 transition-colors"></div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-amber-100">
                    <i class="fas fa-tools text-xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Recuperadas</p>
                    <h3 class="text-2xl font-black text-slate-800 font-outfit">{{ $stats['recovered_hours'] }}</h3>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 font-medium">Total de horas sumadas por recuperación.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Chart Section -->
        <div class="lg:col-span-2 bg-white p-8 rounded-[2.5rem] shadow-premium border border-slate-100">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-xl font-bold text-slate-800 font-outfit">Historial de Actividad</h3>
                    <p class="text-xs text-slate-400 font-medium">Horas laboradas en los últimos 14 días</p>
                </div>
                <div class="flex gap-2">
                    <span class="flex items-center gap-1.5 px-3 py-1 bg-sena/5 text-sena text-[10px] font-bold rounded-full border border-sena/10">
                        <span class="w-1.5 h-1.5 bg-sena rounded-full"></span> Horas/Día
                    </span>
                </div>
            </div>
            
            <div class="h-64 flex items-end justify-between gap-2 px-2">
                @php
                    $maxHours = $history->max() ?: 1;
                @endphp
                @foreach($history as $date => $hours)
                    <div class="flex-1 flex flex-col items-center group relative">
                        <div class="w-full bg-slate-50 rounded-t-lg group-hover:bg-sena/10 transition-all duration-300 relative" 
                             style="height: {{ ($hours / $maxHours) * 100 }}%">
                            <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10 font-bold">
                                {{ number_format($hours, 1) }}h
                            </div>
                            <div class="absolute inset-0 bg-sena opacity-20 rounded-t-lg scale-y-0 group-hover:scale-y-100 origin-bottom transition-transform duration-500"></div>
                        </div>
                        <span class="text-[8px] font-black text-slate-400 uppercase tracking-tighter mt-3 transform -rotate-45 md:rotate-0">
                            {{ Carbon\Carbon::parse($date)->format('d M') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recovery Info -->
        <div class="bg-white p-8 rounded-[2.5rem] shadow-premium border border-slate-100 flex flex-col justify-between overflow-hidden relative group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-amber-50 rounded-full blur-3xl -mr-16 -mt-16 group-hover:bg-amber-100 transition-colors"></div>
            
            <div class="relative z-10">
                <h3 class="text-xl font-bold text-slate-800 font-outfit mb-2">Resumen de Recuperación</h3>
                <p class="text-xs text-slate-500 font-medium leading-relaxed">Las horas recuperadas son aquellas que has repuesto mediante el módulo de sanciones o solicitudes especiales.</p>
                
                <div class="mt-8 space-y-6">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Minutos Totales</span>
                        <span class="text-lg font-black text-slate-800 font-outfit">{{ number_format($stats['total_minutes']) }} min</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Equivalencia</span>
                        <span class="text-lg font-black text-amber-600 font-outfit">{{ $stats['recovered_hours'] }}</span>
                    </div>
                </div>
            </div>

            <div class="relative z-10 mt-8">
                <a href="{{ route('apprentice.recovery.index') }}" class="w-full py-4 bg-amber-500 hover:bg-amber-600 text-white rounded-2xl text-xs font-black uppercase tracking-[0.2em] transition-all shadow-lg shadow-amber-100 flex items-center justify-center gap-2">
                    <i class="fas fa-plus-circle"></i> Nueva Recuperación
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
