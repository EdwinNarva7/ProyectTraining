@extends('layouts.masteraprendiz')

@section('title', 'Mis Sesiones de Recuperación')
@section('page-title', 'Centro de Jornadas Especiales')

@section('breadcrumb')
    <span class="text-slate-400">/</span>
    <span class="text-slate-600 font-bold uppercase tracking-widest text-[10px]">Recuperaciones</span>
@endsection

@section('content')
    <div class="space-y-10 animate-fade-in pb-12">
        
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h1 class="text-4xl font-black text-slate-800 tracking-tight font-outfit uppercase">
                    JORNADAS DE <span class="text-emerald-500 font-black">RECUPERACIÓN</span>
                </h1>
                <p class="text-slate-500 font-bold text-[11px] uppercase tracking-widest opacity-60">Seguimiento y ejecución de sesiones para saldo de deudas</p>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="px-6 py-3 bg-white border border-slate-100 rounded-2xl shadow-premium text-[10px] font-black uppercase tracking-widest text-emerald-500 flex items-center gap-3">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-500 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    Compensación Activa
                </div>
            </div>
        </div>

        {{-- Active Session Alert --}}
        @if($activeSession)
            <div class="bg-slate-900 rounded-[3.5rem] p-10 text-white shadow-2xl relative overflow-hidden group">
                <div class="absolute inset-0 bg-emerald-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-1000"></div>
                <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                    <div class="flex items-center gap-6 text-center md:text-left">
                        <div class="w-16 h-16 rounded-2xl sena-gradient flex items-center justify-center text-2xl shadow-xl animate-pulse">
                            <i class="fas fa-play"></i>
                        </div>
                        <div>
                            <span class="text-[9px] font-black uppercase tracking-[0.3em] text-emerald-400 mb-2 block">Sesión en progreso</span>
                            <h2 class="text-3xl font-black font-outfit uppercase tracking-tighter">¡ESTÁS RECUPERANDO TIEMPO!</h2>
                            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1 opacity-70">Sincronizado a las {{ Carbon\Carbon::parse($activeSession->start_time)->format('H:i') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('apprentice.recovery.show', $activeSession) }}"
                       class="px-10 py-5 bg-white text-slate-900 rounded-[2rem] font-black uppercase tracking-[0.2em] text-[11px] hover:bg-emerald-500 hover:text-white hover:scale-105 active:scale-95 transition-all shadow-xl shadow-black/20 flex items-center gap-3">
                        <i class="fas fa-tachometer-alt"></i>
                        Ver Panel de Control
                    </a>
                </div>
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-white/5 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-1000"></div>
            </div>
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-10">
            
            {{-- Sessions List --}}
            <div class="xl:col-span-8 space-y-8">
                <div class="flex items-center gap-4">
                    <div class="w-2.5 h-6 bg-emerald-500 rounded-full"></div>
                    <h3 class="text-2xl font-black text-slate-800 font-outfit uppercase tracking-tight">Cronograma de <span class="text-emerald-500">Rescate</span></h3>
                </div>

                <div class="bg-white rounded-[3.5rem] shadow-premium border border-slate-50 overflow-hidden group/card shadow-xl">
                    @forelse($sessions as $session)
                        <div class="p-10 border-b border-slate-50 last:border-0 hover:bg-slate-50/40 transition-all group/item">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
                                <div class="flex items-center gap-6">
                                    <div class="w-16 h-16 rounded-[1.5rem] bg-white border border-slate-100 flex flex-col items-center justify-center text-slate-400 group-hover/item:rotate-6 group-hover/item:scale-110 transition-all shadow-sm">
                                        <span class="text-[9px] font-black uppercase leading-none mb-0.5">{{ $session->date->translatedFormat('M') }}</span>
                                        <span class="text-2xl font-black text-slate-800 font-outfit leading-none">{{ $session->date->format('d') }}</span>
                                    </div>
                                    <div class="space-y-2">
                                        <h4 class="text-lg font-black text-slate-800 uppercase tracking-tighter leading-none">Compensación Horaria</h4>
                                        <div class="flex items-center gap-4">
                                            <div class="flex items-center gap-2 px-3 py-1 bg-slate-100 rounded-lg">
                                                <i class="fas fa-clock text-[10px] text-slate-400"></i>
                                                <span class="text-[11px] font-black text-slate-600 font-outfit">
                                                    {{ Carbon\Carbon::parse($session->scheduled_start_time)->format('H:i') }} — 
                                                    {{ Carbon\Carbon::parse($session->scheduled_end_time)->format('H:i') }}
                                                </span>
                                            </div>
                                            @if($session->date->isToday())
                                                <span class="text-[9px] font-black text-emerald-500 uppercase tracking-[0.2em] italic">Disponible Hoy</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4">
                                    @if($session->status === 'scheduled')
                                        @if($session->date->isToday())
                                            <form action="{{ route('apprentice.recovery.start', $session) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="px-8 py-4 bg-emerald-500 text-white rounded-[1.5rem] font-black uppercase tracking-[0.2em] text-[10px] hover:bg-slate-900 transition-all shadow-xl shadow-emerald-100 active:scale-95 flex items-center gap-3">
                                                    <i class="fas fa-power-off"></i> Iniciar Sesión
                                                </button>
                                            </form>
                                        @else
                                            <div class="px-6 py-3 bg-slate-50 text-slate-300 rounded-[1.25rem] text-[9px] font-black uppercase tracking-widest border border-slate-100 border-dashed">
                                                {{ $session->date > now() ? 'Pendiente por Fecha' : 'Misión Expirada' }}
                                            </div>
                                        @endif
                                    @elseif($session->status === 'in_progress')
                                        <a href="{{ route('apprentice.recovery.show', $session) }}"
                                            class="px-8 py-4 bg-indigo-50 text-indigo-600 rounded-[1.5rem] font-black uppercase tracking-[0.2em] text-[10px] border border-indigo-100 shadow-sm flex items-center gap-3">
                                            <i class="fas fa-external-link-alt"></i> Panel en Vivo
                                        </a>
                                    @elseif($session->status === 'completed')
                                        <div class="flex flex-col items-end gap-1">
                                            <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-emerald-50 text-sena rounded-full text-[9px] font-black uppercase tracking-widest border border-emerald-100 shadow-sm">
                                                <i class="fas fa-check-double"></i> Consolidada
                                            </span>
                                            <span class="text-[9px] font-black text-slate-300 uppercase tracking-[0.2em] font-outfit">+{{ round($session->duration_minutes / 60, 1) }}h RECUPERADAS</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-32 flex flex-col items-center justify-center text-center space-y-8 bg-white">
                            <div class="w-32 h-32 bg-slate-50 rounded-[4rem] flex items-center justify-center text-slate-200 shadow-inner border border-slate-100 overflow-hidden relative">
                                <div class="absolute inset-0 bg-white/40 blur-xl scale-150 animate-pulse"></div>
                                <i class="fas fa-calendar-check text-5xl relative z-10"></i>
                            </div>
                            <div class="space-y-2">
                                <h3 class="text-2xl font-black text-slate-800 font-outfit uppercase tracking-tighter">Sin jornadas activas</h3>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] max-w-sm mx-auto leading-relaxed">Tus jornadas de recuperación aprobadas se listarán aquí.</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="pagination-premium">
                    {{ $sessions->links() }}
                </div>
            </div>

            {{-- Info Sidebar --}}
            <div class="xl:col-span-4 space-y-8">

                {{-- Pro Tip --}}
                <div class="bg-indigo-50 rounded-[3rem] p-10 border border-indigo-100 group hover:bg-indigo-100 transition-colors">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-indigo-500 shadow-sm border border-indigo-100 group-hover:rotate-12 transition-transform">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <h4 class="text-[10px] font-black text-indigo-900 uppercase tracking-widest">Compensación Directa</h4>
                    </div>
                    <p class="text-[11px] font-bold text-indigo-700 leading-relaxed uppercase tracking-tight opacity-70">
                        Cada minuto en esta sesión reduce automáticamente tu deuda histórica en el módulo de incumplimientos.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection