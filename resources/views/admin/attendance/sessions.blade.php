@extends('layouts.master')

@section('title', 'Sesiones de Asistencia - SIAP Admin')
@section('page-title', 'Sesiones de Asistencia')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.attendance.index') }}">Asistencia</a></li>
    <li class="breadcrumb-item active">Sesiones</li>
@endsection

@section('content')
    <!-- Header Section -->
    <div class="mb-10">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-4xl font-extrabold text-slate-900 mb-2 font-outfit tracking-tight">Sesiones Registradas</h1>
                <p class="text-slate-500 font-medium font-outfit">Control y seguimiento a la duración de las jornadas de formación.</p>
            </div>
            <div class="flex gap-4">
                <button onclick="exportSessions()"
                    class="btn-primary-unified flex items-center gap-2 px-6 py-3 shadow-sena">
                    <i class="fas fa-file-export"></i>
                    Exportar Reporte
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <!-- Total Sessions -->
        <div class="group relative bg-white rounded-[2rem] p-8 shadow-premium border border-slate-100 hover:border-blue-200 transition-all duration-500 overflow-hidden hover:-translate-y-2">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-700 opacity-50"></div>
            <div class="relative z-10 flex flex-col h-full">
                <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 mb-6 group-hover:rotate-12 transition-all shadow-sm">
                    <i class="fas fa-history text-2xl"></i>
                </div>
                <div class="flex items-baseline gap-2 mb-1">
                    <span class="text-4xl font-extrabold text-slate-800 font-outfit tracking-tighter">{{ $totalSessions ?? 0 }}</span>
                </div>
                <p class="text-slate-500 text-xs font-bold uppercase tracking-widest">Total Sesiones</p>
            </div>
        </div>

        <!-- Completed Sessions -->
        <div class="group relative bg-white rounded-[2rem] p-8 shadow-premium border border-slate-100 hover:border-emerald-200 transition-all duration-500 overflow-hidden hover:-translate-y-2 text-center md:text-left">
            <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-700 opacity-50"></div>
            <div class="relative z-10">
                <div class="mx-auto md:mx-0 w-14 h-14 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600 mb-6 group-hover:rotate-12 transition-all shadow-sm">
                    <i class="fas fa-check-double text-2xl"></i>
                </div>
                <div class="flex items-baseline justify-center md:justify-start gap-2 mb-1">
                    <span class="text-4xl font-extrabold text-slate-800 font-outfit tracking-tighter">{{ $completedSessions ?? 0 }}</span>
                </div>
                <p class="text-slate-500 text-xs font-bold uppercase tracking-widest">Completadas</p>
            </div>
        </div>

        <!-- Active Sessions -->
        <div class="group relative bg-white rounded-[2rem] p-8 shadow-premium border border-slate-100 hover:border-amber-200 transition-all duration-500 overflow-hidden hover:-translate-y-2">
            <div class="absolute top-0 right-0 w-32 h-32 bg-amber-50 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-700 opacity-50"></div>
            <div class="relative z-10">
                <div class="w-14 h-14 bg-amber-100 rounded-2xl flex items-center justify-center text-amber-600 mb-6 group-hover:rotate-12 transition-all shadow-sm">
                    <i class="fas fa-play-circle text-2xl animate-pulse"></i>
                </div>
                <div class="flex items-baseline gap-2 mb-1">
                    <span class="text-4xl font-extrabold text-slate-800 font-outfit tracking-tighter">{{ $activeSessions ?? 0 }}</span>
                </div>
                <p class="text-slate-500 text-xs font-bold uppercase tracking-widest">Activas Ahora</p>
            </div>
        </div>

        <!-- Total Hours -->
        <div class="group relative bg-white rounded-[2rem] p-8 shadow-premium border border-slate-100 hover:border-indigo-200 transition-all duration-500 overflow-hidden hover:-translate-y-2">
            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-700 opacity-50"></div>
            <div class="relative z-10">
                <div class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600 mb-6 group-hover:rotate-12 transition-all shadow-sm">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
                <div class="flex items-baseline gap-1 mb-1">
                    <span class="text-4xl font-extrabold text-slate-800 font-outfit tracking-tighter">@hmMinutes($totalMinutes ?? 0)</span>
                </div>
                <p class="text-slate-500 text-xs font-bold uppercase tracking-widest">Total Horas</p>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white/80 backdrop-blur-xl rounded-[2.5rem] shadow-premium border border-white/20 p-8 mb-10 group hover:shadow-2xl transition-all duration-500">
        <form action="{{ route('admin.attendance.sessions') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Apprentice Filter -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] ml-1">Aprendiz</label>
                    <div class="relative group/input">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within/input:text-sena text-slate-400">
                            <i class="fas fa-user-graduate text-sm"></i>
                        </div>
                        <select name="apprentice_id" 
                            class="block w-full pl-11 pr-4 py-3.5 bg-slate-50/50 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-sena/10 focus:border-sena transition-all appearance-none cursor-pointer">
                            <option value="">Todos los aprendices</option>
                            @foreach($apprentices as $apprentice)
                                <option value="{{ $apprentice->id }}" {{ request('apprentice_id') == $apprentice->id ? 'selected' : '' }}>
                                    {{ $apprentice->full_name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] ml-1">Estado</label>
                    <div class="relative group/input">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors group-focus-within/input:text-sena text-slate-400">
                            <i class="fas fa-toggle-on text-sm"></i>
                        </div>
                        <select name="status" 
                            class="block w-full pl-11 pr-4 py-3.5 bg-slate-50/50 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-sena/10 focus:border-sena transition-all appearance-none cursor-pointer">
                            <option value="">Cualquier estado</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activa</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminada</option>
                        </select>
                    </div>
                </div>

                <!-- Date Range -->
                <div class="lg:col-span-2 grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] ml-1">Desde</label>
                        <div class="relative group/input">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within/input:text-sena">
                                <i class="fas fa-calendar-alt text-sm"></i>
                            </div>
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                class="block w-full pl-11 pr-4 py-3.5 bg-slate-50/50 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-sena/10 focus:border-sena transition-all">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] ml-1">Hasta</label>
                        <div class="relative group/input">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within/input:text-sena">
                                <i class="fas fa-calendar-check text-sm"></i>
                            </div>
                            <input type="date" name="date_to" value="{{ request('date_to') }}"
                                class="block w-full pl-11 pr-4 py-3.5 bg-slate-50/50 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-sena/10 focus:border-sena transition-all">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-slate-50">
                <a href="{{ route('admin.attendance.sessions') }}" 
                    class="px-6 py-3 text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors uppercase tracking-widest">
                    Limpiar Filtros
                </a>
                <button type="submit" 
                    class="btn-primary-unified flex items-center gap-2 px-8 py-3 shadow-sena shadow-lg shadow-sena/20">
                    <i class="fas fa-search"></i>
                    Filtrar Sesiones
                </button>
            </div>
        </form>
    </div>

    <!-- Filters and Table Card -->
    <div class="bg-white rounded-[2.5rem] shadow-premium border border-slate-100 overflow-hidden group mb-12">
        <div class="p-8 border-b border-slate-50 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-sena/10 rounded-2xl flex items-center justify-center text-sena shadow-inner">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-800 font-outfit">Listado de Sesiones</h2>
                    <p class="text-sm text-slate-500 font-medium font-outfit">Control de asistencia histórico y tiempo real</p>
                </div>
            </div>
        </div>
        
        <div class="p-0 overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">ID</th>
                        <th class="px-8 py-5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Aprendiz</th>
                        <th class="px-8 py-5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Inicio</th>
                        <th class="px-8 py-5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Fin</th>
                        <th class="px-8 py-5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Duración</th>
                        <th class="px-8 py-5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Estado</th>
                        <th class="px-8 py-5 text-right text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($sessions ?? [] as $session)
                        <tr class="hover:bg-slate-50/80 transition-all group/item">
                            <!-- ID -->
                            <td class="px-8 py-6">
                                <span class="text-sm font-bold text-slate-400 group-hover/item:text-sena transition-colors">#{{ $session->id }}</span>
                            </td>

                            <!-- Apprentice -->
                            <td class="px-8 py-6">
                                @if(isset($session->apprentice))
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl sena-gradient flex items-center justify-center text-white font-bold shadow-sena shadow-md group-hover/item:scale-110 group-hover/item:rotate-3 transition-transform overflow-hidden">
                                            @if($session->apprentice?->profile_photo_path)
                                                <img src="{{ Storage::url($session->apprentice->profile_photo_path) }}" class="w-full h-full object-cover">
                                            @else
                                                {{ strtoupper(substr($session->apprentice->full_name ?? 'U', 0, 1)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-800 mb-0.5">{{ $session->apprentice->full_name ?? 'Aprendiz' }}</p>
                                            <p class="text-[11px] text-slate-400 font-medium">{{ $session->apprentice->email ?? '' }}</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center gap-4 opacity-50">
                                        <div class="w-12 h-12 rounded-2xl bg-slate-200 flex items-center justify-center text-slate-400 font-bold">?</div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-500 mb-0.5">Usuario No Encontrado</p>
                                            <p class="text-[11px] text-slate-400 font-medium">N/A</p>
                                        </div>
                                    </div>
                                @endif
                            </td>

                            <!-- Start -->
                            <td class="px-8 py-6">
                                <p class="text-sm font-bold text-slate-800 mb-0.5">{{ $session->start_at?->format('d/m/Y') ?? '--/--/----' }}</p>
                                <p class="text-xs text-sena font-bold italic">{{ $session->start_at?->format('H:i') ?? '--:--' }}</p>
                            </td>

                            <!-- End -->
                            <td class="px-8 py-6">
                                @if($session->isCompleted() && $session->end_at)
                                    <p class="text-sm font-bold text-slate-800 mb-0.5">{{ $session->end_at->format('d/m/Y') }}</p>
                                    <p class="text-xs text-rose-500 font-bold italic">{{ $session->end_at->format('H:i') }}</p>
                                @else
                                    <div class="flex items-center gap-2">
                                        <span class="relative flex h-2 w-2">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                        </span>
                                        <span class="text-xs text-amber-600 font-bold uppercase tracking-wider">En curso</span>
                                    </div>
                                @endif
                            </td>

                            <!-- Duration -->
                            <td class="px-8 py-6">
                                @if($session->isCompleted())
                                    <span class="inline-flex items-center px-3 py-1 bg-slate-100 text-slate-700 text-[11px] font-bold rounded-lg border border-slate-200">
                                        <i class="far fa-clock mr-1.5 opacity-60"></i>
                                        {{ $session->duration_in_hours }} hrs
                                    </span>
                                @else
                                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest animate-pulse">Calculando...</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="px-8 py-6">
                                @if($session->isCompleted())
                                    <span class="inline-flex items-center px-4 py-1.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-100">
                                        <i class="fas fa-check-circle mr-1.5"></i>
                                        Terminada
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-4 py-1.5 bg-amber-50 text-amber-700 text-[10px] font-bold uppercase tracking-widest rounded-full border border-amber-100">
                                        <i class="fas fa-hourglass-half mr-1.5 animate-spin-slow"></i>
                                        Activa
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <button onclick="viewSessionDetails({{ $session->id }})"
                                        class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-sena hover:text-white transition-all shadow-sm active:scale-95">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    @if(!$session->isCompleted())
                                        <button onclick="closeSession({{ $session->id }})"
                                            class="w-10 h-10 rounded-xl bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white transition-all shadow-sm active:scale-95">
                                            <i class="fas fa-power-off"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-8 py-20 text-center">
                                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200">
                                    <i class="fas fa-clock text-4xl"></i>
                                </div>
                                <h2 class="text-xl font-bold text-slate-800 mb-2 font-outfit">Sin sesiones</h2>
                                <p class="text-slate-500 font-medium">No se han encontrado registros de sesiones en el sistema.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <!-- Pagination -->
        @if(isset($sessions) && $sessions->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <!-- Results Info -->
                    <div class="text-sm text-gray-600">
                        Mostrando
                        <span class="font-medium text-gray-900">{{ $sessions->firstItem() }}</span>
                        a
                        <span class="font-medium text-gray-900">{{ $sessions->lastItem() }}</span>
                        de
                        <span class="font-medium text-gray-900">{{ $sessions->total() }}</span>
                        resultados
                    </div>

                    <!-- Pagination Links -->
                    <nav class="flex items-center gap-2">
                        {{-- Previous Page Link --}}
                        @if ($sessions->onFirstPage())
                            <span
                                class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-white border border-gray-200 text-gray-400 cursor-not-allowed">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                                    </path>
                                </svg>
                            </span>
                        @else
                            <a href="{{ $sessions->appends(request()->query())->previousPageUrl() }}"
                                class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-colors duration-150">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                                    </path>
                                </svg>
                            </a>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($sessions->getUrlRange(1, $sessions->lastPage()) as $page => $url)
                            @if ($page == $sessions->currentPage())
                                <span
                                    class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-blue-600 text-white font-medium shadow-sm">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $sessions->appends(request()->query())->url($page) }}"
                                    class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-colors duration-150">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($sessions->hasMorePages())
                            <a href="{{ $sessions->appends(request()->query())->nextPageUrl() }}"
                                class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-colors duration-150">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        @else
                            <span
                                class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-white border border-gray-200 text-gray-400 cursor-not-allowed">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        @endif
                    </nav>
                </div>
            </div>
        @endif
    </div>

@endsection

@push('scripts')
    <script>
        function viewSessionDetails(sessionId) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Detalles de la Sesión',
                    html: `
                            <div class="text-left">
                                <p class="mb-2"><strong>ID de Sesión:</strong> #${sessionId}</p>
                                <p class="text-gray-600">Información detallada sobre esta sesión de asistencia, incluyendo horas de entrada y salida.</p>
                            </div>
                        `,
                    icon: 'info',
                    confirmButtonColor: '#3B82F6',
                    confirmButtonText: 'Cerrar'
                });
            }
        }

        function closeSession(sessionId) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '¿Cerrar Sesión?',
                    text: "¿Estás seguro de que deseas cerrar esta sesión? Esta acción no se puede deshacer.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#F59E0B',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Sí, cerrarla',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Implement session closing logic here
                        // For example: make an AJAX call to close the session
                        Swal.fire({
                            title: '¡Cerrada!',
                            text: 'La sesión se ha cerrado correctamente.',
                            icon: 'success',
                            confirmButtonColor: '#10B981',
                            timer: 2000
                        });
                    }
                });
            }
        }

        function exportSessions() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Exportando...',
                    text: 'Preparando su archivo CSV',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Simulate export - replace with actual export endpoint
                setTimeout(() => {
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('export', 'csv');

                    const link = document.createElement('a');
                    link.href = currentUrl.toString();
                    link.download = 'attendance_sessions_' + new Date().toISOString().split('T')[0] + '.csv';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    Swal.fire({
                        title: '¡Éxito!',
                        text: 'Su archivo ha sido exportado',
                        icon: 'success',
                        confirmButtonColor: '#10B981',
                        timer: 2000
                    });
                }, 1000);
            }
        }
    </script>
@endpush