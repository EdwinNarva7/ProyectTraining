@extends('layouts.masteraprendiz')

@section('title', 'Mis Horarios - SIAP Aprendiz')
@section('page-title', 'Mis Compromisos Semanales')

@section('breadcrumb')
    <span class="text-slate-400">/</span>
    <span class="text-slate-600 font-bold uppercase tracking-widest text-[10px]">Horarios</span>
@endsection

@section('content')
    <div class="space-y-10 animate-fade-in pb-12">
        
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h1 class="text-4xl font-black text-slate-800 tracking-tight font-outfit uppercase">
                    MI <span class="text-sena font-black">CRONOGRAMA</span>
                </h1>
                <p class="text-slate-500 font-bold text-[11px] uppercase tracking-widest opacity-60">Visualización de jornadas programadas para la etapa práctica</p>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="px-6 py-3 bg-white border border-slate-100 rounded-2xl shadow-premium text-[10px] font-black uppercase tracking-widest text-sena flex items-center gap-3">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-sena opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-sena"></span>
                    </span>
                    Ciclo Lectivo Actual
                </div>
            </div>
        </div>

        @if(count($schedules) > 0)
            {{-- Metrics Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Total Horas -->
                <div class="bg-white p-8 rounded-[2.5rem] shadow-premium border border-slate-50 group hover:shadow-xl transition-all">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center text-xl shadow-inner group-hover:bg-blue-500 group-hover:text-white transition-all">
                            <i class="fas fa-clock"></i>
                        </div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Carga Semanal</span>
                    </div>
                    @php
                        $totalMinutes = 0;
                        foreach($schedules as $schedule) {
                            $start = \Carbon\Carbon::parse($schedule->start_time);
                            $end = \Carbon\Carbon::parse($schedule->end_time);
                            $totalMinutes += $start->diffInMinutes($end);
                        }
                        $hours = floor($totalMinutes / 60);
                        $minutes = $totalMinutes % 60;
                    @endphp
                    <div class="flex items-baseline gap-2">
                        <span class="text-5xl font-black text-slate-800 font-outfit tracking-tighter">{{ $hours }}<span class="text-xl opacity-40">h</span></span>
                        @if($minutes > 0)
                            <span class="text-lg font-black text-slate-400">{{ $minutes }}m</span>
                        @endif
                    </div>
                </div>

                <!-- Días Activos -->
                <div class="bg-white p-8 rounded-[2.5rem] shadow-premium border border-slate-50 group hover:shadow-xl transition-all">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 bg-sena/10 text-sena rounded-2xl flex items-center justify-center text-xl shadow-inner group-hover:bg-sena group-hover:text-white transition-all">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Días de Jornada</span>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-5xl font-black text-slate-800 font-outfit tracking-tighter">{{ collect($schedulesByDay)->count() }}</span>
                        <span class="text-sm font-black text-slate-400 uppercase tracking-widest">Asignados</span>
                    </div>
                </div>

                <!-- Estatus Horario -->
                <div class="bg-white p-8 rounded-[2.5rem] shadow-premium border border-slate-50 group hover:shadow-xl transition-all">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 bg-indigo-50 text-indigo-500 rounded-2xl flex items-center justify-center text-xl shadow-inner group-hover:bg-indigo-500 group-hover:text-white transition-all">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Estado de Ficha</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-5xl font-black text-slate-800 font-outfit tracking-tighter">{{ $schedules->where('status', 'active')->count() }}<span class="text-lg opacity-20">/</span>{{ count($schedules) }}</span>
                        <span class="px-3 py-1 bg-emerald-50 text-sena rounded-lg text-[9px] font-black uppercase tracking-widest border border-emerald-100">Activo</span>
                    </div>
                </div>
            </div>

            {{-- Main Schedule Card --}}
            <div class="bg-white rounded-[3.5rem] shadow-premium border border-slate-50 overflow-hidden group">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-50">
                                <th class="px-10 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Día Semanal</th>
                                <th class="px-8 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Bloque de Entrada</th>
                                <th class="px-8 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Bloque de Salida</th>
                                <th class="px-8 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Inversión</th>
                                <th class="px-10 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Efectividad</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @php
                                $weekdays = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
                            @endphp
                            @foreach($schedulesByDay as $weekday => $daySchedules)
                                @php
                                    $isToday = now()->dayOfWeek == $weekday;
                                    $isWorkDay = in_array($weekday, [1, 2, 3, 4, 5]);
                                @endphp
                                @foreach($daySchedules as $schedule)
                                    <tr class="hover:bg-slate-50/40 transition-all group/item {{ $isToday ? 'bg-sena/[0.02]' : '' }}">
                                        <td class="px-10 py-6">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-black text-sm border-2 transition-all {{ $isToday ? 'bg-sena text-white border-sena scale-110 shadow-lg shadow-sena/20' : 'bg-white text-slate-400 border-slate-50' }}">
                                                    {{ substr($weekdays[$weekday], 0, 2) }}
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-black text-slate-800 uppercase tracking-tight font-outfit">{{ $weekdays[$weekday] }}</span>
                                                    @if($isToday)
                                                        <span class="text-[9px] font-black text-sena uppercase tracking-widest animate-pulse">Día Actual</span>
                                                    @elseif(!$isWorkDay)
                                                        <span class="text-[9px] font-black text-amber-500 uppercase tracking-widest italic opacity-60">Fin de Semana</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-sena flex items-center justify-center text-xs">
                                                    <i class="fas fa-door-open"></i>
                                                </div>
                                                <span class="text-lg font-black text-slate-700 font-outfit leading-none">{{ $schedule->start_time->format('H:i') }}</span>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center text-xs">
                                                    <i class="fas fa-door-closed"></i>
                                                </div>
                                                <span class="text-lg font-black text-slate-700 font-outfit leading-none">{{ $schedule->end_time->format('H:i') }}</span>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            @php
                                                $start = \Carbon\Carbon::parse($schedule->start_time);
                                                $end = \Carbon\Carbon::parse($schedule->end_time);
                                                $totalMinutes = $start->diffInMinutes($end);
                                                $hours = floor($totalMinutes / 60);
                                                $minutes = $totalMinutes % 60;
                                                $timeDisplay = $hours > 0 ? "{$hours}h" : '';
                                                if($minutes > 0) {
                                                    $timeDisplay .= ($hours > 0 ? ' ' : '') . "{$minutes}m";
                                                }
                                            @endphp
                                            <span class="inline-flex items-center px-4 py-1.5 bg-slate-900 text-white rounded-full text-[10px] font-black uppercase tracking-widest group-hover/item:bg-indigo-600 transition-colors">
                                                {{ $timeDisplay ?: '0m' }}
                                            </span>
                                        </td>
                                        <td class="px-10 py-6 text-right">
                                            @if($schedule->status === 'active')
                                                <div class="flex flex-col items-end">
                                                    <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-emerald-50 text-sena rounded-full text-[9px] font-black uppercase tracking-widest border border-emerald-100 shadow-sm">
                                                        <i class="fas fa-circle text-[6px]"></i> Vigente
                                                    </span>
                                                </div>
                                            @else
                                                <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-slate-50 text-slate-300 rounded-full text-[9px] font-black uppercase tracking-widest border border-slate-100 italic">
                                                    Caduco
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-10 py-8 bg-slate-50/50 border-t border-slate-50 flex items-center justify-between">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3em]">Cualquier ajuste requiere solicitud vía coordinación administrativa.</p>
                    <div class="flex items-center gap-2 text-indigo-400 group-hover:translate-x-1 transition-transform cursor-help">
                        <i class="fas fa-info-circle"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest">Gestión de Calendario</span>
                    </div>
                </div>
            </div>
        @else
            {{-- Empty State --}}
            <div class="bg-white rounded-[4rem] shadow-premium border-4 border-dashed border-slate-50 p-24 text-center">
                <div class="w-32 h-32 bg-slate-50 rounded-[3.5rem] flex items-center justify-center text-slate-200 shadow-inner border border-slate-100 mx-auto mb-10">
                    <i class="fas fa-calendar-times text-5xl"></i>
                </div>
                <h3 class="text-3xl font-black text-slate-800 font-outfit uppercase tracking-tighter mb-4">Sin Horarios Programados</h3>
                <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] max-w-sm mx-auto leading-relaxed mb-12 opacity-60">Parece que aún no tienes una ficha de horario asignada para esta semana.</p>
                <a href="{{ route('apprentice.dashboard') }}" class="px-12 py-5 sena-gradient text-white rounded-[2rem] text-xs font-black uppercase tracking-[0.2em] shadow-2xl shadow-sena/30 hover:scale-[1.05] transition-all">
                    Volver al Inicio
                </a>
            </div>
        @endif
    </div>
@endsection
