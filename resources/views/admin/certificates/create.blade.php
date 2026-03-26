@extends('layouts.master')

@section('title', 'Crear Certificado - SIAP Admin')
@section('page-title', 'Emitir Nuevo Certificado')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.certificates.index') }}">Certificados</a></li>
<li class="breadcrumb-item active">Emitir</li>
@endsection

@section('content')
<!-- Header Section -->
<div class="mb-10">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-4xl font-extrabold text-slate-900 mb-2 font-outfit tracking-tight uppercase">Emitir <span class="text-sena">Certificado</span></h1>
            <p class="text-slate-500 font-bold text-[11px] uppercase tracking-[0.2em] opacity-60">Genere un nuevo certificado de horas para un aprendiz elegible.</p>
        </div>
        <div>
            <a href="{{ route('admin.certificates.index') }}" 
               class="inline-flex items-center px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-2xl transition-all duration-200 uppercase tracking-widest text-[10px] gap-2">
                <i class="fas fa-arrow-left"></i>
                Volver al Listado
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Left Column - Form -->
    <div class="lg:col-span-2">
        <form id="certificate-form" action="{{ route('admin.certificates.store') }}" method="POST">
            @csrf
            
            <div class="bg-white rounded-[2.5rem] shadow-premium border border-slate-100 overflow-hidden">
                <!-- Form Section 1: Apprentice & Hours -->
                <div class="p-10 border-b border-slate-100 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full blur-3xl opacity-30 -mr-10 -mt-10"></div>
                    
                    <div class="flex items-center gap-4 mb-8 relative z-10">
                        <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 shadow-inner">
                            <i class="fas fa-user-graduate text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-slate-800 uppercase tracking-tight">Información del Colaborador</h2>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Seleccione un colaborador elegible y confirme sus horas.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
                        <!-- Collaborator Selection -->
                        <div class="space-y-3">
                            <label for="apprentice_id" class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">
                                Seleccionar Colaborador <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none transition-colors group-focus-within:text-sena">
                                    <i class="fas fa-search text-slate-300"></i>
                                </div>
                                <select name="apprentice_id" id="apprentice_id" required
                                        class="w-full pl-12 pr-4 py-4 bg-slate-50/50 border border-slate-100 rounded-[1.5rem] text-slate-900 focus:outline-none focus:ring-4 focus:ring-sena/5 focus:bg-white transition-all font-bold text-sm appearance-none cursor-pointer @error('apprentice_id') border-rose-300 focus:ring-rose-500 @enderror">
                                    <option value="">Seleccione un colaborador</option>
                                    @foreach($eligibleApprentices as $apprentice)
                                    <option value="{{ $apprentice->id }}" 
                                            {{ old('apprentice_id') == $apprentice->id ? 'selected' : '' }}
                                            data-hours="{{ $apprentice->hours_completed }}"
                                            data-email="{{ $apprentice->email }}"
                                            data-cohort="{{ $apprentice->apprenticeProfile?->cohort ?? 'N/A' }}">
                                        {{ $apprentice->full_name }}
                                    </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-5 flex items-center pointer-events-none">
                                    <i class="fas fa-chevron-down text-slate-300 text-xs"></i>
                                </div>
                            </div>
                            <p class="mt-2 text-[9px] text-slate-400 font-bold uppercase tracking-widest ml-1 italic opacity-60">Solo colaboradores que han cumplido con la Meta Fase del programa.</p>
                            @error('apprentice_id')
                                <p class="mt-1 text-xs text-rose-600 font-bold uppercase tracking-widest ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hours Completed -->
                        <div class="space-y-3">
                            <label for="hours_completed" class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">
                                Horas Completadas <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none transition-colors group-focus-within:text-sena">
                                    <i class="fas fa-clock text-slate-300"></i>
                                </div>
                                <input type="number" name="hours_completed" id="hours_completed" required step="0.5"
                                       class="w-full pl-12 pr-4 py-4 bg-slate-50/50 border border-slate-100 rounded-[1.5rem] text-slate-900 focus:outline-none focus:ring-4 focus:ring-sena/5 focus:bg-white transition-all font-bold text-sm @error('hours_completed') border-rose-300 focus:ring-rose-500 @enderror"
                                       value="{{ old('hours_completed') }}" placeholder="Ej: 120">
                            </div>
                            @error('hours_completed')
                                <p class="mt-1 text-xs text-rose-600 font-bold uppercase tracking-widest ml-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Section 2: Delivery -->
                <div class="p-10 bg-slate-50/50 border-b border-slate-100 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-full blur-3xl opacity-30 -mr-10 -mt-10"></div>
                    
                    <div class="flex items-center gap-4 mb-8 relative z-10">
                        <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-inner">
                            <i class="fas fa-paper-plane text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-black text-slate-800 uppercase tracking-tight">Opciones de Entrega</h2>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Configure cómo el colaborador recibirá su certificado.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
                        <!-- Email Delivery -->
                        <div class="space-y-3">
                            <label for="email_to" class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">
                                Correo de Notificación
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none transition-colors group-focus-within:text-sena">
                                    <i class="fas fa-envelope text-slate-300"></i>
                                </div>
                                <input type="email" name="email_to" id="email_to" 
                                       class="w-full pl-12 pr-4 py-4 bg-white border border-slate-100 rounded-[1.5rem] text-slate-900 focus:outline-none focus:ring-4 focus:ring-sena/5 focus:bg-white transition-all font-bold text-sm @error('email_to') border-rose-300 focus:ring-rose-500 @enderror"
                                       value="{{ old('email_to') }}" placeholder="correo@ejemplo.com">
                            </div>
                            @error('email_to')
                                <p class="mt-1 text-xs text-rose-600 font-bold uppercase tracking-widest ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Info Box -->
                        <div class="flex items-center p-6 bg-white rounded-[1.5rem] border border-slate-100 shadow-sm">
                            <div class="w-10 h-10 bg-blue-50 rounded-2xl flex items-center justify-center mr-4 flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-600"></i>
                            </div>
                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest leading-relaxed italic opacity-80">
                                El certificado se generará en formato PDF y podrá enviarse automáticamente si proporciona un correo válido.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Footer / Action -->
                <div class="p-10 flex justify-end gap-4 bg-white">
                    <a href="{{ route('admin.certificates.index') }}" 
                       class="px-8 py-4 bg-slate-50 text-slate-400 font-black rounded-2xl hover:bg-slate-100 transition-all uppercase tracking-widest text-xs border border-slate-100">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-10 py-4 sena-gradient text-white font-black rounded-2xl shadow-lg shadow-sena/20 transition-all hover:scale-105 active:scale-95 uppercase tracking-widest text-xs">
                        Emitir Certificado
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Right Column - Sidebar Context -->
    <div class="space-y-8">
        <!-- Live Preview / Selection Details -->
        <div id="apprentice-preview" class="bg-white rounded-[2.5rem] shadow-premium border border-slate-100 p-8 opacity-50 transition-all duration-500 overflow-hidden relative group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-slate-50 rounded-full blur-3xl opacity-50 -mr-10 -mt-10 group-hover:bg-blue-50 transition-colors"></div>
            
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-8 flex items-center gap-3 relative z-10">
                <i class="fas fa-eye text-xs text-slate-300"></i>
                Vista Previa de Selección
            </h3>
            
            <div id="preview-no-selection" class="text-center py-12 relative z-10">
                <div class="w-20 h-24 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-6 border border-slate-100 shadow-inner group-hover:scale-110 transition-transform">
                    <i class="fas fa-user-circle text-5xl text-slate-200"></i>
                </div>
                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest italic opacity-60">Seleccione un aprendiz para ver detalles</p>
            </div>

            <div id="preview-content" class="hidden space-y-6 relative z-10">
                <div class="flex items-center gap-5 mb-10">
                    <div id="preview-avatar" class="w-16 h-16 rounded-2xl sena-gradient flex items-center justify-center text-white text-2xl font-black shadow-lg shadow-sena/20 border border-white/20">
                        JD
                    </div>
                    <div class="overflow-hidden">
                        <p id="preview-name" class="text-lg font-black text-slate-800 leading-tight truncate">Juan Doe</p>
                        <p id="preview-email" class="text-[10px] text-slate-400 font-bold uppercase tracking-widest truncate opacity-70">juan@example.com</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between items-center p-4 bg-slate-50 rounded-2xl border border-slate-100 group/item hover:bg-white transition-colors">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Ficha / Cohorte</span>
                        <span id="preview-cohort" class="text-xs font-black text-slate-800 uppercase">ADSO-2024-1</span>
                    </div>
                    <div class="flex justify-between items-center p-4 bg-slate-50 rounded-2xl border border-slate-100 group/item hover:bg-white transition-colors">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Horas Registradas</span>
                        <span id="preview-hours" class="text-lg font-black text-sena tracking-tighter">120.5 hrs</span>
                    </div>
                    <div class="flex items-center gap-3 p-4 bg-emerald-50 rounded-2xl border border-emerald-100 shadow-sm animate-pulse-once">
                        <i class="fas fa-check-circle text-emerald-500"></i>
                        <span class="text-[9px] text-emerald-700 font-black uppercase tracking-widest">Elegible para Certificación</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Usage Info -->
        <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white shadow-2xl shadow-slate-200 relative overflow-hidden group">
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-sena/10 rounded-full blur-3xl opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
            
            <h3 class="text-[10px] font-black text-sena uppercase tracking-[0.2em] mb-6 flex items-center gap-3 relative z-10">
                <i class="fas fa-shield-alt text-xs"></i>
                Reglas de Emisión
            </h3>
            <ul class="space-y-5 relative z-10">
                <li class="flex gap-4">
                    <div class="w-1.5 h-1.5 bg-sena rounded-full mt-1.5 flex-shrink-0 shadow-sena shadow-md"></div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-relaxed">
                        Cumplimiento mínimo: <span class="text-white">Meta Fase (Fase Actual)</span>.
                    </p>
                </li>
                <li class="flex gap-4">
                    <div class="w-1.5 h-1.5 bg-sena rounded-full mt-1.5 flex-shrink-0 shadow-sena shadow-md"></div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-relaxed">
                        El estado será <span class="text-white">"Generado"</span> tras la creación del PDF.
                    </p>
                </li>
                <li class="flex gap-4">
                    <div class="w-1.5 h-1.5 bg-sena rounded-full mt-1.5 flex-shrink-0 shadow-sena shadow-md"></div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-relaxed">
                        Los correos se envían <span class="text-white">solo si</span> la dirección es válida.
                    </p>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const apprenticeSelect = $('#apprentice_id');
    const previewBox = $('#apprentice-preview');
    const noSelection = $('#preview-no-selection');
    const previewContent = $('#preview-content');

    apprenticeSelect.on('change', function() {
        const selected = $(this).find('option:selected');
        const id = $(this).val();

        if (id) {
            const hours = selected.data('hours');
            const email = selected.data('email');
            const cohort = selected.data('cohort');
            const name = selected.text().trim();

            // Update Preview
            $('#preview-name').text(name);
            $('#preview-email').text(email);
            $('#preview-cohort').text(cohort);
            $('#preview-hours').text(hours + ' hrs');
            $('#preview-avatar').text(name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase());
            $('#email_to').val(email);
            $('#hours_completed').val(hours);

            // Show Content
            previewBox.removeClass('opacity-50').addClass('shadow-2xl shadow-blue-500/10 scale-[1.02]');
            noSelection.addClass('hidden');
            previewContent.removeClass('hidden');
        } else {
            previewBox.addClass('opacity-50').removeClass('shadow-2xl shadow-blue-500/10 scale-[1.02]');
            noSelection.removeClass('hidden');
            previewContent.addClass('hidden');
            $('#email_to').val('');
            $('#hours_completed').val('');
        }
    });

    // Form Submission with SweetAlert2
    $('#certificate-form').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        const apprenticeName = $('#apprentice_id option:selected').text();

        Swal.fire({
            title: '¿Confirmar emisión?',
            text: `Está a punto de emitir un certificado para ${apprenticeName}.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#39A900',
            cancelButtonColor: '#64748B',
            confirmButtonText: 'Sí, emitirlo',
            cancelButtonText: 'Revisar',
            customClass: {
                popup: 'rounded-[2rem]',
                confirmButton: 'rounded-xl px-6 py-3 font-bold uppercase tracking-widest text-xs',
                cancelButton: 'rounded-xl px-6 py-3 font-bold uppercase tracking-widest text-xs'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush
