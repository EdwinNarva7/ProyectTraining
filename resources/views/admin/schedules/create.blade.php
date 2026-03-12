@extends('layouts.master')

@section('title', 'Asignar Horario - SIAP Admin')
@section('page-title', 'Crear Nuevo Horario')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.schedules.index') }}">Horarios</a></li>
    <li class="breadcrumb-item active">Asignar</li>
@endsection

@section('content')
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2 font-outfit">Asignar Horario</h1>
                <p class="text-gray-500 text-sm">Defina las horas de trabajo y disponibilidad de un aprendiz</p>
            </div>
            <div>
                <a href="{{ route('admin.schedules.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                        </path>
                    </svg>
                    Volver a la Lista
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
                        <div class="p-2 bg-sena/10 rounded-lg">
                            <svg class="w-5 h-5 text-sena" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        Configuración del Horario
                    </h2>
                </div>

                <form action="{{ route('admin.schedules.store') }}" method="POST" class="p-8 space-y-8" id="scheduleForm">
                    @csrf

                    <!-- Apprentice Selection -->
                    <div class="space-y-4">
                        <label class="block text-sm font-bold text-gray-700 ml-1">Seleccionar Aprendiz <span
                                class="text-rose-500">*</span></label>
                        <div class="relative group">
                            <select name="apprentice_id" id="apprentice_id"
                                class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-sena/50 transition duration-200 appearance-none @error('apprentice_id') border-rose-500 @enderror"
                                required>
                                <option value="">Elija un aprendiz...</option>
                                @foreach ($apprentices as $apprentice)
                                    <option value="{{ $apprentice->id }}"
                                        data-name="{{ $apprentice->full_name }}"
                                        data-email="{{ $apprentice->email }}"
                                        data-cohort="{{ $apprentice->apprenticeProfile?->cohort ?? 'N/A' }}"
                                        {{ old('apprentice_id') == $apprentice->id ? 'selected' : '' }}>
                                        {{ $apprentice->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-sena transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                        @error('apprentice_id')
                            <p class="text-xs text-rose-500 mt-1 ml-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Weekday -->
                        <div class="space-y-4">
                            <label class="block text-sm font-bold text-gray-700 ml-1">Día de la Semana <span
                                    class="text-rose-500">*</span></label>
                            <div class="relative">
                                <select name="weekday" id="weekday"
                                    class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-sena/50 transition duration-200 appearance-none @error('weekday') border-rose-500 @enderror"
                                    required>
                                    <option value="">Seleccionar Día...</option>
                                    <option value="1" {{ old('weekday') == '1' ? 'selected' : '' }}>Lunes</option>
                                    <option value="2" {{ old('weekday') == '2' ? 'selected' : '' }}>Martes</option>
                                    <option value="3" {{ old('weekday') == '3' ? 'selected' : '' }}>Miércoles</option>
                                    <option value="4" {{ old('weekday') == '4' ? 'selected' : '' }}>Jueves</option>
                                    <option value="5" {{ old('weekday') == '5' ? 'selected' : '' }}>Viernes</option>
                                    <option value="6" {{ old('weekday') == '6' ? 'selected' : '' }}>Sábado</option>
                                    <option value="7" {{ old('weekday') == '7' ? 'selected' : '' }}>Domingo</option>
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
                            <label class="block text-sm font-bold text-gray-700 ml-1">Estado Inicial</label>
                            <div class="flex p-1 bg-gray-100 rounded-xl">
                                <label class="flex-1 text-center cursor-pointer">
                                    <input type="radio" name="status" value="activo" class="sr-only peer" checked>
                                    <span class="block py-2.5 px-4 text-sm font-medium rounded-lg peer-checked:bg-white peer-checked:text-sena peer-checked:shadow-sm transition-all text-gray-500">
                                        Activo
                                    </span>
                                </label>
                                <label class="flex-1 text-center cursor-pointer">
                                    <input type="radio" name="status" value="inactivo" class="sr-only peer" {{ old('status') == 'inactivo' ? 'checked' : '' }}>
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
                                class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-sena/50 transition duration-200 @error('start_time') border-rose-500 @enderror"
                                value="{{ old('start_time') }}" required>
                        </div>

                        <div class="space-y-4">
                            <label class="block text-sm font-bold text-gray-700 ml-1">Hora de Fin <span
                                    class="text-rose-500">*</span></label>
                            <input type="time" name="end_time" id="end_time"
                                class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-sena/50 transition duration-200 @error('end_time') border-rose-500 @enderror"
                                value="{{ old('end_time') }}" required>
                        </div>
                    </div>

                    <!-- Duration Display -->
                    <div id="duration_wrapper" class="hidden animate-fade-in">
                        <div class="p-6 bg-indigo-50/50 rounded-2xl border border-indigo-100">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-indigo-100 rounded-lg">
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-indigo-600 uppercase tracking-wider">Duración Calculada</p>
                                        <p class="text-lg font-bold text-gray-900" id="duration_text">0.00 Horas</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="w-32 h-2 bg-indigo-200 rounded-full overflow-hidden">
                                        <div id="duration_progress" class="h-full bg-indigo-600 transition-all duration-500" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex items-center justify-between">
                        <button type="submit"
                            class="inline-flex items-center px-8 py-3.5 bg-sena hover:bg-sena-dark text-white font-bold rounded-xl shadow-lg shadow-sena/30 transition-all duration-200 hover:shadow-xl hover:shadow-sena/40 -translate-y-0.5 active:translate-y-0">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Guardar asignación
                        </button>
                        <p class="text-xs text-gray-400 font-medium italic">Todos los campos marcados con * son obligatorios</p>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column: Sidebar -->
        <div class="space-y-6">
            <!-- Apprentice Preview -->
            <div id="apprentice_card" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 transition-all duration-300 opacity-50 grayscale pointer-events-none">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-6">Aprendiz Seleccionado</h3>
                
                <div class="flex flex-col items-center text-center">
                    <div id="app_avatar" class="w-24 h-24 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 text-3xl font-bold mb-4 shadow-inner ring-4 ring-white transition-all duration-300">
                        ?
                    </div>
                    <h4 id="app_name" class="text-xl font-bold text-gray-900 mb-1">Esperando Selección...</h4>
                    <p id="app_email" class="text-sm text-gray-500 mb-6 font-medium">Por favor seleccione un aprendiz</p>

                    <div class="w-full space-y-3">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <span class="text-xs font-bold text-gray-400 uppercase">Ficha</span>
                            <span id="app_cohort" class="text-sm font-bold text-gray-900">--</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <span class="text-xs font-bold text-gray-400 uppercase">Estado</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-200 text-gray-600">Pendiente</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule Visualizer -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-6">Vista General del Horario</h3>
                
                <div class="space-y-6">
                    <!-- Visual Timeline -->
                    <div class="relative pt-4 pb-2">
                        <div class="h-1.5 w-full bg-gray-100 rounded-full"></div>
                        <div id="timeline_marker" class="absolute top-4 h-1.5 bg-sena rounded-full shadow-sm transition-all duration-500" style="left: 0%; width: 0%"></div>
                        
                        <div class="flex justify-between text-[10px] font-bold text-gray-300 mt-2">
                            <span>00:00</span>
                            <span>12:00</span>
                            <span>23:59</span>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-white rounded-lg shadow-sm">
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Sugerencia</p>
                                <p class="text-xs text-gray-600 leading-relaxed font-medium">Los turnos estándar suelen ser de 8 horas, incluyendo el descanso.</p>
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
    const apprenticeSelect = $('#apprentice_id');
    const startTimeInput = $('#start_time');
    const endTimeInput = $('#end_time');

    // Apprentice Selection Change
    apprenticeSelect.on('change', function() {
        const selected = $(this).find(':selected');
        if (selected.val()) {
            const name = selected.data('name');
            const email = selected.data('email');
            const cohort = selected.data('cohort');
            const initials = name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();

            $('#apprentice_card').removeClass('opacity-50 grayscale pointer-events-none');
            $('#app_avatar').html(initials).addClass('bg-gradient-to-br from-sena to-sena-dark text-white ring-sena/20');
            $('#app_name').text(name);
            $('#app_email').text(email);
            $('#app_cohort').text(cohort);
        } else {
            $('#apprentice_card').addClass('opacity-50 grayscale pointer-events-none');
            $('#app_avatar').html('?').removeClass('bg-gradient-to-br from-sena to-sena-dark text-white ring-sena/20');
            $('#app_name').text('Esperando Selección...');
            $('#app_email').text('Por favor seleccione un aprendiz');
            $('#app_cohort').text('--');
        }
    });

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
                
                // Progress bar (max 24h)
                const percent = (hours / 24) * 100;
                $('#duration_progress').css('width', Math.min(percent, 100) + '%');

                // Timeline marker
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
    
    // Initial calculation if editing/old input exists
    if (startTimeInput.val() || endTimeInput.val()) updateCalculations();
    if (apprenticeSelect.val()) apprenticeSelect.trigger('change');

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
                confirmButtonColor: '#10B981',
                customClass: { confirmButton: 'rounded-xl px-6 py-2.5 font-medium' }
            });
        }
    });

    // Success styling for focus
    $('input, select').on('focus', function() {
        $(this).parent().find('.absolute').addClass('text-sena');
    }).on('blur', function() {
        $(this).parent().find('.absolute').removeClass('text-sena');
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

