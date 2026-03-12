@extends('layouts.masteraprendiz')

@section('title', 'Sesiones de Trabajo - SIEAP')
@section('page-title', 'Mis Jornadas Consolidadas')

@section('breadcrumb')
    <span class="text-slate-400">/</span>
    <span class="text-slate-600 font-bold uppercase tracking-widest text-[10px]">Sesiones</span>
@endsection

@section('content')
    <div class="space-y-8 animate-fade-in pb-12">
        
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-6">
                <a href="{{ route('apprentice.attendance.index') }}" 
                   class="group w-12 h-12 rounded-2xl bg-white border border-slate-100 text-slate-400 flex items-center justify-center hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all shadow-premium active:scale-90">
                    <i class="fas fa-chevron-left group-hover:-translate-x-1 transition-transform"></i>
                </a>
                <div>
                    <h1 class="text-4xl font-bold text-slate-800 tracking-tight font-outfit">
                        Mis Jornadas <span class="text-sena">Consolidadas</span>
                    </h1>
                    <p class="text-slate-500 mt-2 font-medium text-lg">Consolidado diario de horas y cumplimiento de horario.</p>
                </div>
            </div>
        </div>

        {{-- Main Sessions Card --}}
        <div class="bg-white rounded-[3rem] shadow-premium border border-slate-50 overflow-hidden group">
            @if($sessions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-50">
                                <th class="px-10 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Fecha Mensual</th>
                                <th class="px-8 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Intervalo</th>
                                <th class="px-8 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Tiempo Efectivo</th>
                                <th class="px-10 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Evolución</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($sessions as $session)
                                <tr class="hover:bg-slate-50/40 transition-all group/item">
                                    <td class="px-10 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 bg-white rounded-2xl flex flex-col items-center justify-center text-slate-800 shadow-sm border border-slate-100 group-hover/item:rotate-6 transition-transform">
                                                <span class="text-[9px] font-black uppercase leading-none mb-0.5 text-slate-400">{{ $session->start_at->translatedFormat('M') }}</span>
                                                <span class="text-lg font-black font-outfit leading-none">{{ $session->start_at->format('d') }}</span>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-xs font-black text-slate-700 tracking-tight uppercase">{{ $session->start_at->translatedFormat('l') }}</span>
                                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">{{ $session->start_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="flex flex-col">
                                                <span class="text-[9px] font-black text-emerald-500 uppercase tracking-widest leading-none mb-1">Entrada</span>
                                                <span class="text-sm font-black text-slate-600 font-outfit uppercase">{{ $session->start_at->format('H:i') }}</span>
                                            </div>
                                            <div class="w-8 h-px bg-slate-100 italic text-[10px] text-slate-300 text-center flex items-center justify-center">→</div>
                                            <div class="flex flex-col">
                                                <span class="text-[9px] font-black text-rose-500 uppercase tracking-widest leading-none mb-1">Salida</span>
                                                <span class="text-sm font-black text-slate-600 font-outfit uppercase">
                                                    {{ $session->end_at ? $session->end_at->format('H:i') : '--:--' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        @if($session->duration_minutes)
                                            <div class="inline-flex flex-col items-center">
                                                <div class="text-2xl font-black text-indigo-500 font-outfit tracking-tighter leading-none mb-1">
                                                    {{ round($session->duration_minutes / 60, 1) }}<span class="text-sm font-medium ml-0.5">h</span>
                                                </div>
                                                <span class="text-[9px] font-black text-slate-300 uppercase tracking-[0.2em] leading-none">{{ $session->duration_minutes }} MINUTOS</span>
                                            </div>
                                        @else
                                            <div class="flex items-center justify-center gap-2 text-amber-500 animate-pulse">
                                                <i class="fas fa-circle-notch fa-spin text-xs"></i>
                                                <span class="text-[10px] font-black uppercase tracking-widest italic">Calculando...</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-10 py-6 text-right">
                                        <div class="flex flex-col items-end gap-3">
                                            @if($session->end_at)
                                                @if($session->daily_progress)
                                                    @if($session->daily_progress['has_pending'])
                                                        <div class="flex flex-col items-end gap-2">
                                                            <span class="inline-flex items-center gap-2 px-3 py-1 bg-amber-50 text-amber-600 rounded-lg text-[9px] font-black uppercase tracking-widest border border-amber-100 shadow-sm">
                                                                <i class="fas fa-hourglass-start"></i> Faltan: {{ $session->daily_progress['pending_hours'] }}h {{ $session->daily_progress['pending_minutes'] }}m
                                                            </span>
                                                            <div class="w-24 h-1.5 bg-slate-100 rounded-full overflow-hidden p-0.5 border border-slate-200">
                                                                <div class="h-full bg-amber-500 rounded-full transition-all duration-1000" style="width: {{ $session->daily_progress['progress_percent'] }}%"></div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-emerald-50 text-sena rounded-full text-[10px] font-black uppercase tracking-widest border border-sena/10 shadow-sm">
                                                            <i class="fas fa-check-double text-xs"></i> Completada
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="inline-flex items-center px-4 py-1.5 bg-slate-50 text-slate-400 rounded-full text-[10px] font-black uppercase tracking-widest border border-slate-100">Corte Cerrado</span>
                                                @endif
                                            @else
                                                <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-sky-50 text-sky-500 rounded-full text-[10px] font-black uppercase tracking-widest border border-sky-100 shadow-lg shadow-sky-100 group-hover/item:scale-105 transition-transform duration-500">
                                                    <i class="fas fa-running animate-bounce-slow"></i> Sesión en Curso
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-10 py-8 bg-slate-50/50 border-t border-slate-50">
                    <div class="pagination-premium">
                        {{ $sessions->links() }}
                    </div>
                </div>
            @else
                <div class="py-24 flex flex-col items-center justify-center text-center space-y-8">
                    <div class="w-32 h-32 bg-slate-50 rounded-[3.5rem] flex items-center justify-center text-slate-200 shadow-inner border border-slate-100">
                        <i class="fas fa-layer-group text-4xl"></i>
                    </div>
                    <div class="space-y-3">
                        <h3 class="text-2xl font-black text-slate-800 font-outfit uppercase tracking-tighter">Sin trayectorias registradas</h3>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] max-w-sm mx-auto leading-relaxed">Tus jornadas de trabajo diario se consolidan aquí al finalizar cada sesión.</p>
                    </div>
                    <a href="{{ route('apprentice.attendance.index') }}" 
                       class="px-12 py-4 sena-gradient text-white rounded-[2rem] text-xs font-black uppercase tracking-[0.2em] shadow-2xl shadow-sena/30 hover:shadow-sena/50 active:scale-95 transition-all flex items-center gap-3">
                        <i class="fas fa-plus"></i> Iniciar Jornada Hoy
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
