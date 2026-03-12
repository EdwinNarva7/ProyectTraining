@extends('layouts.master')

@section('title', 'Ver Horario - SIAP Admin')
@section('page-title', 'Detalles del Horario')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.schedules.index') }}">Horarios</a></li>
    <li class="breadcrumb-item active">Ver</li>
@endsection

@section('content')
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2 font-outfit">Detalles de la Asignación</h1>
                <p class="text-gray-500 text-sm">Revisando las horas de trabajo para {{ $schedule->apprentice->full_name }}</p>
            </div>
            <div class="flex gap-3">
                    Editar Asignación
                </a>
                    Volver a la Lista
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Apprentice Profile Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <!-- Avatar -->
                <div class="flex justify-center mb-6">
                    <div
                        class="w-24 h-24 rounded-full bg-gradient-to-br from-sena to-sena-dark flex items-center justify-center text-white text-3xl font-bold shadow-lg ring-4 ring-sena/10">
                        {{ strtoupper(substr($schedule->apprentice->full_name, 0, 2)) }}
                    </div>
                </div>

                <!-- Name & Role -->
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ $schedule->apprentice->full_name }}</h2>
                    <p class="text-sm text-gray-500 font-medium">{{ $schedule->apprentice->email }}</p>
                    <div class="mt-4 flex justify-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 uppercase tracking-wider">
                            Aprendiz
                        </span>
                    </div>
                </div>

                <!-- Profile Info -->
                <div class="space-y-4">
                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Ficha (Cohort) / Grupo</p>
                        <p class="text-sm font-bold text-gray-900">{{ $schedule->apprentice->apprenticeProfile->cohort ?? 'N/A' }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Número de Documento</p>
                        <p class="text-sm font-bold text-gray-900">{{ $schedule->apprentice->apprenticeProfile->document_number ?? 'N/A' }}</p>
                    </div>
                </div>

                <!-- Action Button Status Toggle -->
                <div class="mt-8">
                    <button type="button" onclick="toggleStatus({{ $schedule->id }})"
                        class="w-full flex items-center justify-center gap-2 py-3.5 px-4 rounded-xl font-bold transition-all duration-200 {{ $schedule->status === 'activo' ? 'bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-100' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100 border border-emerald-100' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 12.728l-3.536-3.536M12 3v4m0 10v4m9-9h-4M7 12H3"></path>
                        </svg>
                        <span>{{ $schedule->status === 'activo' ? 'Desactivar Horario' : 'Activar Horario' }}</span>
                    </button>
                </div>
            </div>

            <!-- Other Sessions Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-6">Otros Días Activos</h3>
                
                @php
                    $otherSchedules = $schedule->apprentice->schedules()
                        ->where('id', '!=', $schedule->id)
                        ->where('status', 'activo')
                        ->orderBy('weekday')
                        ->get();
                @endphp

                <div class="space-y-3">
                    @forelse($otherSchedules as $other)
                        <a href="{{ route('admin.schedules.show', $other) }}" class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-indigo-50 group transition-colors border border-transparent hover:border-indigo-100">
                            <div>
                                <p class="text-xs font-bold text-gray-900 group-hover:text-indigo-700 transition-colors uppercase">{{ $other->weekday_name }}</p>
                                <p class="text-[10px] text-gray-500 font-medium tracking-tight">{{ $other->start_time->format('H:i') }} - {{ $other->end_time->format('H:i') }}</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-300 group-hover:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    @empty
                        <div class="text-center py-6">
                            <p class="text-xs text-gray-400 font-medium italic">No hay otros horarios activos</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Main Details Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Schedule Details Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Información del Horario</h2>
                        <p class="text-sm text-gray-500">Registro oficial de asignación de tiempo</p>
                    </div>
                    @if($schedule->status === 'activo')
                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-2 animate-pulse"></span>
                            ACTIVO
                        </span>
                    @else
                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-gray-100 text-gray-500 border border-gray-200">
                            INACTIVO
                        </span>
                    @endif
                </div>

                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-12">
                    <div class="space-y-8">
                        <!-- Weekday -->
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center flex-shrink-0 border border-indigo-100">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Día Asignado</p>
                                <p class="text-xl font-bold text-gray-900 uppercase mt-1">{{ $schedule->weekday_name }}</p>
                            </div>
                        </div>

                        <!-- Duration -->
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center flex-shrink-0 border border-emerald-100">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Duración Total</p>
                                @php
                                    $start = \Carbon\Carbon::parse($schedule->start_time);
                                    $end = \Carbon\Carbon::parse($schedule->end_time);
                                    $duration = $start->diffInMinutes($end);
                                    $hours = floor($duration / 60);
                                    $minutes = $duration % 60;
                                @endphp
                                <p class="text-xl font-bold text-gray-900 mt-1">{{ $hours }} Horas {{ $minutes > 0 ? $minutes . 'm' : '' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <!-- Start Time -->
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center flex-shrink-0 border border-emerald-100">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Hora de Inicio</p>
                                <p class="text-2xl font-black text-emerald-600 mt-1">{{ $schedule->start_time->format('H:i') }}</p>
                            </div>
                        </div>

                        <!-- End Time -->
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-rose-50 rounded-xl flex items-center justify-center flex-shrink-0 border border-rose-100">
                                <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Hora de Fin</p>
                                <p class="text-2xl font-black text-rose-600 mt-1">{{ $schedule->end_time->format('H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visual Timeline -->
                <div class="px-8 pb-8">
                    <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Línea de Tiempo Diaria (24h)</h4>
                        <div class="relative pt-6 pb-2">
                             <div class="h-3 w-full bg-gray-200 rounded-full overflow-hidden shadow-inner">
                                @php
                                    $startMinutes = $start->hour * 60 + $start->minute;
                                    $totalMinutes = 24 * 60;
                                    $markerLeft = ($startMinutes / $totalMinutes) * 100;
                                    $markerWidth = ($duration / $totalMinutes) * 100;
                                @endphp
                                <div class="h-full bg-gradient-to-r from-sena to-sena-dark shadow-sm" style="margin-left: {{ $markerLeft }}%; width: {{ $markerWidth }}%;"></div>
                             </div>
                             <div class="flex justify-between text-[10px] font-bold text-gray-400 mt-3 uppercase tracking-tighter">
                                <span>00:00</span>
                                <span>04:00</span>
                                <span>08:00</span>
                                <span>12:00</span>
                                <span>16:00</span>
                                <span>20:00</span>
                                <span>23:59</span>
                             </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Metadata Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Recorded By -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Auditoría de Registro</h4>
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-[10px] font-bold text-gray-400 uppercase mb-0.5">Creado Por</p>
                                <p class="text-sm font-bold text-gray-900">{{ $schedule->createdBy->full_name ?? 'Sistema' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 border-t border-gray-50 pt-4">
                            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-[10px] font-bold text-gray-400 uppercase mb-0.5">Fecha de Creación</p>
                                <p class="text-sm font-bold text-gray-900">{{ $schedule->created_at->format('d/m/Y \a las H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Last Update -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Última Actualización</h4>
                    <div class="flex flex-col items-center justify-center h-full py-2">
                        <div class="w-16 h-16 rounded-2xl bg-indigo-50 flex items-center justify-center mb-4 border border-indigo-100">
                             <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                             </svg>
                        </div>
                        <p class="text-sm font-bold text-gray-900">{{ $schedule->updated_at->diffForHumans() }}</p>
                        <p class="text-[10px] font-bold text-gray-400 uppercase mt-1">{{ $schedule->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Delete Form -->
            <div class="pt-4">
                <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="confirmDelete()"
                        class="w-full flex items-center justify-center gap-2 py-4 px-4 bg-white hover:bg-rose-50 text-rose-500 rounded-2xl font-bold transition-all duration-200 border border-gray-100 hover:border-rose-100 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Eliminar Permanentemente
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function toggleStatus(scheduleId) {
    Swal.fire({
        title: '¿Cambiar estado?',
        text: "¿Está seguro de que desea actualizar la disponibilidad de este horario?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#9CA3AF',
        confirmButtonText: 'Sí, actualizar',
        cancelButtonText: 'Cancelar',
        customClass: {
            confirmButton: 'rounded-xl px-6 py-2.5 font-bold',
            cancelButton: 'rounded-xl px-6 py-2.5 font-bold'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Updating...',
                didOpen: () => { Swal.showLoading(); }
            });

            fetch(`/admin/schedules/${scheduleId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: '¡Actualizado!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonColor: '#10B981',
                        customClass: { confirmButton: 'rounded-xl px-6 py-2.5 font-bold' }
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message,
                        icon: 'error',
                        confirmButtonColor: '#EF4444',
                        customClass: { confirmButton: 'rounded-xl px-6 py-2.5 font-bold' }
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error del Sistema',
                    text: 'No se pudo conectar al servidor',
                    icon: 'error',
                    confirmButtonColor: '#EF4444',
                    customClass: { confirmButton: 'rounded-xl px-6 py-2.5 font-bold' }
                });
            });
        }
    });
}

function confirmDelete() {
    Swal.fire({
        title: '¿Eliminar Asignación?',
        text: "Esta acción no se puede deshacer. Toda la información relacionada será eliminada.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#9CA3AF',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        customClass: {
            confirmButton: 'rounded-xl px-6 py-2.5 font-bold',
            cancelButton: 'rounded-xl px-6 py-2.5 font-bold'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteForm').submit();
        }
    });
}
</script>
@endpush

