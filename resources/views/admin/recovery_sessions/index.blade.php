@extends('layouts.master')

@section('title', 'Sesiones de Recuperación - SIAP Admin')
@section('page-title', 'Control de Sesiones Extraordinarias')

@section('breadcrumb')
    <li class="breadcrumb-item active">Sesiones de Recuperación</li>
@endsection

@section('content')
    <!-- Header -->
    <div class="mb-10">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-4xl font-extrabold text-slate-900 mb-2 font-outfit tracking-tight">Sesiones de Recuperación</h1>
                <p class="text-slate-500 font-medium font-outfit">Gestión y control de jornadas extraordinarias para la compensación de horas.</p>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-[2.5rem] shadow-premium border border-slate-100 overflow-hidden group mb-12">
        <div class="p-0 overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-center">ID</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Aprendiz</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-center">Fecha</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Horario</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Duración</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Estado</th>
                        <th class="px-8 py-5 text-right text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sessions as $session)
                        <tr class="group/item hover:bg-slate-50/80 transition-all duration-200">
                            <td class="px-8 py-6 text-center">
                                <span class="text-xs font-bold text-slate-300">#{{ str_pad($session->id, 5, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="flex-shrink-0 h-11 w-11">
                                        <div
                                            class="h-11 w-11 rounded-2xl sena-gradient flex items-center justify-center text-white font-bold text-base shadow-sena shadow-md group-hover/item:scale-110 group-hover/item:rotate-3 transition-transform overflow-hidden">
                                            @if($session->apprentice?->profile_photo_path)
                                                <img src="{{ Storage::url($session->apprentice->profile_photo_path) }}" class="w-full h-full object-cover">
                                            @else
                                                {{ strtoupper(substr($session->apprentice?->name ?? 'A', 0, 1)) }}
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 mb-0.5">{{ $session->apprentice?->name ?? 'Aprendiz desconocido' }}</p>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ $session->apprentice?->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <div class="text-sm font-bold text-slate-600">{{ $session->date->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="space-y-1.5">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[8px] font-black text-slate-300 uppercase tracking-tighter">PLAN:</span>
                                        <span class="text-[10px] font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md">
                                            {{ Carbon\Carbon::parse($session->scheduled_start_time)->format('H:i') }} - {{ Carbon\Carbon::parse($session->scheduled_end_time)->format('H:i') }}
                                        </span>
                                    </div>
                                    @if($session->start_time)
                                        <div class="flex items-center gap-2">
                                            <span class="text-[8px] font-black text-slate-300 uppercase tracking-tighter">REAL:</span>
                                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-100/50">
                                                {{ Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ $session->end_time ? Carbon\Carbon::parse($session->end_time)->format('H:i') : '--:--' }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-sm font-black font-outfit text-slate-700 bg-slate-50 px-3 py-1 rounded-lg border border-slate-100">@hmMinutes($session->duration_minutes)</span>
                            </td>
                            <td class="px-8 py-6">
                                @if($session->status === 'scheduled')
                                    <span class="inline-flex items-center px-4 py-1.5 bg-indigo-50 text-indigo-600 text-[10px] font-bold uppercase tracking-widest rounded-full border border-indigo-100">
                                        <i class="fas fa-calendar-alt mr-1.5 animate-pulse"></i>
                                        Programada
                                    </span>
                                @elseif($session->status === 'completed')
                                    <span class="inline-flex items-center px-4 py-1.5 bg-emerald-50 text-emerald-600 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-100">
                                        <i class="fas fa-check-double mr-1.5"></i>
                                        Completada
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-4 py-1.5 bg-slate-50 text-slate-400 text-[10px] font-bold uppercase tracking-widest rounded-full border border-slate-100">
                                        Cancelada
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <button class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-sena hover:text-white transition-all shadow-sm active:scale-95 flex items-center justify-center opacity-50 cursor-not-allowed" title="Próximamente">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 italic font-medium">No se encontraron
                                sesiones de recuperación para este periodo.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <script>
            function closeSessionManually(id) {
                Swal.fire({
                    title: 'Cerrar Sesión Manualmente',
                    text: 'Ingresa la duración real trabajada en minutos:',
                    input: 'number',
                    inputAttributes: {
                        min: 1,
                        step: 1
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Cerrar y Guardar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#39A900',
                    customClass: {
                        popup: 'rounded-3xl',
                        confirmButton: 'rounded-xl px-6 py-3 font-bold',
                        cancelButton: 'rounded-xl px-6 py-3 font-bold'
                    },
                    inputValidator: (value) => {
                        if (!value) {
                            return '¡Debes ingresar los minutos laborados!'
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/admin/recovery-sessions/${id}/close`;

                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                        const tokenInput = document.createElement('input');
                        tokenInput.type = 'hidden';
                        tokenInput.name = '_token';
                        tokenInput.value = csrfToken;

                        const durationInput = document.createElement('input');
                        durationInput.type = 'hidden';
                        durationInput.name = 'duration_minutes';
                        durationInput.value = result.value;

                        form.appendChild(tokenInput);
                        form.appendChild(durationInput);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        </script>
    @endpush
@endsection
