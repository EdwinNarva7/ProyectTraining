@extends('layouts.master')

@section('title', 'Crear Fase - SIAP Admin')
@section('page-title', 'Crear Nueva Fase')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.phases.index') }}">Fases</a></li>
    <li class="breadcrumb-item active">Crear</li>
@endsection

@section('content')
    <!-- Header Section -->
    <div class="mb-10">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-4xl font-extrabold text-slate-900 mb-2 font-outfit tracking-tight uppercase">Crear Nueva <span class="text-sena">Fase</span></h1>
                <p class="text-slate-500 font-bold text-[11px] uppercase tracking-[0.2em] opacity-60">Defina un nuevo grupo o cohorte de aprendices para el sistema.</p>
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
        <form action="{{ route('admin.phases.store') }}" method="POST">
            @csrf
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
                            <input type="text" name="name" id="name" required value="{{ old('name') }}"
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
                                <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
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
                                <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                                    class="w-full pl-12 pr-4 py-4 bg-slate-50/50 border border-slate-100 rounded-[1.5rem] text-slate-900 focus:outline-none focus:ring-4 focus:ring-sena/5 focus:bg-white transition-all font-bold text-sm">
                            </div>
                            @error('end_date')
                                <p class="mt-1 text-xs text-rose-600 font-bold uppercase tracking-widest ml-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="space-y-3">
                        <label for="status" class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">
                            Estado Inicial <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none transition-colors group-focus-within:text-sena">
                                <i class="fas fa-info-circle text-slate-300"></i>
                            </div>
                            <select name="status" id="status" required
                                class="w-full pl-12 pr-4 py-4 bg-slate-50/50 border border-slate-100 rounded-[1.5rem] text-slate-900 focus:outline-none focus:ring-4 focus:ring-sena/5 focus:bg-white transition-all font-bold text-sm appearance-none cursor-pointer">
                                <option value="abierta" {{ old('status') == 'abierta' ? 'selected' : '' }}>Abierta (Permite registros)</option>
                                <option value="cerrada" {{ old('status') == 'cerrada' ? 'selected' : '' }}>Cerrada (Solo lectura)</option>
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
                        Crear Fase
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
