@extends('layouts.master')

@section('title', 'Dashboard Gerencia - SIEAP')
@section('page-title', 'Inicio Gerencia')

@section('breadcrumb')
    <li class="breadcrumb-item active text-slate-500 font-medium">Dashboard</li>
@endsection

@section('content')
<div class="min-h-screen fade-in space-y-8">
    
    <!-- Welcome Section -->
    <div class="relative overflow-hidden rounded-[2rem] premium-gradient p-10 shadow-2xl border border-white/10 group">
        <div class="absolute inset-0 bg-white/5 opacity-20">
            <div class="absolute -top-4 -right-4 w-32 h-32 bg-white/20 rounded-full blur-2xl animate-pulse"></div>
            <div class="absolute -bottom-8 -left-8 w-40 h-40 bg-white/10 rounded-full blur-3xl animate-pulse delay-500"></div>
        </div>
        
        <div class="flex flex-col md:flex-row items-center relative z-10 gap-8">
            <div class="w-20 h-20 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-2xl group-hover:scale-110 transition-transform duration-500">
                <i class="fas fa-briefcase text-4xl text-white"></i>
            </div>
            <div class="text-center md:text-left">
                <h1 class="text-4xl font-extrabold text-white mb-2 font-outfit tracking-tight">
                    Panel de Gerencia <span class="text-white/80">SIEAP</span>
                </h1>
                <p class="text-white/90 text-lg max-w-2xl font-medium">
                    Gestiona el cumplimiento normativo, aprueba planes de recuperación y supervisa la correcta ejecución del programa de horas de los aprendices.
                </p>
            </div>
            <div class="md:ml-auto hidden xl:block">
                <div class="bg-white/10 border border-white/20 rounded-2xl px-6 py-4 backdrop-blur-sm shadow-xl flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-bold text-white/70 mb-1 tracking-widest">Hoy es</p>
                        <p class="text-white font-bold">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Solicitudes Pendientes -->
        <a href="{{ route('admin.recovery-requests.index') }}" class="bg-white rounded-3xl p-6 shadow-premium border border-slate-100 hover:border-amber-300 relative group overflow-hidden transition-all duration-300 hover:-translate-y-1 block">
            <div class="absolute top-0 right-0 w-24 h-24 bg-amber-50 rounded-bl-full -mr-4 -mt-4 transition-transform duration-500 group-hover:scale-150 opacity-50"></div>
            <div class="relative z-10 flex flex-col h-full justify-between">
                <div>
                    <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center mb-4 text-xl shadow-sm group-hover:rotate-12 transition-transform">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="text-4xl font-extrabold text-slate-800 font-outfit mb-1">{{ $pendingRequests }}</h3>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-3">Solicitudes Pendientes</p>
                    <div class="text-[10px] font-bold text-amber-600 flex items-center gap-1 group-hover:gap-2 transition-all">
                        GESTIONAR <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
            </div>
        </a>

        <!-- Deudas Activas -->
        <a href="{{ route('admin.penalties.index') }}" class="bg-white rounded-3xl p-6 shadow-premium border border-slate-100 hover:border-rose-300 relative group overflow-hidden transition-all duration-300 hover:-translate-y-1 block">
            <div class="absolute top-0 right-0 w-24 h-24 bg-rose-50 rounded-bl-full -mr-4 -mt-4 transition-transform duration-500 group-hover:scale-150 opacity-50"></div>
            <div class="relative z-10 flex flex-col h-full justify-between">
                <div>
                    <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center mb-4 text-xl shadow-sm group-hover:rotate-12 transition-transform">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="text-4xl font-extrabold text-slate-800 font-outfit mb-1">{{ $activePenalties }}</h3>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-3">Penalizaciones Activas</p>
                    <div class="text-[10px] font-bold text-rose-600 flex items-center gap-1 group-hover:gap-2 transition-all">
                        SEGUIMIENTO <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
            </div>
        </a>

        <!-- Sesiones en Curso / Programadas -->
        <a href="{{ route('admin.recovery-sessions.index') }}" class="bg-white rounded-3xl p-6 shadow-premium border border-slate-100 hover:border-blue-300 relative group overflow-hidden transition-all duration-300 hover:-translate-y-1 block md:col-span-2 lg:col-span-1">
            <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-4 -mt-4 transition-transform duration-500 group-hover:scale-150 opacity-50"></div>
            <div class="relative z-10 flex flex-col h-full justify-between">
                <div>
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mb-4 text-xl shadow-sm group-hover:rotate-12 transition-transform">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3 class="text-4xl font-extrabold text-slate-800 font-outfit mb-1">{{ $scheduledSessions + $inProgressSessions }}</h3>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-3">Sesiones Programadas</p>
                    <div class="text-[10px] font-bold text-blue-600 flex items-center gap-1 group-hover:gap-2 transition-all">
                        VER AGENDA <i class="fas fa-arrow-right"></i>
                    </div>
                </div>
            </div>
        </a>
        
        <!-- Solicitudes Aprobadas Global -->
        <div class="bg-white rounded-3xl p-6 shadow-premium border border-slate-100 relative group overflow-hidden transition-all duration-300">
            <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-50 rounded-bl-full -mr-4 -mt-4 transition-transform duration-500 group-hover:scale-150 opacity-50"></div>
            <div class="relative z-10 flex flex-col h-full justify-between">
                <div>
                    <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mb-4 text-xl shadow-sm group-hover:rotate-12 transition-transform">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <h3 class="text-4xl font-extrabold text-slate-800 font-outfit mb-1">{{ $approvedRequests }}</h3>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-3">Solicitudes Aprobadas</p>
                    <div class="text-[10px] font-bold text-emerald-600 flex items-center gap-1">
                        HISTÓRICO
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Solicitudes Recientes -->
        <div class="bg-white rounded-[2rem] shadow-premium border border-slate-100 overflow-hidden">
            <div class="bg-slate-50 px-8 py-6 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-slate-800 font-extrabold text-xl font-outfit tracking-tight">Últimas Solicitudes</h3>
                <a href="{{ route('admin.recovery-requests.index') }}" class="text-[10px] font-bold text-sena uppercase tracking-widest hover:text-sena-dark transition-colors bg-sena/10 px-3 py-1.5 rounded-lg">Ver Todas</a>
            </div>
            <div class="p-0 overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-white border-b border-slate-100">
                            <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Colaborador</th>
                            <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Horas</th>
                            <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentRequests as $req)
                        <tr class="group/item hover:bg-slate-50/50 transition-colors">
                            <td class="px-8 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 premium-gradient rounded-xl flex items-center justify-center text-white font-bold text-xs shadow-md">
                                        {{ strtoupper(substr($req->apprentice->full_name, 0, 1)) }}
                                    </div>
                                    <div class="text-sm font-bold text-slate-800">{{ $req->apprentice->full_name }}</div>
                                </div>
                            </td>
                            <td class="px-8 py-4">
                                <span class="text-xs font-extrabold text-slate-700 bg-slate-100 px-2 py-1 rounded-md">{{ $req->hours_requested }}H</span>
                            </td>
                            <td class="px-8 py-4">
                                @if($req->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-1 bg-amber-50 text-amber-600 text-[9px] font-black uppercase tracking-widest rounded-lg border border-amber-200">
                                        Pendiente
                                    </span>
                                @elseif($req->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-1 bg-emerald-50 text-emerald-600 text-[9px] font-black uppercase tracking-widest rounded-lg border border-emerald-200">
                                        Aprobada
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 bg-rose-50 text-rose-600 text-[9px] font-black uppercase tracking-widest rounded-lg border border-rose-200">
                                        Rechazada
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-8 py-12 text-center">
                                <i class="fas fa-inbox text-4xl text-slate-200 mb-3"></i>
                                <p class="text-slate-500 font-bold text-xs uppercase tracking-widest">Sin solicitudes recientes</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sesiones Activas Hoy -->
        <div class="bg-white rounded-[2rem] shadow-premium border border-slate-100 overflow-hidden">
            <div class="bg-slate-50 px-8 py-6 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-slate-800 font-extrabold text-xl font-outfit tracking-tight">Sesiones de Hoy</h3>
                <span class="text-[10px] font-bold text-blue-600 uppercase tracking-widest bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-100">
                    {{ \Carbon\Carbon::now()->format('d/m/Y') }}
                </span>
            </div>
            <div class="p-0 overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-white border-b border-slate-100">
                            <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Colaborador</th>
                            <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Horario</th>
                            <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($activeSessionsToday as $session)
                        <tr class="group/item hover:bg-slate-50/50 transition-colors">
                            <td class="px-8 py-4">
                                <div class="text-sm font-bold text-slate-800 truncate">{{ $session->apprentice->name }}</div>
                            </td>
                            <td class="px-8 py-4">
                                <div class="text-xs font-bold text-slate-600">
                                    {{ \Carbon\Carbon::parse($session->scheduled_start_time)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($session->scheduled_end_time)->format('H:i') }}
                                </div>
                            </td>
                            <td class="px-8 py-4">
                                @if($session->status === 'in_progress')
                                    <span class="inline-flex items-center px-2.5 py-1 bg-blue-50 text-blue-600 text-[9px] font-black uppercase tracking-widest rounded-lg border border-blue-200">
                                        <span class="w-1.5 h-1.5 bg-blue-600 rounded-full mr-1.5 animate-pulse"></span>
                                        En Curso
                                    </span>
                                @elseif($session->status === 'scheduled')
                                    <span class="inline-flex items-center px-2.5 py-1 bg-slate-50 text-slate-600 text-[9px] font-black uppercase tracking-widest rounded-lg border border-slate-200">
                                        Programada
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-8 py-12 text-center">
                                <i class="fas fa-calendar-times text-4xl text-slate-200 mb-3"></i>
                                <p class="text-slate-500 font-bold text-xs uppercase tracking-widest">Sin sesiones programadas para hoy</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
