@extends('layouts.master')

@section('title', 'Editar Horario - SIAP Admin')
@section('page-title', 'Editar Horario')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.schedules.index') }}">Horarios</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2 font-outfit">Editar Asignación</h1>
                <p class="text-gray-500 text-sm">Modificando horario para {{ $schedule->apprentice->full_name }}</p>
            </div>
            <div>
                <a href="{{ route('admin.schedules.show', $schedule) }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                        </path>
                    </svg>
                    Volver a Detalles
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-100 bg-gray-50/30">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <div class="p-2 bg-amber-50 rounded-lg">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </div>
                        Actualizar Asignación
                    </h2>
                </div>

                <form action="{{ route('admin.schedules.update', $schedule) }}" method="POST" class="p-8 space-y-8" id="scheduleForm">
                    @csrf
                    @method('PUT')

                    <!-- Apprentice Selection (Disabled/ReadOnly feel) -->
                    <div class="space-y-4">
                        <label class="block text-sm font-bold text-gray-700 ml-1">Aprendiz</label>
                        <div class="relative group opacity-75">
                            <select name="apprentice_id" id="apprentice_id"
                                class="w-full pl-12 pr-4 py-3.5 bg-gray-100 border border-gray-200 rounded-xl text-gray-500 focus:outline-none cursor-not-allowed appearance-none"
                                required>
                                @foreach ($apprentices as $apprentice)
                                    <option value="{{ $apprentice->id }}"
                                        data-name="{{ $apprentice->full_name }}"
                                        data-email="{{ $apprentice->email }}"
                                        data-cohort="{{ $apprentice->apprenticeProfile?->cohort ?? 'N/A' }}"
                                        {{ old('apprentice_id', $schedule->apprentice_id) == $apprentice->id ? 'selected' : '' }}>
                                        {{ $apprentice->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Weekday -->
                        <div class="space-y-4">
                            <label class="block text-sm font-bold text-gray-700 ml-1">Día de la Semana <span
                                    class="text-rose-500">*</span></label>
                            <div class="relative">
                                <select name="weekday" id="weekday"
                                    class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition duration-200 appearance-none @error('weekday') border-rose-500 @enderror"
                                    required>
                                    <option value="">Seleccionar Día...</option>
                                    <option value="1" {{ old('weekday', $schedule->weekday) == '1' ? 'selected' : '' }}>Lunes</option>
                                    <option value="2" {{ old('weekday', $schedule->weekday) == '2' ? 'selected' : '' }}>Martes</option>
                                    <option value="3" {{ old('weekday', $schedule->weekday) == '3' ? 'selected' : '' }}>Miércoles</option>
                                    <option value="4" {{ old('weekday', $schedule->weekday) == '4' ? 'selected' : '' }}>Jueves</option>
                                    <option value="5" {{ old('weekday', $schedule->weekday) == '5' ? 'selected' : '' }}>Viernes</option>
                                    <option value="6" {{ old('weekday', $schedule->weekday) == '6' ? 'selected' : '' }}>Sábado</option>
                                    <option value="7" {{ old('weekday', $schedule->weekday) == '7' ? 'selected' : '' }}>Domingo</option>
                                </select>
                                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="space-y-4">
                            <label class="block text-sm font-bold text-gray-700 ml-1">Estado Actual</label>
                            <div class="flex p-1 bg-gray-100 rounded-xl">
                                <label class="flex-1 text-center cursor-pointer">
                                    <input type="radio" name="status" value="activo" class="sr-only peer" {{ old('status', $schedule->status) == 'activo' ? 'checked' : '' }}>
                                    <span class="block py-2.5 px-4 text-sm font-medium rounded-lg peer-checked:bg-white peer-checked:text-sena peer-checked:shadow-sm transition-all text-gray-500">
                                        Activo
                                    </span>
                                </label>
                                <label class="flex-1 text-center cursor-pointer">
                                    <input type="radio" name="status" value="inactivo" class="sr-only peer" {{ old('status', $schedule->status) == 'inactivo' ? 'checked' : '' }}>
                                    <span class="block py-2.5 px-4 text-sm font-medium rounded-lg peer-checked:bg-white peer-checked:text-rose-600 peer-checked:shadow-sm transition-all text-gray-500">
                                        Inactivo
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Time Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4">
                        <div class="space-y-4">
                            <label class="block text-sm font-bold text-gray-700 ml-1">Hora de Inicio <span
                                    class="text-rose-500">*</span></label>
                            <input type="time" name="start_time" id="start_time"
                                class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition duration-200 @error('start_time') border-rose-500 @enderror"
                                value="{{ old('start_time', $schedule->start_time->format('H:i')) }}" required>
                        </div>

                        <div class="space-y-4">
                            <label class="block text-sm font-bold text-gray-700 ml-1">Hora de Fin <span
                                    class="text-rose-500">*</span></label>
                            <input type="time" name="end_time" id="end_time"
                                class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-amber-500/50 transition duration-200 @error('end_time') border-rose-500 @enderror"
                                value="{{ old('end_time', $schedule->end_time->format('H:i')) }}" required>
                        </div>
                    </div>

                    <!-- Duration Display -->
                    <div id="duration_wrapper" class="animate-fade-in">
                        <div class="p-6 bg-amber-50/50 rounded-2xl border border-amber-100">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-amber-100 rounded-lg">
                                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-amber-600 uppercase tracking-wider">Duración Actualizada</p>
                                        <p class="text-lg font-bold text-gray-900" id="duration_text">0.00 Horas</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="w-32 h-2 bg-amber-200 rounded-full overflow-hidden shadow-inner">
                                        <div id="duration_progress" class="h-full bg-amber-600 transition-all duration-500" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex items-center justify-between">
                        <button type="submit"
                            class="inline-flex items-center px-8 py-3.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow-lg shadow-amber-500/30 transition-all duration-200 hover:shadow-xl hover:shadow-amber-500/40 -translate-y-0.5 active:translate-y-0 text-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Guardar Cambios
                        </button>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Modificado por el Sistema</p>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column: Sidebar -->
        <div class="space-y-6">
            <!-- Current Status Detail -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-6">Registro Actual</h3>
                
                <div class="flex flex-col items-center text-center">
                    <div id="app_avatar" class="w-24 h-24 rounded-full bg-gradient-to-br from-sena to-sena-dark flex items-center justify-center text-white text-3xl font-bold mb-4 shadow-lg ring-4 ring-sena/10">
                        {{ strtoupper(substr($schedule->apprentice->full_name, 0, 2)) }}
                    </div>
                    <h4 id="app_name" class="text-xl font-bold text-gray-900 mb-1">{{ $schedule->apprentice->full_name }}</h4>
                    <p id="app_email" class="text-sm text-gray-500 mb-6 font-medium">{{ $schedule->apprentice->email }}</p>

                    <div class="w-full space-y-3">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">ID</span>
                            <span class="text-sm font-bold text-gray-900">#{{ $schedule->id }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Creado</span>
                            <span class="text-sm font-bold text-gray-900">{{ $schedule->created_at->format('d M, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule Visualizer -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-6">Nueva Vista Previa</h3>
                
                <div class="space-y-6">
                    <div class="relative pt-4 pb-2">
                        <div class="h-1.5 w-full bg-gray-100 rounded-full shadow-inner"></div>
                        <div id="timeline_marker" class="absolute top-4 h-1.5 bg-amber-500 rounded-full shadow-sm shadow-amber-500/50 transition-all duration-500" style="left: 0%; width: 0%"></div>
                        
                        <div class="flex justify-between text-[10px] font-bold text-gray-300 mt-2 px-1">
                            <span>00:00</span>
                            <span class="text-gray-200">12:00</span>
                            <span>23:59</span>
                        </div>
                    </div>

                    <div class="bg-indigo-50/50 rounded-xl p-4 border border-indigo-100">
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-white rounded-lg shadow-sm">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-indigo-400 uppercase mb-1">Recordatorio</p>
                                <p class="text-xs text-gray-600 leading-relaxed font-medium tracking-tight">Actualizar este horario afectará los futuros logs de asistencia para este día.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const startTimeInput = $('#start_time');
    const endTimeInput = $('#end_time');

    // Duration and Timeline Calculation
    function updateCalculations() {
        const start = startTimeInput.val();
        const end = endTimeInput.val();

        if (start && end) {
            const startDate = new Date(`2000-01-01T${start}`);
            const endDate = new Date(`2000-01-01T${end}`);

            if (endDate > startDate) {
                const diffMs = endDate - startDate;
                const hours = diffMs / (1000 * 60 * 60);

                $('#duration_wrapper').removeClass('hidden');
                $('#duration_text').text(hours.toFixed(2) + ' Horas');
                
                const percent = (hours / 24) * 100;
                $('#duration_progress').css('width', Math.min(percent, 100) + '%');

                const startMinutes = startDate.getHours() * 60 + startDate.getMinutes();
                const totalMinutes = 24 * 60;
                const markerLeft = (startMinutes / totalMinutes) * 100;
                const markerWidth = (hours / 24) * 100;

                $('#timeline_marker').css({
                    'left': markerLeft + '%',
                    'width': markerWidth + '%'
                });
            } else {
                $('#duration_wrapper').addClass('hidden');
            }
        }
    }

    startTimeInput.on('change', updateCalculations);
    endTimeInput.on('change', updateCalculations);
    
    // Initial calculation
    updateCalculations();

    // Form Validation Enhancements
    $('#scheduleForm').on('submit', function(e) {
        const start = startTimeInput.val();
        const end = endTimeInput.val();

        if (new Date(`2000-01-01T${end}`) <= new Date(`2000-01-01T${start}`)) {
            e.preventDefault();
            Swal.fire({
                title: 'Rango de Tiempo Inválido',
                text: 'La hora de fin debe ser estrictamente después de la hora de inicio.',
                icon: 'error',
                confirmButtonColor: '#F59E0B',
                customClass: { confirmButton: 'rounded-xl px-6 py-2.5 font-bold' }
            });
        }
    });

    // Success styling for focus
    $('input, select').on('focus', function() {
        $(this).parent().find('.absolute').addClass('text-amber-500');
    }).on('blur', function() {
        $(this).parent().find('.absolute').removeClass('text-amber-500');
    });
});
</script>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fade-in 0.3s ease-out forwards;
}
</style>
@endpush

