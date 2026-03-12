@extends('layouts.masteraprendiz')

@section('title', 'Detalle de Penalización')
@section('page-title', 'Expediente de Incumplimiento')

@section('breadcrumb')
    <span class="text-slate-400">/</span>
    <span class="text-slate-400 uppercase tracking-widest text-[10px]">Incumplimientos</span>
    <span class="text-slate-400 font-bold mx-2">/</span>
    <span class="text-slate-600 font-black uppercase tracking-widest text-[10px]">Detalle</span>
@endsection

@section('content')
    <div class="space-y-10 animate-fade-in pb-12">
        
        {{-- Quick Back --}}
        <div>
            <a href="{{ route('apprentice.penalties.index') }}" 
               class="inline-flex items-center gap-3 px-6 py-3 bg-white border border-slate-100 rounded-2xl shadow-premium text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 transition-all group active:scale-95">
                <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                Listado General
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            
            {{-- Left Side: Main Info --}}
            <div class="lg:col-span-12 xl:col-span-4 space-y-10">
                
                {{-- Debt Card --}}
                <div class="bg-white p-10 rounded-[3.5rem] shadow-premium border border-slate-50 relative overflow-hidden group">
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-rose-50 rounded-full blur-3xl opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
                    
                    <div class="relative z-10 space-y-8">
                        <div class="w-16 h-16 rounded-[1.5rem] bg-rose-500 text-white flex items-center justify-center text-2xl shadow-xl shadow-rose-100">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        
                        <div>
                            <h3 class="text-2xl font-black text-slate-800 font-outfit uppercase tracking-tight">RESUMEN DE <span class="text-rose-500">DEUDA</span></h3>
                            <p class="text-slate-400 text-[11px] font-bold uppercase tracking-widest mt-1">Registrado el {{ $penalty->date->translatedFormat('d M, Y') }}</p>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-5 bg-slate-50/50 rounded-3xl border border-slate-50 shadow-inner">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Monto Adeudado</span>
                                <span class="text-2xl font-black text-rose-500 font-outfit tracking-tighter leading-none">@hm($penalty->penalty_hours)</span>
                            </div>
                            <div class="flex items-center justify-between p-5 bg-slate-50/50 rounded-3xl border border-slate-50 shadow-inner">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Estado Actual</span>
                                @if($penalty->status === 'pending')
                                    <span class="px-3 py-1 bg-amber-50 text-amber-500 rounded-lg text-[9px] font-black uppercase tracking-widest border border-amber-100">Pendiente</span>
                                @elseif($penalty->status === 'in_recovery')
                                    <span class="px-3 py-1 bg-blue-50 text-blue-500 rounded-lg text-[9px] font-black uppercase tracking-widest border border-blue-100">En Trámite</span>
                                @else
                                    <span class="px-3 py-1 bg-emerald-50 text-sena rounded-lg text-[9px] font-black uppercase tracking-widest border border-emerald-100">Saldada</span>
                                @endif
                            </div>
                        </div>

                        @if($penalty->status === 'pending')
                            <button onclick="openRecoveryForm({{ $penalty->id }}, {{ $penalty->penalty_hours }})"
                                class="w-full py-5 bg-slate-900 text-white rounded-[2rem] font-black text-[11px] uppercase tracking-[0.2em] shadow-xl hover:bg-slate-800 transition-all flex items-center justify-center gap-3">
                                <i class="fas fa-redo-alt text-rose-400"></i> Solicitar Acción
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Schedule Context --}}
                @if($penalty->schedule)
                <div class="bg-indigo-900 p-10 rounded-[3.5rem] text-white shadow-2xl relative overflow-hidden group">
                    <div class="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="relative z-10 space-y-6">
                        <div class="flex items-center gap-4">
                            <i class="fas fa-calendar-alt text-indigo-300"></i>
                            <h4 class="text-sm font-black uppercase tracking-widest">Contexto Institucional</h4>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between items-end border-b border-white/10 pb-4">
                                <span class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40">Jornada</span>
                                <span class="text-sm font-black uppercase font-outfit tracking-tight">{{ $penalty->schedule->day_of_week }}</span>
                            </div>
                            <div class="flex justify-between items-end">
                                <span class="text-[10px] font-black uppercase tracking-[0.2em] opacity-40">Horario oficial</span>
                                <span class="text-sm font-black uppercase font-outfit tracking-tight">{{ $penalty->schedule->start_time->format('H:i') }} — {{ $penalty->schedule->end_time->format('H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Right Side: Request History --}}
            <div class="lg:col-span-12 xl:col-span-8 flex flex-col">
                <div class="bg-white rounded-[3.5rem] shadow-premium border border-slate-50 overflow-hidden flex-1 group">
                    <div class="px-10 py-8 border-b border-slate-50 bg-slate-50/30 flex items-center gap-4">
                        <div class="w-10 h-10 rounded-2xl bg-white shadow-sm flex items-center justify-center text-slate-600">
                            <i class="fas fa-history"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-800 font-outfit uppercase tracking-tight text-left">HISTORIAL DE <span class="text-indigo-600">PETICIONES</span></h3>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mt-1">Intentos de recuperación asociados</p>
                        </div>
                    </div>

                    <div class="p-10">
                        <div class="space-y-10 relative">
                            {{-- Vertical Timeline Line --}}
                            @if($penalty->recoveryRequests->count() > 0)
                                <div class="absolute left-6 top-6 bottom-6 w-0.5 bg-slate-100"></div>
                            @endif

                            @forelse($penalty->recoveryRequests as $req)
                                <div class="relative pl-16 group/node">
                                    {{-- Timeline Ball --}}
                                    <div class="absolute left-4 top-1 w-4 h-4 rounded-full border-4 border-white bg-slate-200 shadow-sm z-10 group-hover/node:bg-indigo-500 transition-colors"></div>

                                    <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 p-8 bg-slate-50/50 rounded-[2.5rem] border border-slate-50 shadow-inner group-hover/node:bg-white group-hover/node:shadow-premium transition-all">
                                        <div class="flex items-center gap-6">
                                            <div class="w-16 h-16 rounded-[1.5rem] bg-white text-indigo-500 flex flex-col items-center justify-center shadow-premium border border-slate-100">
                                                <span class="text-[9px] font-black uppercase leading-none mb-0.5 text-slate-400">{{ $req->requested_date->translatedFormat('M') }}</span>
                                                <span class="text-xl font-black font-outfit leading-none">{{ $req->requested_date->format('d') }}</span>
                                            </div>
                                            <div class="space-y-1">
                                                <div class="flex items-center gap-3">
                                                    <span class="text-sm font-black text-slate-800 uppercase tracking-tighter">@hm($req->hours_requested) solicitadas</span>
                                                    @if($req->status === 'pending')
                                                        <span class="px-3 py-0.5 bg-amber-100 text-amber-600 rounded-full text-[8px] font-black uppercase tracking-widest border border-amber-200">En revisión</span>
                                                    @elseif($req->status === 'approved')
                                                        <span class="px-3 py-0.5 bg-emerald-100 text-sena rounded-full text-[8px] font-black uppercase tracking-widest border border-emerald-200">Aprobada</span>
                                                    @else
                                                        <span class="px-3 py-0.5 bg-rose-100 text-rose-500 rounded-full text-[8px] font-black uppercase tracking-widest border border-rose-200">Rechazada</span>
                                                    @endif
                                                </div>
                                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Enviada el {{ $req->created_at->translatedFormat('d M, Y \a \l\a\s H:i') }}</p>
                                            </div>
                                        </div>

                                        @if($req->admin_notes)
                                            <div class="shrink-0 max-w-xs md:max-w-sm">
                                                <div class="relative pl-4 border-l-2 border-indigo-100 py-1">
                                                    <p class="text-[11px] font-bold text-slate-500 italic leading-relaxed">"{{ $req->admin_notes }}"</p>
                                                    <span class="text-[8px] font-black text-slate-300 uppercase tracking-widest mt-2 block">— {{ $req->admin->name ?? 'Sistema' }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="py-24 flex flex-col items-center justify-center text-center space-y-6">
                                    <div class="w-24 h-24 bg-slate-50 rounded-[2.5rem] flex items-center justify-center text-slate-200 border border-slate-50 shadow-inner">
                                        <i class="fas fa-folder-open text-3xl"></i>
                                    </div>
                                    <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.3em]">No se registran gestiones para esta deuda</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function openRecoveryForm(penaltyId, maxHours) {
                const initialHours = Math.floor(maxHours);
                const initialMinutes = Math.round((maxHours % 1) * 60);

                Swal.fire({
                    title: '<span class="font-outfit font-black uppercase text-2xl tracking-tight">Recuperación De Horas</span>',
                    html: `
                        <div class="text-left space-y-6 py-6 font-outfit">
                            <div class="bg-indigo-50/50 p-6 rounded-3xl border border-indigo-100">
                                <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1">Monto a Recuperar</p>
                                <p class="text-3xl font-black text-indigo-600">${initialHours}h ${initialMinutes}m</p>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Fecha Programada (Fines de Semana)</label>
                                    <input type="date" id="requested_date" class="w-full px-6 py-4 bg-white border-2 border-slate-100 rounded-2xl text-sm font-bold focus:border-indigo-500 outline-none transition-all shadow-sm">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Horas</label>
                                        <input type="number" id="hours" min="0" max="${Math.floor(maxHours + 0.1)}" value="${initialHours}" class="w-full px-6 py-4 bg-white border-2 border-slate-100 rounded-2xl text-sm font-bold focus:border-indigo-500 outline-none transition-all shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Minutos</label>
                                        <input type="number" id="minutes" min="0" max="59" value="${initialMinutes}" class="w-full px-6 py-4 bg-white border-2 border-slate-100 rounded-2xl text-sm font-bold focus:border-indigo-500 outline-none transition-all shadow-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'ENVIAR SOLICITUD',
                    cancelButtonText: 'CANCELAR',
                    confirmButtonColor: '#312e81',
                    customClass: {
                        popup: 'rounded-[3.5rem] p-10 shadow-2xl border-4 border-slate-50',
                        confirmButton: 'rounded-[1.5rem] px-10 py-5 font-black text-xs uppercase tracking-[0.2em] shadow-xl',
                        cancelButton: 'rounded-[1.5rem] px-10 py-5 font-black text-xs uppercase tracking-[0.2em] text-slate-400 bg-slate-100'
                    },
                    preConfirm: () => {
                        const date = document.getElementById('requested_date').value;
                        const h = parseInt(document.getElementById('hours').value) || 0;
                        const m = parseInt(document.getElementById('minutes').value) || 0;
                        const totalHours = h + (m / 60);

                        if (!date) { Swal.showValidationMessage('Selecciona una fecha válida'); return false; }
                        if (totalHours <= 0) { Swal.showValidationMessage('El tiempo debe ser mayor a 0'); return false; }
                        if (totalHours > maxHours + 0.05) { Swal.showValidationMessage(`Excediste el tope de ${initialHours}h ${initialMinutes}m`); return false; }

                        return { date, totalHours };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/aprendiz/penalties/${penaltyId}/request-recovery`;
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        
                        const inputs = {
                            '_token': csrfToken,
                            'requested_date': result.value.date,
                            'hours_requested': result.value.totalHours
                        };

                        Object.keys(inputs).forEach(key => {
                            const input = document.createElement('input');
                            input.type = 'hidden'; input.name = key; input.value = inputs[key];
                            form.appendChild(input);
                        });

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        </script>
    @endpush
@endsection
