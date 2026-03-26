@extends('layouts.master')

@section('title', 'Solicitudes de Recuperación - SIAP Admin')
@section('page-title', 'Bandeja de Solicitudes')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.penalties.index') }}">Penalizaciones</a></li>
    <li class="breadcrumb-item active">Solicitudes</li>
@endsection

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 mb-2 font-outfit">Solicitudes de Recuperación</h1>
                <p class="text-slate-500 text-sm">Gestiona y aprueba las peticiones de los aprendices para saldar horas
                    debidas</p>
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Colaborador</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-center">Fecha
                            Solicitada</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-center">Horas a
                            Recuperar</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Origen
                            (Penalización)</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Estado</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-right">Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 italic">
                    @forelse($requests as $req)
                        <tr class="hover:bg-slate-50/50 transition-colors not-italic">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-full sena-gradient flex items-center justify-center text-white font-black text-xs overflow-hidden">
                                        @if($req->apprentice?->profile_photo_path)
                                            <img src="{{ Storage::url($req->apprentice->profile_photo_path) }}" class="w-full h-full object-cover">
                                        @else
                                            {{ strtoupper(substr(($req->apprentice?->name ?? 'A'), 0, 1)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-slate-800">{{ $req->apprentice?->name ?? 'Colaborador sin nombre' }}</div>
                                        <div class="text-xs text-slate-400 font-medium">Enviada:
                                            {{ $req->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div
                                    class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-bold border border-indigo-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    {{ $req->requested_date->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-black text-slate-700">@hm($req->hours_requested)</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Día del
                                    Incumplimiento:</div>
                                <div
                                    class="text-xs font-bold text-slate-600 underline decoration-rose-200 decoration-2 underline-offset-4">
                                    {{ $req->penalty->date->format('d M, Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($req->status === 'pending')
                                    <span
                                        class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-amber-50 text-amber-600 border border-amber-100">Pendiente</span>
                                @elseif($req->status === 'approved')
                                    <span
                                        class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-50 text-emerald-600 border border-emerald-100">Aprobada</span>
                                @else
                                    <span
                                        class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-rose-50 text-rose-600 border border-rose-100">Rechazada</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($req->status === 'pending')
                                    <div class="flex items-center justify-end gap-2">
                                        <button onclick="handleRequest({{ $req->id }}, 'approve')"
                                            class="p-2 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-600 hover:text-white transition-all shadow-sm"
                                            title="Aprobar Solicitud">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                        <button onclick="handleRequest({{ $req->id }}, 'reject')"
                                            class="p-2 bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-600 hover:text-white transition-all shadow-sm"
                                            title="Rechazar Solicitud">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    <span
                                        class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic">Procesada</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">No hay solicitudes pendientes
                                de revisión.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <script>
            function handleRequest(id, action) {
                const title = action === 'approve' ? 'Programar Recuperación' : '¿Rechazar esta solicitud?';
                const confirmText = action === 'approve' ? 'Aprobar y Programar' : 'Sí, Rechazar';
                const confirmColor = action === 'approve' ? '#39A900' : '#F43F5E';
                const isReject = action === 'reject';
                const isApprove = action === 'approve';

                let html = '';
                if (isReject) {
                    html = '<div class="text-left"><label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Motivo del rechazo:</label></div>';
                } else {
                    html = `
                                <div class="text-left space-y-4">
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Hora de Inicio Programada</label>
                                        <input type="time" id="scheduled_start_time" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-sm focus:ring-2 focus:ring-sena outline-none" value="08:00">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Hora de Fin Programada</label>
                                        <input type="time" id="scheduled_end_time" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-sm focus:ring-2 focus:ring-sena outline-none" value="12:00">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Notas (Opcional)</label>
                                    </div>
                                </div>
                            `;
                }

                Swal.fire({
                    title: title,
                    html: html,
                    input: 'textarea',
                    inputPlaceholder: 'Escribe aquí...',
                    showCancelButton: true,
                    confirmButtonText: confirmText,
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: confirmColor,
                    customClass: {
                        popup: 'rounded-[32px]',
                        confirmButton: 'rounded-xl px-6 py-3 font-bold uppercase tracking-widest text-xs',
                        cancelButton: 'rounded-xl px-6 py-3 font-bold uppercase tracking-widest text-xs'
                    },
                    preConfirm: () => {
                        const notes = Swal.getInput().value;
                        if (isReject && !notes) {
                            Swal.showValidationMessage('¡Debes proporcionar un motivo para el rechazo!');
                            return false;
                        }

                        if (isApprove) {
                            const start = document.getElementById('scheduled_start_time').value;
                            const end = document.getElementById('scheduled_end_time').value;
                            if (!start || !end) {
                                Swal.showValidationMessage('Debes programar el horario de inicio y fin');
                                return false;
                            }
                            return { notes, start, end };
                        }

                        return { notes };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const url = action === 'approve'
                            ? `{{ url('admin/recovery-requests') }}/${id}/approve`
                            : `{{ url('admin/recovery-requests') }}/${id}/reject`;

                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = url;

                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                        const tokenInput = document.createElement('input');
                        tokenInput.type = 'hidden'; tokenInput.name = '_token'; tokenInput.value = csrfToken;
                        form.appendChild(tokenInput);

                        const noteInput = document.createElement('input');
                        noteInput.type = 'hidden'; noteInput.name = 'admin_notes'; noteInput.value = result.value.notes || '';
                        form.appendChild(noteInput);

                        if (isApprove) {
                            const startInput = document.createElement('input');
                            startInput.type = 'hidden'; startInput.name = 'scheduled_start_time'; startInput.value = result.value.start;
                            form.appendChild(startInput);

                            const endInput = document.createElement('input');
                            endInput.type = 'hidden'; endInput.name = 'scheduled_end_time'; endInput.value = result.value.end;
                            form.appendChild(endInput);
                        }

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        </script>
    @endpush
@endsection
