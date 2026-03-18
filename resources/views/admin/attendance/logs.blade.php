@extends('layouts.master')

@section('title', 'Registros de Asistencia - SIEAP Admin')
@section('page-title', 'Registros de Asistencia')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.attendance.index') }}">Asistencia</a></li>
    <li class="breadcrumb-item active text-sena font-bold">Registros Históricos</li>
@endsection

@section('content')
    <div class="space-y-8 animate-fade-in pb-12">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 py-4">
            <div>
                <h1 class="text-4xl font-bold text-slate-800 tracking-tight font-outfit">
                    Registro de <span class="text-sena">Actividad</span>
                </h1>
                <p class="text-slate-500 mt-2 font-medium text-lg">Historial detallado de todas las marcaciones registradas.</p>
            </div>
            <div class="flex items-center gap-4">
                <button onclick="exportLogs()"
                    class="flex items-center gap-3 px-6 py-4 bg-white border border-slate-200 text-slate-700 rounded-[1.5rem] font-bold hover:bg-slate-50 transition-all shadow-premium active:scale-95 group">
                    <i class="fas fa-file-export text-blue-500 group-hover:scale-110 transition-transform"></i>
                    Exportar Reporte
                </button>
            </div>
        </div>

        {{-- Filters Section --}}
        <div class="bg-white rounded-[3rem] shadow-premium border border-slate-100 p-10 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-64 h-64 bg-slate-50 rounded-full blur-3xl opacity-50 -mr-20 -mt-20 group-hover:bg-blue-50/50 transition-colors duration-700"></div>
            
            <form method="GET" action="{{ route('admin.attendance.logs') }}" class="relative z-10 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    <!-- Aprendiz -->
                    <div class="space-y-3">
                        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Aprendiz</label>
                        <div class="relative group/input">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 group-focus-within/input:text-sena transition-colors">
                                <i class="fas fa-user text-sm"></i>
                            </div>
                            <select name="apprentice_id" id="apprentice_id"
                                class="w-full pl-12 pr-10 py-4 rounded-2xl bg-slate-50/50 border-slate-100 text-slate-700 font-bold text-sm focus:bg-white focus:ring-4 focus:ring-sena/10 focus:border-sena transition-all appearance-none cursor-pointer">
                                <option value="">Todos los aprendices</option>
                                @foreach($apprentices ?? [] as $apprentice)
                                    <option value="{{ $apprentice->id }}" {{ request('apprentice_id') == $apprentice->id ? 'selected' : '' }}>
                                        {{ $apprentice->full_name }}
                                    </option>
                                @endforeach 
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-300">
                                <i class="fas fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Fecha Desde (Implied from date_from) -->
                    <div class="space-y-3">
                        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Desde</label>
                        <div class="relative group/input">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 group-focus-within/input:text-sena transition-colors">
                                <i class="fas fa-calendar-alt text-sm"></i>
                            </div>
                            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                                class="w-full pl-12 pr-5 py-4 rounded-2xl bg-slate-50/50 border-slate-100 text-slate-700 font-bold text-sm focus:bg-white focus:ring-4 focus:ring-sena/10 focus:border-sena transition-all">
                        </div>
                    </div>

                    <!-- Fecha Hasta -->
                    <div class="space-y-3">
                        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Hasta</label>
                        <div class="relative group/input">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 group-focus-within/input:text-sena transition-colors">
                                <i class="fas fa-calendar-check text-sm"></i>
                            </div>
                            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                                class="w-full pl-12 pr-5 py-4 rounded-2xl bg-slate-50/50 border-slate-100 text-slate-700 font-bold text-sm focus:bg-white focus:ring-4 focus:ring-sena/10 focus:border-sena transition-all">
                        </div>
                    </div>

                    <!-- Origen -->
                    <div class="space-y-3">
                        <label class="block text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Origen</label>
                        <div class="relative group/input">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-400 group-focus-within/input:text-sena transition-colors">
                                <i class="fas fa-satellite-dish text-sm"></i>
                            </div>
                            <select name="source" id="source"
                                class="w-full pl-12 pr-10 py-4 rounded-2xl bg-slate-50/50 border-slate-100 text-slate-700 font-bold text-sm focus:bg-white focus:ring-4 focus:ring-sena/10 focus:border-sena transition-all appearance-none cursor-pointer">
                                <option value="">Todos los orígenes</option>
                                <option value="lector" {{ request('source') == 'lector' ? 'selected' : '' }}>Lector Biométrico</option>
                                <option value="web" {{ request('source') == 'web' ? 'selected' : '' }}>Plataforma Web</option>
                                <option value="admin" {{ request('source') == 'admin' ? 'selected' : '' }}>Asistencia Manual</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-300">
                                <i class="fas fa-chevron-down text-[10px]"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex pt-4 justify-end gap-3 border-t border-slate-50">
                    <a href="{{ route('admin.attendance.logs') }}"
                        class="px-8 py-3.5 bg-slate-100 text-slate-500 rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-slate-200 transition-all active:scale-95">
                        Limpiar
                    </a>
                    <button type="submit"
                        class="px-10 py-3.5 sena-gradient text-white rounded-2xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-sena/20 hover:shadow-sena/40 active:scale-95 transition-all flex items-center gap-3">
                        <i class="fas fa-filter text-xs"></i> Filtrar Historial
                    </button>
                </div>
            </form>
        </div>

        {{-- Table Card --}}
        <div class="bg-white rounded-[3rem] shadow-premium border border-slate-100 overflow-hidden group">
            <div class="p-10 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                <div>
                    <h3 class="text-2xl font-bold text-slate-800 font-outfit">Bitácora de <span class="text-sena font-black">Asistencias</span></h3>
                    <p class="text-[11px] text-slate-400 mt-2 font-bold uppercase tracking-[0.2em]">
                        @if(isset($logs) && $logs->total() > 0)
                            Mostrando {{ $logs->total() }} registros históricos
                        @else
                            No se encontraron registros activos
                        @endif
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80">
                            <th class="px-10 py-6 text-[11px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100">Aprendiz</th>
                            <th class="px-6 py-6 text-[11px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100 text-center">Evento</th>
                            <th class="px-6 py-6 text-[11px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100">Fecha y Hora</th>
                            <th class="px-6 py-6 text-[11px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100">Origen</th>
                            <th class="px-6 py-6 text-[11px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100">Observaciones</th>
                            <th class="px-10 py-6 text-[11px] font-bold text-slate-500 uppercase tracking-widest text-right border-b border-slate-100">Ver</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50/50">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-50/40 transition-all group/item">
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-5">
                                        <div class="w-12 h-12 sena-gradient rounded-2xl flex items-center justify-center text-white font-bold text-sm shadow-md group-hover/item:scale-110 transition-transform shrink-0 border-2 border-white overflow-hidden">
                                            @if($log->apprentice?->profile_photo_path)
                                                <img src="{{ Storage::url($log->apprentice->profile_photo_path) }}" class="w-full h-full object-cover">
                                            @else
                                                @php
                                                    $nameParts = explode(' ', $log->apprentice?->full_name ?? 'A A');
                                                    $initials = strtoupper(substr($nameParts[0],0,1) . (isset($nameParts[1]) ? substr($nameParts[1],0,1) : ''));
                                                @endphp
                                                {{ $initials }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800 text-sm tracking-tight mb-0.5">{{ $log->apprentice?->full_name ?? 'Aprendiz Desconocido' }}</div>
                                            <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ $log->apprentice?->email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    @if($log->isEntry())
                                        <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-green-50 text-sena rounded-full text-[10px] font-black uppercase tracking-widest border border-green-100">
                                            <i class="fas fa-sign-in-alt"></i> Entrada
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-amber-50 text-amber-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-amber-100">
                                            <i class="fas fa-sign-out-alt"></i> Salida
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-6">
                                    <div class="text-sm font-bold text-slate-700 tracking-tight">{{ $log->occurred_at->format('d/m/Y') }}</div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">{{ $log->occurred_at->format('H:i:s A') }}</div>
                                </td>
                                <td class="px-6 py-6 font-medium">
                                    @php
                                        $sourceConfigs = [
                                            'lector' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'border' => 'border-purple-100', 'icon' => 'fa-fingerprint', 'label' => 'BIOMÉTRICO'],
                                            'web' => ['bg' => 'bg-sky-50', 'text' => 'text-sky-600', 'border' => 'border-sky-100', 'icon' => 'fa-globe', 'label' => 'WEB'],
                                            'admin' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'border' => 'border-slate-200', 'icon' => 'fa-user-shield', 'label' => 'ADMIN'],
                                        ];
                                        $source = $log->source ?? 'web';
                                        $config = $sourceConfigs[$source] ?? $sourceConfigs['web'];
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-[9px] font-black {{ $config['bg'] }} {{ $config['text'] }} {{ $config['border'] }} border shadow-sm">
                                        <i class="fas {{ $config['icon'] }} mr-2 opacity-70"></i>
                                        {{ $config['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-6">
                                    @if($log->note)
                                        <div class="text-xs font-bold text-slate-500 max-w-[200px] truncate" title="{{ $log->note }}">
                                            {{ $log->note }}
                                        </div>
                                    @else
                                        <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest italic">Sin notas</span>
                                    @endif
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <button onclick="viewLogDetails({{ $log->id }}, '{{ addslashes($log->apprentice?->full_name) }}', '{{ $log->occurred_at->format('d/m/Y H:i:s') }}')"
                                        class="w-10 h-10 rounded-xl bg-white text-slate-400 hover:text-sena border border-slate-100 shadow-premium flex items-center justify-center transition-all hover:scale-110 active:scale-95 group/btn">
                                        <i class="fas fa-eye text-sm group-hover/btn:scale-110 transition-transform"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-10 py-24 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-24 h-24 bg-slate-50 rounded-[2.5rem] flex items-center justify-center text-slate-200 mb-6 shadow-inner border border-slate-100">
                                            <i class="fas fa-search-minus text-4xl"></i>
                                        </div>
                                        <p class="text-slate-400 font-black uppercase tracking-[0.4em] text-xs">No se encontraron marcaciones</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if(isset($logs) && $logs->hasPages())
                <div class="px-10 py-8 bg-slate-50/50 border-t border-slate-50 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="text-xs font-black text-slate-400 uppercase tracking-widest">
                        Exhibiendo <span class="text-slate-900">{{ $logs->firstItem() }}-{{ $logs->lastItem() }}</span> de <span class="text-slate-900">{{ $logs->total() }}</span> registros
                    </div>
                    <div class="pagination-premium">
                        {{ $logs->appends(request()->query())->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function viewLogDetails(id, name, time) {
            Swal.fire({
                title: '<span class="font-outfit font-black uppercase tracking-tight">Detalle de Marcación</span>',
                html: `
                    <div class="text-center p-6 bg-slate-50 rounded-[2rem] border border-slate-100 shadow-inner">
                        <div class="w-20 h-20 sena-gradient rounded-[1.5rem] flex items-center justify-center text-white mx-auto mb-6 shadow-xl border-4 border-white">
                            <i class="fas fa-fingerprint text-3xl"></i>
                        </div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-2">Aprendiz Identificado</p>
                        <h4 class="text-2xl font-black text-slate-800 font-outfit uppercase tracking-tight mb-6">${name}</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-1">Registro ID</p>
                                <p class="text-sm font-bold text-slate-700">#${id}</p>
                            </div>
                            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-1">Sello de Tiempo</p>
                                <p class="text-sm font-bold text-slate-700">${time}</p>
                            </div>
                        </div>
                    </div>
                `,
                showCloseButton: true,
                showConfirmButton: false,
                customClass: {
                    popup: 'rounded-[3rem] p-4 border-2 border-slate-50',
                    closeButton: 'rounded-full hover:bg-slate-100 transition-colors'
                }
            });
        }

        function exportLogs(format = 'csv') {
            const params = new URLSearchParams(window.location.search);
            const qs = params.toString();
            const url = `{{ route('admin.reports.export') }}?module=logs&format=${format}${qs ? '&' + qs : ''}`;
            if (window.Swal) {
                Swal.fire({
                    title: 'Generando Reporte',
                    text: 'Preparando su archivo...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                setTimeout(() => { window.location.href = url; }, 300);
            } else {
                window.location.href = url;
            }
        }
    </script>
@endpush
