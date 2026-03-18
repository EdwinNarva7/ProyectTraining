@extends('layouts.master')

@section('title', 'Reporte Detallado de Asistencia - SIAP Admin')
@section('page-title', 'Reporte Detallado de Asistencia')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.attendance.index') }}">Asistencia</a></li>
    <li class="breadcrumb-item active">Reporte Detallado</li>
@endsection

@section('content')
    <!-- Header Section -->
    <div class="mb-10">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-4xl font-bold text-slate-900 mb-2 font-outfit tracking-tight">Reporte <span class="text-sena">Detallado</span></h1>
                <p class="text-slate-500 font-medium font-outfit">Análisis exhaustivo y estadísticas de cumplimiento institucional.</p>
            </div>
            <div class="flex flex-wrap gap-4">
                <button onclick="exportToExcel()"
                    class="btn-primary-unified flex items-center gap-2 px-6 py-3 shadow-sena group">
                    <i class="fas fa-file-excel group-hover:scale-110 transition-transform"></i>
                    Exportar Excel
                </button>
                <button onclick="exportToPdf()"
                    class="inline-flex items-center px-6 py-3 bg-slate-900 border border-slate-800 text-white font-bold rounded-2xl shadow-xl shadow-slate-200 hover:bg-slate-800 hover:-translate-y-1 transition-all group">
                    <i class="fas fa-file-pdf mr-2 text-rose-400 group-hover:scale-110 transition-transform"></i>
                    Exportar PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Summary Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        @php
            $totalHours = collect($reportData)->sum('hours_worked');
            $completedCount = collect($reportData)->where('is_complete', true)->count();
            $incompletedCount = collect($reportData)->where('is_complete', false)->count();
            $complianceRate = count($reportData) > 0 ? round(($completedCount / count($reportData)) * 100, 1) : 0;
        @endphp

        <div class="bg-white rounded-[2rem] p-8 shadow-premium border border-slate-100 group transition-all duration-500 hover:-translate-y-2">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 bg-sena/10 text-sena rounded-2xl flex items-center justify-center text-xl shadow-inner group-hover:rotate-12 transition-transform">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <h3 class="text-3xl font-bold text-slate-800 font-outfit tracking-tight">{{ number_format($totalHours, 1) }}h</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Horas Totales</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] p-8 shadow-premium border border-slate-100 group transition-all duration-500 hover:-translate-y-2">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center text-xl shadow-inner group-hover:rotate-12 transition-transform">
                    <i class="fas fa-check-double"></i>
                </div>
                <div>
                    <h3 class="text-3xl font-bold text-slate-800 font-outfit tracking-tight">{{ $completedCount }}</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Completados</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] p-8 shadow-premium border border-slate-100 group transition-all duration-500 hover:-translate-y-2">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center text-xl shadow-inner group-hover:rotate-12 transition-transform">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div>
                    <h3 class="text-3xl font-bold text-slate-800 font-outfit tracking-tight">{{ $incompletedCount }}</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Incompletos</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] p-8 shadow-premium border border-slate-100 group transition-all duration-500 hover:-translate-y-2">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 bg-indigo-50 text-indigo-500 rounded-2xl flex items-center justify-center text-xl shadow-inner group-hover:rotate-12 transition-transform">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div>
                    <h3 class="text-3xl font-bold text-slate-800 font-outfit tracking-tight">{{ $complianceRate }}%</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Cumplimiento</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="bg-white/80 backdrop-blur-xl rounded-[2.5rem] shadow-premium border border-slate-100 p-8 mb-10 group hover:shadow-2xl transition-all duration-500">
        <div class="flex items-center gap-4 mb-8">
            <div class="w-10 h-10 bg-slate-900 text-white rounded-xl flex items-center justify-center">
                <i class="fas fa-filter text-sm"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-800 font-outfit">Panel de Filtros</h2>
                <p class="text-sm text-slate-500 font-medium">Refina los datos del reporte por temporalidad o aprendiz</p>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.attendance.detailed-report') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Date From -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] ml-1">Desde Fecha</label>
                    <div class="relative group/input">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within/input:text-sena transition-colors">
                            <i class="fas fa-calendar-alt text-sm"></i>
                        </div>
                        <input type="date" name="date_from" id="date_from"
                            value="{{ request('date_from', now()->subMonth()->format('Y-m-d')) }}"
                            class="block w-full pl-11 pr-4 py-3.5 bg-slate-50/50 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-sena/10 focus:border-sena transition-all">
                    </div>
                </div>

                <!-- Date To -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] ml-1">Hasta Fecha</label>
                    <div class="relative group/input">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within/input:text-sena transition-colors">
                            <i class="fas fa-calendar-check text-sm"></i>
                        </div>
                        <input type="date" name="date_to" id="date_to"
                            value="{{ request('date_to', now()->format('Y-m-d')) }}"
                            class="block w-full pl-11 pr-4 py-3.5 bg-slate-50/50 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-sena/10 focus:border-sena transition-all">
                    </div>
                </div>

                <!-- Apprentice Filter -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] ml-1">Aprendiz</label>
                    <div class="relative group/input">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within/input:text-sena transition-colors">
                            <i class="fas fa-user-graduate text-sm"></i>
                        </div>
                        <select name="apprentice_id" id="apprentice_id"
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

                <!-- Cohort Filter -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] ml-1">Ficha / Cohorte</label>
                    <div class="relative group/input">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within/input:text-sena transition-colors">
                            <i class="fas fa-users text-sm"></i>
                        </div>
                        <select name="cohort" id="cohort"
                            class="block w-full pl-11 pr-4 py-3.5 bg-slate-50/50 border-slate-100 rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-sena/10 focus:border-sena transition-all appearance-none cursor-pointer">
                            <option value="">Todas las fichas</option>
                            @foreach($cohorts as $cohort)
                                <option value="{{ $cohort }}" {{ request('cohort') == $cohort ? 'selected' : '' }}>
                                    {{ $cohort }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 mt-8 pt-6 border-t border-slate-50">
                <a href="{{ route('admin.attendance.detailed-report') }}"
                    class="px-6 py-3 text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors uppercase tracking-widest">
                    Limpiar Filtros
                </a>
                <button type="submit"
                    class="btn-primary-unified flex items-center gap-2 px-8 py-3 shadow-sena shadow-lg shadow-sena/20">
                    <i class="fas fa-search"></i>
                    Generar Reporte
                </button>
            </div>
        </form>
    </div>

    <!-- Report Table -->
    <div class="bg-white rounded-[2.5rem] shadow-premium border border-slate-100 overflow-hidden mb-12">
        <div class="p-8 border-b border-slate-50 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 shadow-inner">
                    <i class="fas fa-list-ul text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-800 font-outfit">Resultados del Reporte</h2>
                    <p class="text-sm text-slate-500 font-medium font-outfit">Desglose completo de asistencia registrada</p>
                </div>
            </div>
        </div>

        @if(count($reportData) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-8 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-[0.2em]">Aprendiz</th>
                            <th class="px-8 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-[0.2em]">Ficha</th>
                            <th class="px-8 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-[0.2em]">Fecha</th>
                            <th class="px-8 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-[0.2em]">E / S</th>
                            <th class="px-8 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-[0.2em]">Duración</th>
                            <th class="px-8 py-5 text-[10px] font-extrabold text-slate-400 uppercase tracking-[0.2em]">Estado</th>
                            <th class="px-8 py-5 text-right text-[10px] font-extrabold text-slate-400 uppercase tracking-[0.2em]">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 font-outfit">
                        @foreach($reportData as $row)
                            <tr class="hover:bg-slate-50/50 transition-all group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl sena-gradient flex items-center justify-center text-white font-bold shadow-sena shadow-md group-hover:scale-110 group-hover:rotate-3 transition-transform overflow-hidden">
                                            @if($row['apprentice']->profile_photo_path)
                                                <img src="{{ Storage::url($row['apprentice']->profile_photo_path) }}" class="w-full h-full object-cover">
                                            @else
                                                {{ strtoupper(substr($row['apprentice']->full_name, 0, 1)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-800 mb-0.5">{{ $row['apprentice']->full_name }}</p>
                                            <p class="text-[11px] text-slate-400 font-medium">{{ $row['apprentice']->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="inline-flex px-3 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-bold border border-slate-200">
                                        {{ $row['apprentice']->apprenticeProfile->cohort ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-sm font-bold text-slate-700">{{ \Carbon\Carbon::parse($row['date'])->translatedFormat('d M, Y') }}</p>
                                    <p class="text-[10px] text-slate-400 uppercase font-bold tracking-widest">{{ \Carbon\Carbon::parse($row['date'])->translatedFormat('l') }}</p>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="flex flex-col">
                                            <span class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest leading-none mb-1">Entrada</span>
                                            <span class="text-sm font-bold text-slate-700">{{ $row['first_entry'] ? $row['first_entry']->occurred_at->format('H:i') : '--:--' }}</span>
                                        </div>
                                        <div class="w-4 h-px bg-slate-200"></div>
                                        <div class="flex flex-col">
                                            <span class="text-[10px] font-bold text-rose-400 uppercase tracking-widest leading-none mb-1">Salida</span>
                                            <span class="text-sm font-bold text-slate-700">{{ $row['last_exit'] ? $row['last_exit']->occurred_at->format('H:i') : '--:--' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-lg font-bold text-indigo-600 tracking-tight leading-none mb-1">
                                            {{ number_format($row['hours_worked'], 1) }}<span class="text-[10px] ml-0.5">h</span>
                                        </span>
                                        <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">Tiempo Efectivo</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    @if($row['is_complete'])
                                        <span class="inline-flex items-center px-4 py-1.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-100">
                                            <i class="fas fa-check-circle mr-1.5 text-xs"></i>
                                            Completado
                                        </span>
                                    @elseif($row['total_entries'] > 0 && $row['total_exits'] == 0)
                                        <span class="inline-flex items-center px-4 py-1.5 bg-amber-50 text-amber-700 text-[10px] font-bold uppercase tracking-widest rounded-full border border-amber-100">
                                            <i class="fas fa-spinner fa-spin mr-1.5 text-xs"></i>
                                            En Curso
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-4 py-1.5 bg-rose-50 text-rose-700 text-[10px] font-bold uppercase tracking-widest rounded-full border border-rose-100">
                                            <i class="fas fa-exclamation-triangle mr-1.5 text-xs"></i>
                                            Incompleto
                                        </span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <button onclick="viewDetails('{{ $row['apprentice']->id }}', '{{ $row['date'] }}')"
                                        class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-slate-900 hover:text-white transition-all shadow-sm active:scale-95">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-32 text-center bg-slate-50/50">
                <div class="w-24 h-24 bg-white rounded-[2rem] shadow-premium flex items-center justify-center mx-auto mb-6 text-slate-200">
                    <i class="fas fa-folder-open text-4xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-800 font-outfit mb-2">Sin datos disponibles</h3>
                <p class="text-slate-400 font-medium max-w-sm mx-auto">Ajusta los filtros para generar un nuevo análisis de asistencia.</p>
            </div>
        @endif
    </div>

    <!-- Modal for Details -->
    <div id="detailsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Detalles de Asistencia</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                <h3 class="text-xl font-bold text-gray-900">Detalles de Asistencia</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <div id="modalContent" class="p-6 overflow-y-auto max-h-[calc(90vh-80px)]">
                <!-- Dynamic content loaded here -->
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function buildQuery() {
            const params = new URLSearchParams();
            const df = document.getElementById('date_from')?.value || '';
            const dt = document.getElementById('date_to')?.value || '';
            const ap = document.getElementById('apprentice_id')?.value || '';
            const ch = document.getElementById('cohort')?.value || '';
            if (df) params.set('date_from', df);
            if (dt) params.set('date_to', dt);
            if (ap) params.set('apprentice_id', ap);
            if (ch) params.set('cohort', ch);
            return params.toString();
        }

        function exportToExcel() {
            const qs = buildQuery();
            Swal.fire({
                title: 'Exportando a Excel...',
                text: 'Preparando su archivo de reporte',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            window.location.href = `{{ route('admin.reports.export') }}?module=attendance&format=csv&${qs}`;

            setTimeout(() => {
                Swal.close();
            }, 2000);
        }

        function exportToPdf() {
            const qs = buildQuery();
            Swal.fire({
                title: 'Exportando a PDF...',
                text: 'Estamos generando su documento',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            window.location.href = `{{ route('admin.reports.export') }}?module=attendance&format=pdf&${qs}`;

            setTimeout(() => {
                Swal.close();
            }, 2000);
        }

        function viewDetails(apprenticeId, date) {
            const modal = document.getElementById('detailsModal');
            const content = document.getElementById('modalContent');

            content.innerHTML = `
            <div class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                <p class="mt-4 text-gray-600">Cargando detalles...</p>
            </div>
        `;

            modal.classList.remove('hidden');

            // Simulate loading - replace with actual AJAX call
            setTimeout(() => {
                content.innerHTML = `
                <div class="space-y-4">
                    <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                        <h4 class="text-lg font-semibold text-blue-900 mb-2 font-outfit">Registros del ${date}</h4>
                        <p class="text-sm text-blue-700 font-medium">Información detallada de asistencia para el aprendiz y fecha seleccionados.</p>
                    </div>
                    <div class="text-sm text-slate-600 bg-slate-50 p-4 rounded-xl border border-slate-100 italic">
                        <p>Este panel permite visualizar cronológicamente cada ingreso y salida, validando la procedencia de cada registro biométrico.</p>
                    </div>
                </div>
            `;
            }, 500);
        }

        function closeModal() {
            document.getElementById('detailsModal').classList.add('hidden');
        }
        document.getElementById('detailsModal')?.addEventListener('click', function (e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
@endpush
