@extends('layouts.master')

@section('title', 'Editar Fase - SIAP Admin')
@section('page-title', 'Editar Fase')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.phases.index') }}">Fases</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
    <!-- Header Section -->
    <div class="mb-10">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-4xl font-extrabold text-slate-900 mb-2 font-outfit tracking-tight uppercase">Editar <span class="text-sena">Fase</span></h1>
                <p class="text-slate-500 font-bold text-[11px] uppercase tracking-[0.2em] opacity-60">Actualice la información de la fase seleccionada.</p>
            </div>
            <div>
                <a href="{{ route('admin.phases.index') }}"
                    class="inline-flex items-center px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-2xl transition-all duration-200 uppercase tracking-widest text-[10px] gap-2">
                    <i class="fas fa-arrow-left"></i>
                    Volver al Listado
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-3xl">
        <form action="{{ route('admin.phases.update', $phase) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="bg-white rounded-[2.5rem] shadow-premium border border-slate-100 overflow-hidden">
                <div class="p-10 space-y-8">
                    <!-- Name -->
                    <div class="space-y-3">
                        <label for="name" class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">
                            Nombre de la Fase <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none transition-colors group-focus-within:text-sena">
                                <i class="fas fa-tag text-slate-300"></i>
                            </div>
                            <input type="text" name="name" id="name" required value="{{ old('name', $phase->name) }}"
                                class="w-full pl-12 pr-4 py-4 bg-slate-50/50 border border-slate-100 rounded-[1.5rem] text-slate-900 focus:outline-none focus:ring-4 focus:ring-sena/5 focus:bg-white transition-all font-bold text-sm"
                                placeholder="Ej: Fase 1 - 2024 (ADSO)">
                        </div>
                        @error('name')
                            <p class="mt-1 text-xs text-rose-600 font-bold uppercase tracking-widest ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Start Date -->
                        <div class="space-y-3">
                            <label for="start_date" class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">
                                Fecha de Inicio
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none transition-colors group-focus-within:text-sena">
                                    <i class="fas fa-calendar-alt text-slate-300"></i>
                                </div>
                                <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $phase->start_date ? $phase->start_date->format('Y-m-d') : '') }}"
                                    class="w-full pl-12 pr-4 py-4 bg-slate-50/50 border border-slate-100 rounded-[1.5rem] text-slate-900 focus:outline-none focus:ring-4 focus:ring-sena/5 focus:bg-white transition-all font-bold text-sm">
                            </div>
                            @error('start_date')
                                <p class="mt-1 text-xs text-rose-600 font-bold uppercase tracking-widest ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- End Date -->
                        <div class="space-y-3">
                            <label for="end_date" class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">
                                Fecha de Finalización
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none transition-colors group-focus-within:text-sena">
                                    <i class="fas fa-calendar-check text-slate-300"></i>
                                </div>
                                <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $phase->end_date ? $phase->end_date->format('Y-m-d') : '') }}"
                                    class="w-full pl-12 pr-4 py-4 bg-slate-50/50 border border-slate-100 rounded-[1.5rem] text-slate-900 focus:outline-none focus:ring-4 focus:ring-sena/5 focus:bg-white transition-all font-bold text-sm">
                            </div>
                            @error('end_date')
                                <p class="mt-1 text-xs text-rose-600 font-bold uppercase tracking-widest ml-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Technologists Section -->
                    <div class="pt-6 border-t border-slate-100">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Tecnólogos Vinculados</h3>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Gestione los programas que pertenecen a esta fase.</p>
                            </div>
                            <button type="button" onclick="addTechnologist()" 
                                    class="inline-flex items-center px-4 py-2 bg-sena/10 hover:bg-sena/20 text-sena font-black rounded-xl transition-all duration-200 uppercase tracking-widest text-[9px] gap-2">
                                <i class="fas fa-plus"></i>
                                Añadir Tecnólogo
                            </button>
                        </div>

                        <div id="technologists-container" class="space-y-4">
                            @foreach($phase->technologists as $index => $tech)
                            <div class="technologist-row bg-slate-50/50 p-6 rounded-[1.5rem] border border-slate-100 relative group">
                                <input type="hidden" name="technologists[{{ $index }}][id]" value="{{ $tech->id }}">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Nombre (ADSO, PAE, etc.)</label>
                                        <input type="text" name="technologists[{{ $index }}][name]" required value="{{ $tech->name }}"
                                            class="w-full px-5 py-3 bg-white border border-slate-100 rounded-xl text-slate-900 focus:outline-none focus:ring-4 focus:ring-sena/5 transition-all font-bold text-sm"
                                            placeholder="Nombre del programa">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Fecha Fin Específica</label>
                                        <input type="date" name="technologists[{{ $index }}][end_date]" value="{{ $tech->end_date ? $tech->end_date->format('Y-m-d') : '' }}"
                                            class="w-full px-5 py-3 bg-white border border-slate-100 rounded-xl text-slate-900 focus:outline-none focus:ring-4 focus:ring-sena/5 transition-all font-bold text-sm">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            
                            @if($phase->technologists->isEmpty())
                            <div class="technologist-row bg-slate-50/50 p-6 rounded-[1.5rem] border border-slate-100 relative group">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Nombre (ADSO, PAE, etc.)</label>
                                        <input type="text" name="technologists[0][name]"
                                            class="w-full px-5 py-3 bg-white border border-slate-100 rounded-xl text-slate-900 focus:outline-none focus:ring-4 focus:ring-sena/5 transition-all font-bold text-sm"
                                            placeholder="Nombre del programa">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Fecha Fin Específica</label>
                                        <input type="date" name="technologists[0][end_date]"
                                            class="w-full px-5 py-3 bg-white border border-slate-100 rounded-xl text-slate-900 focus:outline-none focus:ring-4 focus:ring-sena/5 transition-all font-bold text-sm">
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="space-y-3">
                        <label for="status" class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">
                            Estado <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none transition-colors group-focus-within:text-sena">
                                <i class="fas fa-info-circle text-slate-300"></i>
                            </div>
                            <select name="status" id="status" required
                                class="w-full pl-12 pr-4 py-4 bg-slate-50/50 border border-slate-100 rounded-[1.5rem] text-slate-900 focus:outline-none focus:ring-4 focus:ring-sena/5 focus:bg-white transition-all font-bold text-sm appearance-none cursor-pointer">
                                <option value="abierta" {{ old('status', $phase->status) == 'abierta' ? 'selected' : '' }}>Abierta (Permite registros)</option>
                                <option value="cerrada" {{ old('status', $phase->status) == 'cerrada' ? 'selected' : '' }}>Cerrada (Solo lectura)</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-5 flex items-center pointer-events-none">
                                <i class="fas fa-chevron-down text-slate-300 text-xs"></i>
                            </div>
                        </div>
                        @error('status')
                            <p class="mt-1 text-xs text-rose-600 font-bold uppercase tracking-widest ml-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Footer / Action -->
                <div class="p-10 bg-slate-50/50 border-t border-slate-100 flex justify-end gap-4">
                    <a href="{{ route('admin.phases.index') }}" 
                       class="px-8 py-4 bg-white text-slate-400 font-black rounded-2xl hover:bg-slate-100 transition-all uppercase tracking-widest text-xs border border-slate-100">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-10 py-4 sena-gradient text-white font-black rounded-2xl shadow-lg shadow-sena/20 transition-all hover:scale-105 active:scale-95 uppercase tracking-widest text-xs">
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    let techCount = {{ $phase->technologists->count() || 1 }};

    function addTechnologist() {
        const container = document.getElementById('technologists-container');
        const newRow = document.createElement('div');
        newRow.className = 'technologist-row bg-slate-50/50 p-6 rounded-[1.5rem] border border-slate-100 relative group animate-fadeIn mt-4';
        newRow.innerHTML = `
            <button type="button" onclick="this.parentElement.remove()" 
                    class="absolute -top-3 -right-3 w-8 h-8 bg-rose-500 text-white rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-all opacity-0 group-hover:opacity-100">
                <i class="fas fa-times text-xs"></i>
            </button>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Nombre (ADSO, PAE, etc.)</label>
                    <input type="text" name="technologists[${techCount}][name]" required
                        class="w-full px-5 py-3 bg-white border border-slate-100 rounded-xl text-slate-900 focus:outline-none focus:ring-4 focus:ring-sena/5 transition-all font-bold text-sm"
                        placeholder="Nombre del programa">
                </div>
                <div class="space-y-2">
                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest ml-1">Fecha Fin Específica</label>
                    <input type="date" name="technologists[${techCount}][end_date]"
                        class="w-full px-5 py-3 bg-white border border-slate-100 rounded-xl text-slate-900 focus:outline-none focus:ring-4 focus:ring-sena/5 transition-all font-bold text-sm">
                </div>
            </div>
        `;
        container.appendChild(newRow);
        techCount++;
        updateTechnologistDateLimits();
    }

    function updateTechnologistDateLimits() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        
        const techDateInputs = document.querySelectorAll('input[name^="technologists"][name$="[end_date]"]');
        
        techDateInputs.forEach(input => {
            if (startDate) input.min = startDate;
            else input.removeAttribute('min');
            
            if (endDate) input.max = endDate;
            else input.removeAttribute('max');
            
            if (input.value) {
                if (startDate && input.value < startDate) input.value = startDate;
                if (endDate && input.value > endDate) input.value = endDate;
            }
        });
    }

    document.getElementById('start_date').addEventListener('change', updateTechnologistDateLimits);
    document.getElementById('end_date').addEventListener('change', updateTechnologistDateLimits);
    document.addEventListener('DOMContentLoaded', updateTechnologistDateLimits);
</script>
@endpush
