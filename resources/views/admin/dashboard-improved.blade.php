@extends('layouts.master')

@section('title', 'Dashboard - SIAP Admin')
@section('page-title', 'Dashboard')

@section('content')
<div class="min-h-screen fade-in">
    <!-- Welcome Section -->
    <div class="mb-10">
        <div class="relative overflow-hidden rounded-[2rem] bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 p-10 shadow-2xl border border-white/5 group">
            <!-- Animated Background Glow -->
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-sena/20 rounded-full blur-[100px] animate-pulse"></div>
            <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-blue-500/10 rounded-full blur-[80px] animate-pulse delay-700"></div>
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-white/10 opacity-20">
                <div class="absolute -top-4 -right-4 w-24 h-24 bg-white/20 rounded-full blur-xl animate-pulse"></div>
                <div class="absolute -bottom-4 -left-4 w-32 h-32 bg-white/10 rounded-full blur-2xl animate-pulse delay-1000"></div>
            </div>

            <div class="flex flex-col md:flex-row items-center relative z-10 gap-8">
                <div class="w-20 h-20 bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/10 shadow-2xl group-hover:scale-110 group-hover:rotate-3 transition-all duration-500">
                    <i class="fas fa-rocket text-4xl text-sena leading-none"></i>
                </div>
                <div class="text-center md:text-left">
                    <h1 class="text-4xl font-extrabold text-white mb-2 font-outfit tracking-tight">
                        Sistema <span class="text-sena">SIEAP</span>
                    </h1>
                    <p class="text-slate-400 text-lg max-w-2xl font-medium">Panel de Gestión de Asistencia y Certificación. Administra de manera eficiente el flujo de aprendices y el cumplimiento de horas.</p>
                </div>
                <div class="md:ml-auto hidden xl:block">
                    <div class="bg-white/5 border border-white/10 rounded-2xl px-6 py-4 backdrop-blur-sm">
                        <p class="text-[10px] uppercase font-bold text-slate-500 mb-1 tracking-widest text-center">Estado del Sistema</p>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-sena rounded-full animate-pulse"></span>
                            <span class="text-white font-bold">Operativo</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards Row -->
    <div class="mb-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Users Card -->
            <div class="group relative bg-white rounded-[2rem] p-8 shadow-premium border border-slate-100 hover:border-blue-200 transition-all duration-500 overflow-hidden hover:-translate-y-2">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-700 opacity-50"></div>
                <div class="relative z-10">
                    <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 mb-6 group-hover:rotate-12 transition-all shadow-sm group-hover:shadow-blue-200">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <div class="flex items-baseline gap-2 mb-1">
                        <span class="text-4xl font-extrabold text-slate-800 font-outfit leading-none">{{ $stats['total_users'] }}</span>
                    </div>
                    <p class="text-slate-500 text-sm font-bold uppercase tracking-wider mb-6">Total Usuarios</p>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 text-xs font-bold text-blue-600 group-hover:gap-3 transition-all">
                        GESTIONAR <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Active Apprentices Card -->
            <div class="group relative bg-white rounded-[2rem] p-8 shadow-premium border border-slate-100 hover:border-sena/30 transition-all duration-500 overflow-hidden hover:-translate-y-2">
                <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-700 opacity-50"></div>
                <div class="relative z-10">
                    <div class="w-14 h-14 bg-emerald-100 rounded-2xl flex items-center justify-center text-sena mb-6 group-hover:rotate-12 transition-all shadow-sm group-hover:shadow-sena/20">
                        <i class="fas fa-user-graduate text-2xl"></i>
                    </div>
                    <div class="flex items-baseline gap-2 mb-1">
                        <span class="text-4xl font-extrabold text-slate-800 font-outfit leading-none">{{ $stats['active_apprentices'] }}</span>
                    </div>
                    <p class="text-slate-500 text-sm font-bold uppercase tracking-wider mb-6">Aprendices Activos</p>
                    <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-sena h-full rounded-full transition-all duration-1000" style="width: {{ round(($stats['active_apprentices'] / max($stats['total_users'], 1)) * 100) }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Today's Entries Card -->
            <div class="group relative bg-white rounded-[2rem] p-8 shadow-premium border border-slate-100 hover:border-amber-200 transition-all duration-500 overflow-hidden hover:-translate-y-2">
                <div class="absolute top-0 right-0 w-32 h-32 bg-amber-50 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-700 opacity-50"></div>
                <div class="relative z-10">
                    <div class="w-14 h-14 bg-amber-100 rounded-2xl flex items-center justify-center text-amber-600 mb-6 group-hover:rotate-12 transition-all shadow-sm group-hover:shadow-amber-200">
                        <i class="fas fa-sign-in-alt text-2xl"></i>
                    </div>
                    <div class="flex items-baseline gap-2 mb-1">
                        <span class="text-4xl font-extrabold text-slate-800 font-outfit leading-none">{{ $todayAttendance['entries'] }}</span>
                    </div>
                    <p class="text-slate-500 text-sm font-bold uppercase tracking-wider mb-6">Entradas Hoy</p>
                    <p class="text-[10px] text-slate-400 font-bold bg-slate-50 px-2 py-1 rounded-lg inline-block">{{ now()->format('d/m/Y') }}</p>
                </div>
            </div>

            <!-- Certificates Card -->
            <div class="group relative bg-white rounded-[2rem] p-8 shadow-premium border border-slate-100 hover:border-rose-200 transition-all duration-500 overflow-hidden hover:-translate-y-2">
                <div class="absolute top-0 right-0 w-32 h-32 bg-rose-50 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-700 opacity-50"></div>
                <div class="relative z-10">
                    <div class="w-14 h-14 bg-rose-100 rounded-2xl flex items-center justify-center text-rose-600 mb-6 group-hover:rotate-12 transition-all shadow-sm group-hover:shadow-rose-200">
                        <i class="fas fa-certificate text-2xl"></i>
                    </div>
                    <div class="flex items-baseline gap-2 mb-1">
                        <span class="text-4xl font-extrabold text-slate-800 font-outfit leading-none">{{ $stats['total_certificates'] }}</span>
                    </div>
                    <p class="text-slate-500 text-sm font-bold uppercase tracking-wider mb-6">Certificados</p>
                    <span class="text-[10px] font-bold text-rose-500 bg-rose-50 px-2 py-1 rounded-lg">{{ $stats['pending_certificates'] ?? 0 }} pendientes</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="mb-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <!-- Recent Attendance -->
            <div class="bg-white rounded-[2.5rem] shadow-premium border border-slate-100 overflow-hidden group">
                <div class="bg-slate-50 px-8 py-6 border-b border-slate-100 space-y-1">
                    <div class="flex items-center justify-between">
                        <h3 class="text-slate-800 font-extrabold text-xl font-outfit tracking-tight">Registro Reciente</h3>
                        <a href="{{ route('admin.attendance.index') }}" class="text-xs font-bold text-slate-500 hover:text-sena transition-colors tracking-widest uppercase">Ver Todo</a>
                    </div>
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Aprendiz</th>
                                <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Evento</th>
                                <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Hora</th>
                                <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Fuente</th>
                            </tr>
                        </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($recentAttendance as $log)
                                <tr class="group/item hover:bg-slate-50/80 transition-all duration-200">
                                    <td class="px-8 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 sena-gradient rounded-xl flex items-center justify-center text-white font-bold text-xs shadow-sena shadow-md transition-transform group-hover/item:scale-110 group-hover/item:rotate-3">
                                                {{ strtoupper(substr($log->apprentice->full_name, 0, 1)) }}
                                            </div>
                                            <div class="text-sm font-bold text-slate-800">{{ $log->apprentice->full_name }}</div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-4">
                                        @if($log->isEntry())
                                            <span class="inline-flex items-center px-3 py-1 bg-emerald-50 text-emerald-600 text-[9px] font-black uppercase tracking-widest rounded-full border border-emerald-100">
                                                <i class="fas fa-sign-in-alt mr-1"></i> Entrada
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 bg-rose-50 text-rose-600 text-[9px] font-black uppercase tracking-widest rounded-full border border-rose-100">
                                                <i class="fas fa-sign-out-alt mr-1"></i> Salida
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-4">
                                        <div class="font-bold text-slate-800 text-sm mb-0.5">{{ $log->occurred_at->format('H:i') }}</div>
                                        <div class="text-[10px] text-slate-400 font-bold tracking-widest">{{ $log->occurred_at->format('d/m') }}</div>
                                    </td>
                                    <td class="px-8 py-4">
                                        <span class="text-[9px] font-black text-slate-400 border border-slate-100 px-2 py-1 rounded-lg bg-slate-50 uppercase tracking-tighter">
                                            {{ $log->source }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-16 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 text-slate-200">
                                                <i class="fas fa-history text-3xl"></i>
                                            </div>
                                            <p class="text-slate-500 font-bold font-outfit uppercase tracking-widest text-xs">Sin registros recientes</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Certificates -->
            <div class="bg-white rounded-[2.5rem] shadow-premium border border-slate-100 overflow-hidden">
                <div class="bg-slate-50 px-8 py-6 border-b border-slate-100 space-y-1">
                    <div class="flex items-center justify-between">
                        <h3 class="text-slate-800 font-extrabold text-xl font-outfit tracking-tight">Certificados</h3>
                        <a href="{{ route('admin.certificates.index') }}" class="text-xs font-bold text-slate-500 hover:text-sena transition-colors tracking-widest uppercase">Gestionar</a>
                    </div>
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Aprendiz</th>
                                <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Horas</th>
                                <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Estado</th>
                                <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Fecha</th>
                            </tr>
                        </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($recentCertificates as $certificate)
                                <tr class="group/item hover:bg-slate-50/80 transition-all duration-200">
                                    <td class="px-8 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 sena-gradient rounded-xl flex items-center justify-center text-white font-bold text-xs shadow-sena shadow-md transition-transform group-hover/item:scale-110 group-hover/item:rotate-3">
                                                {{ strtoupper(substr($certificate->apprentice->full_name, 0, 1)) }}
                                            </div>
                                            <div class="text-sm font-bold text-slate-800">{{ $certificate->apprentice->full_name }}</div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-4">
                                        <div class="inline-flex items-center px-2 py-0.5 bg-slate-50 text-slate-700 rounded-md border border-slate-100 font-extrabold text-[10px]">
                                            {{ $certificate->hours_completed }}H
                                        </div>
                                    </td>
                                    <td class="px-8 py-4">
                                        @switch($certificate->status)
                                            @case('generado')
                                                <span class="inline-flex items-center px-3 py-1 bg-blue-50 text-blue-600 text-[9px] font-black uppercase tracking-widest rounded-full border border-blue-100">
                                                    <i class="fas fa-file-alt mr-1"></i> Generado
                                                </span>
                                                @break
                                            @case('enviado')
                                                <span class="inline-flex items-center px-3 py-1 bg-amber-50 text-amber-600 text-[9px] font-black uppercase tracking-widest rounded-full border border-amber-100">
                                                    <i class="fas fa-paper-plane mr-1"></i> Enviado
                                                </span>
                                                @break
                                            @case('descargado')
                                                <span class="inline-flex items-center px-3 py-1 bg-emerald-50 text-emerald-600 text-[9px] font-black uppercase tracking-widest rounded-full border border-emerald-100">
                                                    <i class="fas fa-download mr-1"></i> Descargado
                                                </span>
                                                @break
                                            @case('anulado')
                                                <span class="inline-flex items-center px-3 py-1 bg-rose-50 text-rose-600 text-[9px] font-black uppercase tracking-widest rounded-full border border-rose-100">
                                                    <i class="fas fa-ban mr-1"></i> Anulado
                                                </span>
                                                @break
                                            @default
                                                <span class="inline-flex items-center px-3 py-1 bg-slate-50 text-slate-500 text-[9px] font-black uppercase tracking-widest rounded-full border border-slate-100">
                                                    Pendiente
                                                </span>
                                        @endswitch
                                    </td>
                                    <td class="px-8 py-4">
                                        <div class="font-bold text-slate-800 text-sm">{{ $certificate->created_at->format('d/m/Y') }}</div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-16 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 text-slate-200">
                                                <i class="fas fa-certificate text-3xl"></i>
                                            </div>
                                            <p class="text-slate-500 font-bold font-outfit uppercase tracking-widest text-xs">Sin certificados recientes</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Summary -->
    <div class="max-w-7xl mx-auto animate-fade-in-up" style="animation-delay: 0.7s;">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-500">
            <div class="bg-gradient-to-r from-violet-500 via-purple-500 to-pink-500 px-8 py-6 relative overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 bg-white/10 opacity-20">
                    <div class="absolute -top-4 -right-4 w-32 h-32 bg-white/20 rounded-full blur-2xl animate-pulse"></div>
                    <div class="absolute -bottom-4 -left-4 w-24 h-24 bg-white/10 rounded-full blur-xl animate-pulse delay-1000"></div>
                </div>

                <div class="relative z-10">
                    <h3 class="text-white font-bold text-2xl flex items-center">
                        <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl mr-4">
                            <i class="fas fa-chart-pie text-2xl"></i>
                        </div>
                        Resumen de Hoy - {{ now()->format('l, d F Y') }}
                    </h3>
                </div>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                    <!-- Entradas Registradas -->
                    <div class="group relative bg-gradient-to-br from-emerald-50 via-green-50 to-teal-50 rounded-2xl p-6 border border-emerald-200 hover:shadow-xl transition-all duration-500 transform hover:-translate-y-1 hover:rotate-1 overflow-hidden">
                        <!-- Background Pattern -->
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-400/5 to-teal-400/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <div class="bg-gradient-to-br from-emerald-400 to-emerald-600 p-4 rounded-xl group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                    <i class="fas fa-sign-in-alt text-white text-2xl"></i>
                                </div>
                                <div class="text-right">
                                    <p class="text-3xl font-bold text-emerald-800 mb-1">{{ $todayAttendance['entries'] }}</p>
                                    <div class="w-16 h-1 bg-emerald-400 rounded-full"></div>
                                </div>
                            </div>

                            <p class="text-sm font-semibold text-emerald-700 mb-4">Entradas Registradas</p>

                            <div class="w-full bg-emerald-200 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-emerald-400 to-emerald-600 h-3 rounded-full transition-all duration-1000 shadow-sm" style="width: {{ $todayAttendance['entries'] > 0 ? '100%' : '0%' }}"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Salidas Registradas -->
                    <div class="group relative bg-gradient-to-br from-rose-50 via-red-50 to-pink-50 rounded-2xl p-6 border border-rose-200 hover:shadow-xl transition-all duration-500 transform hover:-translate-y-1 hover:-rotate-1 overflow-hidden">
                        <!-- Background Pattern -->
                        <div class="absolute inset-0 bg-gradient-to-br from-rose-400/5 to-pink-400/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <div class="bg-gradient-to-br from-rose-400 to-rose-600 p-4 rounded-xl group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                    <i class="fas fa-sign-out-alt text-white text-2xl"></i>
                                </div>
                                <div class="text-right">
                                    <p class="text-3xl font-bold text-rose-800 mb-1">{{ $todayAttendance['exits'] }}</p>
                                    <div class="w-16 h-1 bg-rose-400 rounded-full"></div>
                                </div>
                            </div>

                            <p class="text-sm font-semibold text-rose-700 mb-4">Salidas Registradas</p>

                            <div class="w-full bg-rose-200 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-rose-400 to-rose-600 h-3 rounded-full transition-all duration-1000 shadow-sm" style="width: {{ $todayAttendance['exits'] > 0 ? '100%' : '0%' }}"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Sesiones Activas -->
                    <div class="group relative bg-gradient-to-br from-amber-50 via-yellow-50 to-orange-50 rounded-2xl p-6 border border-amber-200 hover:shadow-xl transition-all duration-500 transform hover:-translate-y-1 hover:rotate-1 overflow-hidden">
                        <!-- Background Pattern -->
                        <div class="absolute inset-0 bg-gradient-to-br from-amber-400/5 to-orange-400/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <div class="bg-gradient-to-br from-amber-400 to-amber-600 p-4 rounded-xl group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                    <i class="fas fa-clock text-white text-2xl"></i>
                                </div>
                                <div class="text-right">
                                    <p class="text-3xl font-bold text-amber-800 mb-1">{{ $todayAttendance['active_sessions'] }}</p>
                                    <div class="w-16 h-1 bg-amber-400 rounded-full"></div>
                                </div>
                            </div>

                            <p class="text-sm font-semibold text-amber-700 mb-4">Sesiones Activas</p>

                            <div class="w-full bg-amber-200 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-amber-400 to-amber-600 h-3 rounded-full transition-all duration-1000 shadow-sm" style="width: {{ $todayAttendance['active_sessions'] > 0 ? '75%' : '0%' }}"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Certificados Pendientes -->
                    <div class="group relative bg-gradient-to-br from-blue-50 via-indigo-50 to-cyan-50 rounded-2xl p-6 border border-blue-200 hover:shadow-xl transition-all duration-500 transform hover:-translate-y-1 hover:-rotate-1 overflow-hidden">
                        <!-- Background Pattern -->
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-400/5 to-cyan-400/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <div class="bg-gradient-to-br from-blue-400 to-blue-600 p-4 rounded-xl group-hover:scale-110 transition-transform duration-300 shadow-lg">
                                    <i class="fas fa-certificate text-white text-2xl"></i>
                                </div>
                                <div class="text-right">
                                    <p class="text-3xl font-bold text-blue-800 mb-1">{{ $stats['pending_certificates'] ?? 0 }}</p>
                                    <div class="w-16 h-1 bg-blue-400 rounded-full"></div>
                                </div>
                            </div>

                            <p class="text-sm font-semibold text-blue-700 mb-4">Certificados Pendientes</p>

                            <div class="w-full bg-blue-200 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-3 rounded-full transition-all duration-1000 shadow-sm" style="width: {{ ($stats['pending_certificates'] ?? 0) > 0 ? '60%' : '0%' }}"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="flex flex-wrap justify-center gap-6">
                    <a href="{{ route('admin.attendance.index') }}" class="group relative bg-gradient-to-r from-blue-500 via-indigo-500 to-cyan-500 hover:from-blue-600 hover:via-indigo-600 hover:to-cyan-600 text-white px-8 py-4 rounded-2xl font-semibold transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-xl hover:shadow-2xl overflow-hidden">
                        <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative z-10 flex items-center">
                            <i class="fas fa-calendar-check mr-3 text-xl"></i>
                            <span>Gestionar Asistencia</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.certificates.create') }}" class="group relative bg-gradient-to-r from-emerald-500 via-green-500 to-teal-500 hover:from-emerald-600 hover:via-green-600 hover:to-teal-600 text-white px-8 py-4 rounded-2xl font-semibold transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-xl hover:shadow-2xl overflow-hidden">
                        <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative z-10 flex items-center">
                            <i class="fas fa-plus mr-3 text-xl"></i>
                            <span>Nuevo Certificado</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.reports.index') }}" class="group relative bg-gradient-to-r from-purple-500 via-violet-500 to-pink-500 hover:from-purple-600 hover:via-violet-600 hover:to-pink-600 text-white px-8 py-4 rounded-2xl font-semibold transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-xl hover:shadow-2xl overflow-hidden">
                        <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative z-10 flex items-center">
                            <i class="fas fa-chart-bar mr-3 text-xl"></i>
                            <span>Ver Reportes</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.users.create') }}" class="group relative bg-gradient-to-r from-orange-500 via-amber-500 to-yellow-500 hover:from-orange-600 hover:via-amber-600 hover:to-yellow-600 text-white px-8 py-4 rounded-2xl font-semibold transition-all duration-300 transform hover:scale-105 hover:-translate-y-1 shadow-xl hover:shadow-2xl overflow-hidden">
                        <div class="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative z-10 flex items-center">
                            <i class="fas fa-user-plus mr-3 text-xl"></i>
                            <span>Registrar Usuario</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
@endsection
