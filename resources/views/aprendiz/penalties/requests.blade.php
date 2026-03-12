@extends('layouts.masteraprendiz')

@section('title', 'Mis Solicitudes de Recuperación')
@section('page-title', 'Historial de Peticiones')

@section('breadcrumb')
    <span class="text-slate-400">/</span>
    <span class="text-slate-600 font-bold uppercase tracking-widest text-[10px]">Solicitudes</span>
@endsection

@section('content')
    <div class="space-y-8 animate-fade-in pb-12">
        
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="flex items-center gap-6">
                <a href="{{ route('apprentice.penalties.index') }}" 
                   class="group w-12 h-12 rounded-2xl bg-white border border-slate-100 text-slate-400 flex items-center justify-center hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all shadow-premium active:scale-90">
                    <i class="fas fa-chevron-left group-hover:-translate-x-1 transition-transform"></i>
                </a>
                <div>
                    <h1 class="text-4xl font-black text-slate-800 tracking-tight font-outfit uppercase">
                        MIS <span class="text-indigo-600 font-black">SOLICITUDES</span>
                    </h1>
                    <p class="text-slate-500 font-bold text-[11px] uppercase tracking-widest opacity-60">Seguimiento y estado de tus peticiones de recuperación</p>
                </div>
            </div>
        </div>

        {{-- Requests Card --}}
        <div class="bg-white rounded-[3rem] shadow-premium border border-slate-50 overflow-hidden group">
            @if($requests->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-50">
                                <th class="px-10 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Fecha Solicitada</th>
                                <th class="px-8 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Tiempo</th>
                                <th class="px-8 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Origen de Deuda</th>
                                <th class="px-8 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Estado</th>
                                <th class="px-10 py-6 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Respuesta</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($requests as $request)
                                <tr class="hover:bg-slate-50/40 transition-all group/item">
                                    <td class="px-10 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-slate-50 text-indigo-500 flex items-center justify-center text-sm font-black border border-slate-100 group-hover/item:scale-110 transition-all">
                                                {{ $request->requested_date->format('d') }}
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-sm font-black text-slate-700 tracking-tight font-outfit uppercase">{{ $request->requested_date->translatedFormat('F, Y') }}</span>
                                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-none">{{ $request->requested_date->translatedFormat('l') }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-white border border-slate-100 rounded-lg shadow-sm">
                                            <i class="fas fa-clock text-indigo-400 text-[10px]"></i>
                                            <span class="text-xs font-black text-slate-700 font-outfit">@hm($request->hours_requested)</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col">
                                            <span class="text-[10px] font-black text-slate-800 uppercase tracking-tighter">Turno: {{ $request->penalty->date->format('d/m/Y') }}</span>
                                            <span class="text-[9px] font-black text-rose-400 uppercase tracking-widest leading-tight">Deuda Original: @hm($request->penalty->penalty_hours)</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        @if($request->status === 'pending')
                                            <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-amber-50 text-amber-500 rounded-full text-[9px] font-black uppercase tracking-widest border border-amber-100 shadow-sm">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Pendiente
                                            </span>
                                        @elseif($request->status === 'approved')
                                            <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-emerald-50 text-sena rounded-full text-[9px] font-black uppercase tracking-widest border border-emerald-100 shadow-sm">
                                                <i class="fas fa-check-circle"></i> Aprobada
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-rose-50 text-rose-500 rounded-full text-[9px] font-black uppercase tracking-widest border border-rose-100 shadow-sm">
                                                <i class="fas fa-times-circle"></i> Rechazada
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-10 py-6">
                                        @if($request->admin_notes)
                                            <div class="relative pl-4 border-l-2 border-slate-100 py-1 group-hover/item:border-indigo-200 transition-colors">
                                                <p class="text-[11px] font-bold text-slate-500 italic leading-relaxed max-w-xs line-clamp-2" title="{{ $request->admin_notes }}">
                                                    "{{ $request->admin_notes }}"
                                                </p>
                                                <span class="text-[8px] font-black text-slate-300 uppercase tracking-widest mt-1 block">— {{ $request->admin->name ?? 'Sistema' }}</span>
                                            </div>
                                        @else
                                            <span class="text-[9px] font-black text-slate-200 uppercase tracking-widest">En espera de respuesta</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-10 py-8 bg-slate-50/50 border-t border-slate-50">
                    <div class="pagination-premium">
                        {{ $requests->links() }}
                    </div>
                </div>
            @else
                <div class="py-32 flex flex-col items-center justify-center text-center space-y-8 bg-white">
                    <div class="w-24 h-24 bg-slate-50 rounded-[2.5rem] flex items-center justify-center text-slate-200 shadow-inner border border-slate-100 relative">
                        <i class="fas fa-paper-plane text-3xl"></i>
                    </div>
                    <div class="space-y-3">
                        <h3 class="text-2xl font-black text-slate-800 font-outfit uppercase tracking-tighter">Sin solicitudes activas</h3>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] max-w-sm mx-auto leading-relaxed">Tus peticiones de recuperación para fines de semana se listarán aquí.</p>
                    </div>
                    <a href="{{ route('apprentice.penalties.index') }}" 
                       class="px-8 py-3 bg-slate-100 text-slate-500 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 hover:text-white transition-all">
                        Ir a Deuda de Horas
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
