@extends('layouts.masteraprendiz')

@section('title', 'Historial de Registros - SIEAP')
@section('page-title', 'Mi Registro Histórico')

@section('breadcrumb')
    <span class="text-slate-400">/</span>
    <span class="text-slate-600 font-bold uppercase tracking-widest text-[10px]">Bitácora</span>
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
                        Mi Bitácora de <span class="text-sena">Actividad</span>
                    </h1>
                    <p class="text-slate-500 mt-2 font-medium text-lg">Historial detallado de todas tus marcaciones registradas.</p>
                </div>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="bg-white rounded-[3rem] shadow-premium border border-slate-50 overflow-hidden group">
            @if($logs->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-50">
                                <th class="px-10 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Fecha y Hora</th>
                                <th class="px-8 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Movimiento</th>
                                <th class="px-8 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Canal</th>
                                <th class="px-10 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Observaciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($logs as $log)
                                <tr class="hover:bg-slate-50/40 transition-all group/item">
                                    <td class="px-10 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center text-xs border border-slate-100 group-hover/item:bg-white group-hover/item:text-sena group-hover/item:scale-110 transition-all">
                                                <i class="fas fa-calendar-day"></i>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-sm font-black text-slate-700 tracking-tight font-outfit uppercase">{{ $log->occurred_at->translatedFormat('d F, Y') }}</span>
                                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">{{ $log->occurred_at->format('H:i:s A') }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        @if($log->event_type === 'entrada')
                                            <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-green-50 text-sena rounded-full text-[10px] font-black uppercase tracking-widest border border-green-100 shadow-sm">
                                                <i class="fas fa-sign-in-alt"></i> Entrada
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-rose-50 text-rose-500 rounded-full text-[10px] font-black uppercase tracking-widest border border-rose-100 shadow-sm">
                                                <i class="fas fa-sign-out-alt"></i> Salida
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        @php
                                            $sourceIcons = [
                                                'lector' => ['icon' => 'fa-fingerprint', 'color' => 'text-purple-500', 'bg' => 'bg-purple-50'],
                                                'web' => ['icon' => 'fa-globe', 'color' => 'text-blue-500', 'bg' => 'bg-blue-50'],
                                                'admin' => ['icon' => 'fa-user-shield', 'color' => 'text-slate-500', 'bg' => 'bg-slate-50'],
                                            ];
                                            $source = $log->source ?? 'web';
                                            $config = $sourceIcons[$source] ?? $sourceIcons['web'];
                                        @endphp
                                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-white border border-slate-100 rounded-lg shadow-sm text-[9px] font-black uppercase tracking-widest text-slate-500">
                                            <i class="fas {{ $config['icon'] }} {{ $config['color'] }} opacity-70"></i>
                                            {{ $source }}
                                        </div>
                                    </td>
                                    <td class="px-10 py-6">
                                        @if($log->note)
                                            <p class="text-[11px] font-bold text-slate-500 italic max-w-xs truncate" title="{{ $log->note }}">"{{ $log->note }}"</p>
                                        @else
                                            <span class="text-[9px] font-black text-slate-200 uppercase tracking-widest">Sin registro adicional</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-10 py-8 bg-slate-50/50 border-t border-slate-50">
                    <div class="pagination-premium">
                        {{ $logs->links() }}
                    </div>
                </div>
            @else
                <div class="py-24 flex flex-col items-center justify-center text-center space-y-6">
                    <div class="w-32 h-32 bg-slate-50 rounded-[3.5rem] flex items-center justify-center text-slate-200 shadow-inner border border-slate-100">
                        <i class="fas fa-ghost text-4xl"></i>
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-2xl font-black text-slate-800 font-outfit uppercase tracking-tighter">Sin actividad aún</h3>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] max-w-xs mx-auto">Tus marcaciones de asistencia aparecerán aquí una vez inicies tu formación.</p>
                    </div>
                    <a href="{{ route('apprentice.attendance.index') }}" 
                       class="px-10 py-4 sena-gradient text-white rounded-[1.5rem] text-xs font-black uppercase tracking-widest shadow-xl shadow-sena/30 hover:shadow-sena/50 active:scale-95 transition-all">
                        Ir al Control de Acceso
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
