@extends('layouts.master')

@section('title', 'Dashboard - SIEAP')
@section('page-title', 'Dashboard Gerencial')

@section('breadcrumb')
    <span class="text-slate-400">/</span>
    <span class="text-slate-600">Resumen</span>
@endsection

@section('content')
    <!-- Welcome Banner/Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Usuarios -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 transition-all hover:shadow-md group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <span class="text-xs font-bold text-green-500 bg-green-50 px-2 py-1 rounded-full">+{{ rand(2, 5) }}%</span>
            </div>
            <h3 class="text-slate-500 text-sm font-medium mb-1">Total de Usuarios</h3>
            <p class="text-2xl font-bold text-slate-800">{{ $stats['total_users'] }}</p>
            <a href="{{ route('admin.users.index') }}" class="mt-4 flex items-center text-xs font-bold text-blue-600 hover:text-blue-700">
                Ver lista completa
                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke-width="3"/></svg>
            </a>
        </div>

        <!-- Aprendices -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 transition-all hover:shadow-md group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-50 text-[#39A900] rounded-xl flex items-center justify-center group-hover:bg-[#39A900] group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                    </svg>
                </div>
                <span class="text-xs font-bold text-slate-500 bg-slate-50 px-2 py-1 rounded-full">Activos</span>
            </div>
            <h3 class="text-slate-500 text-sm font-medium mb-1">Aprendices Activos</h3>
            <p class="text-2xl font-bold text-slate-800">{{ $stats['active_apprentices'] }}</p>
            <div class="mt-4 w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                <div class="bg-[#39A900] h-full" style="width: 75%"></div>
            </div>
        </div>

        <!-- Entradas Hoy -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 transition-all hover:shadow-md group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center group-hover:bg-amber-600 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                </div>
                <div class="flex -space-x-2">
                    <div class="w-6 h-6 rounded-full border-2 border-white bg-slate-200"></div>
                    <div class="w-6 h-6 rounded-full border-2 border-white bg-slate-300"></div>
                </div>
            </div>
            <h3 class="text-slate-500 text-sm font-medium mb-1">Entradas Hoy</h3>
            <p class="text-2xl font-bold text-slate-800">{{ $todayAttendance['entries'] }}</p>
            <p class="mt-4 text-xs text-slate-400 font-medium italic">Actualizado hace 5 min</p>
        </div>

        <!-- Certificados -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 transition-all hover:shadow-md group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                </div>
                <span class="text-[10px] font-bold text-purple-600 bg-purple-50 px-2 py-0.5 rounded-lg border border-purple-100 uppercase tracking-tighter">Firma Digital</span>
            </div>
            <h3 class="text-slate-500 text-sm font-medium mb-1">Total Certificados</h3>
            <p class="text-2xl font-bold text-slate-800">{{ $stats['total_certificates'] }}</p>
            <a href="{{ route('admin.certificates.index') }}" class="mt-4 block text-center py-2 bg-slate-50 hover:bg-slate-100 rounded-xl text-[11px] font-bold text-slate-600 transition-colors uppercase tracking-widest">Gestionar</a>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Ultimos Registros -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-50 flex items-center justify-between">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <span class="w-1.5 h-6 bg-[#39A900] rounded-full"></span>
                    Últimas Asistencias
                </h3>
                <a href="{{ route('admin.attendance.index') }}" class="text-xs font-bold text-[#39A900] hover:underline">Ver todas</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50/50 text-slate-400 text-[10px] uppercase tracking-[0.15em] font-bold">
                        <tr>
                            <th class="px-6 py-4">Aprendiz</th>
                            <th class="px-6 py-4">Tipo</th>
                            <th class="px-6 py-4 text-center">Hora</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($recentAttendance as $log)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 font-bold text-xs uppercase">
                                        {{ substr($log->apprentice->name, 0, 2) }}
                                    </div>
                                    <span class="text-sm font-medium text-slate-700 group-hover:text-[#39A900] transition-colors leading-tight">{{ $log->apprentice->full_name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($log->isEntry())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-green-50 text-green-600 border border-green-100 uppercase tracking-tighter">Entrada</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-100 uppercase tracking-tighter">Salida</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-xs font-mono font-bold text-slate-500">{{ $log->occurred_at->format('H:i') }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-slate-400 text-sm">No hay actividad reciente</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Certificados Recientes -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-50 flex items-center justify-between">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <span class="w-1.5 h-6 bg-purple-500 rounded-full"></span>
                    Certificados Emitidos
                </h3>
                <a href="{{ route('admin.certificates.index') }}" class="text-xs font-bold text-purple-600 hover:underline">Gestionar</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50/50 text-slate-400 text-[10px] uppercase tracking-[0.15em] font-bold">
                        <tr>
                            <th class="px-6 py-4">Aprendiz</th>
                            <th class="px-6 py-4 text-center">Estado</th>
                            <th class="px-6 py-4 text-right">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($recentCertificates as $certificate)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-slate-700 leading-tight block truncate max-w-[150px]">{{ $certificate->apprentice->full_name }}</span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tight">{{ $certificate->hours_completed }} horas</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @switch($certificate->status)
                                    @case('generado')
                                        <span class="w-3 h-3 rounded-full bg-blue-500 inline-block shadow-[0_0_8px_rgba(59,130,246,0.5)]"></span>
                                        @break
                                    @case('enviado')
                                        <span class="w-3 h-3 rounded-full bg-amber-500 inline-block shadow-[0_0_8px_rgba(245,158,11,0.5)]"></span>
                                        @break
                                    @case('descargado')
                                        <span class="w-3 h-3 rounded-full bg-green-500 inline-block shadow-[0_0_8px_rgba(34,197,94,0.5)]"></span>
                                        @break
                                    @default
                                        <span class="w-3 h-3 rounded-full bg-slate-300 inline-block"></span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-[11px] font-bold text-slate-500">{{ $certificate->created_at->format('d/m/Y') }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-slate-400 text-sm">No hay certificados</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
