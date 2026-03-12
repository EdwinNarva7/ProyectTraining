@extends('layouts.masteraprendiz')

@section('title', 'Mis Horas Pendientes')
@section('page-title', 'Módulo de Recuperación')

@section('breadcrumb')
    <span class="text-slate-400">/</span>
    <span class="text-slate-600 font-bold uppercase tracking-widest text-[10px]">Incumplimientos</span>
@endsection

@section('content')
    <div class="space-y-10 animate-fade-in pb-12">
        
        {{-- Header & Warning --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-4xl font-bold text-slate-800 tracking-tight font-outfit">
                    Mi Estado de <span class="text-rose-500">Cuenta</span>
                </h1>
                <p class="text-slate-500 mt-2 font-medium text-lg">Gestión de horas por recuperar e incumplimientos.</p>
            </div>
            
            <div class="flex items-center gap-4">
                <a href="{{ route('apprentice.penalties.requests') }}" 
                   class="px-6 py-3 bg-white border border-slate-100 rounded-2xl shadow-premium text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-900 hover:text-white transition-all group flex items-center gap-3">
                    <i class="fas fa-paper-plane group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
                    Ver Mis Solicitudes
                </a>
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-premium border border-slate-50 hover:shadow-xl transition-all group">
                <div class="flex items-center gap-5 mb-6">
                    <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center text-xl shadow-inner group-hover:bg-rose-500 group-hover:text-white transition-all">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Total Deuda</span>
                </div>
                <div class="text-5xl font-black text-slate-800 font-outfit tracking-tighter leading-none">
                    @hm($stats['total_penalty_hours'])
                </div>
                <p class="text-[9px] font-black text-rose-500 uppercase tracking-widest mt-4 opacity-60">Horas reportadas por instructores</p>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] shadow-premium border border-slate-50 hover:shadow-xl transition-all group">
                <div class="flex items-center gap-5 mb-6">
                    <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl shadow-inner group-hover:bg-amber-500 group-hover:text-white transition-all">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Por Recuperar</span>
                </div>
                <div class="text-5xl font-black text-slate-800 font-outfit tracking-tighter leading-none">
                    @hm($stats['pending_hours'])
                </div>
                <p class="text-[9px] font-black text-amber-500 uppercase tracking-widest mt-4 opacity-60">Saldo actual pendiente de pago</p>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] shadow-premium border border-slate-50 hover:shadow-xl transition-all group">
                <div class="flex items-center gap-5 mb-6">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-sena flex items-center justify-center text-xl shadow-inner group-hover:bg-sena group-hover:text-white transition-all">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Saldado</span>
                </div>
                <div class="text-5xl font-black text-slate-800 font-outfit tracking-tighter leading-none">
                    @hm($stats['completed_hours'])
                </div>
                <p class="text-[9px] font-black text-sena uppercase tracking-widest mt-4 opacity-60">Horas recuperadas exitosamente</p>
            </div>
        </div>

        {{-- Penalties List --}}
        <div class="space-y-8">
            <div class="flex items-center gap-4">
                <div class="w-2.5 h-6 bg-rose-500 rounded-full"></div>
                <h3 class="text-2xl font-bold text-slate-800 font-outfit tracking-tight">Registro de <span class="text-rose-500">Incumplimientos</span></h3>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                @forelse($penalties as $penalty)
                    <div class="bg-white p-10 rounded-[3.5rem] shadow-premium border border-slate-50 flex flex-col justify-between group/card hover:border-slate-200 transition-all relative overflow-hidden">
                        {{-- Background Accent --}}
                        <div class="absolute -right-8 -top-8 w-32 h-32 {{ $penalty->status === 'saldado' ? 'bg-emerald-50' : ($penalty->status === 'in_recovery' ? 'bg-blue-50' : 'bg-rose-50') }} rounded-full opacity-50 group-hover/card:scale-150 transition-transform duration-700"></div>

                        <div class="relative z-10 flex flex-col md:flex-row md:items-start justify-between gap-6 mb-10">
                            <div class="flex items-center gap-6">
                                <div class="w-16 h-16 rounded-[1.5rem] bg-slate-50 flex flex-col items-center justify-center text-slate-400 border border-slate-100 group-hover/card:bg-white transition-colors">
                                    <span class="text-[10px] font-black uppercase leading-none mb-0.5">{{ $penalty->date->translatedFormat('M') }}</span>
                                    <span class="text-xl font-black font-outfit leading-none text-slate-800">{{ $penalty->date->format('d') }}</span>
                                </div>
                                <div class="space-y-1">
                                    <h4 class="text-sm font-black text-slate-800 uppercase tracking-tighter">{{ $penalty->date->translatedFormat('l, d F Y') }}</h4>
                                    <div class="flex items-center gap-3">
                                        <span class="text-2xl font-black text-rose-500 font-outfit tracking-tighter">@hm($penalty->penalty_hours)</span>
                                        <span class="px-3 py-1 bg-rose-50 text-rose-600 rounded-lg text-[9px] font-black uppercase tracking-widest border border-rose-100">Deuda Generada</span>
                                    </div>
                                </div>
                            </div>

                            <div class="shrink-0">
                                @if($penalty->status === 'pending')
                                    <span class="px-5 py-2 bg-amber-50 text-amber-500 text-[10px] font-black uppercase tracking-[0.2em] rounded-full border border-amber-100 shadow-sm">Pendiente</span>
                                @elseif($penalty->status === 'in_recovery')
                                    <span class="px-5 py-2 bg-blue-50 text-blue-500 text-[10px] font-black uppercase tracking-[0.2em] rounded-full border border-blue-100 shadow-sm">En Proceso</span>
                                @else
                                    <span class="px-5 py-2 bg-emerald-50 text-sena text-[10px] font-black uppercase tracking-[0.2em] rounded-full border border-emerald-100 shadow-sm">Saldada</span>
                                @endif
                            </div>
                        </div>

                        <div class="relative z-10 flex items-center justify-between gap-6 mt-auto">
                            @if($penalty->status === 'pending')
                                <button onclick="openRecoveryForm({{ $penalty->id }}, {{ $penalty->penalty_hours }})"
                                    class="w-full py-5 bg-slate-900 text-white rounded-[2rem] font-black text-[11px] uppercase tracking-[0.2em] shadow-xl hover:bg-slate-800 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-3 group/btn">
                                    <i class="fas fa-plus-circle text-rose-400 group-hover/btn:rotate-90 transition-transform"></i>
                                    Iniciar Acción de Recuperación
                                </button>
                            @else
                                <div class="w-full flex items-center justify-center py-5 bg-slate-50 rounded-[2rem] border border-slate-100 border-dashed">
                                     <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.3em]">No requiere acciones adicionales</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-24 flex flex-col items-center justify-center text-center space-y-8 bg-white rounded-[4rem] border-4 border-dashed border-slate-50">
                        <div class="w-32 h-32 bg-emerald-50 text-sena rounded-[3.5rem] flex items-center justify-center shadow-inner border border-emerald-100 overflow-hidden relative">
                             <div class="absolute inset-0 bg-white/40 blur-xl scale-150 animate-pulse"></div>
                             <i class="fas fa-shield-heart text-5xl relative z-10"></i>
                        </div>
                        <div class="space-y-3">
                            <h3 class="text-3xl font-black text-slate-800 font-outfit uppercase tracking-tighter text-emerald-600">¡PAZ Y SALVO!</h3>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] max-w-sm mx-auto leading-relaxed">No tienes incumplimientos registrados. Sigue manteniendo este excelente compromiso.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-4 pagination-premium">
                {{ $penalties->links() }}
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
                            <div class="p-5 bg-amber-50 rounded-2xl border border-amber-100/50">
                                <div class="flex gap-4 items-center">
                                    <i class="fas fa-info-circle text-amber-500 text-xl"></i>
                                    <p class="text-[10px] text-amber-700 font-bold uppercase leading-relaxed tracking-tight">Recuerda que estas solicitudes están sujetas a la aprobación del instructor encargado y se deben realizar con anticipación.</p>
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

                        const selectedDate = new Date(date);
                        const day = selectedDate.getUTCDay();
                        if (day !== 0 && day !== 6) { Swal.showValidationMessage('Solo Sábados o Domingos disponibles'); return false; }

                        return { date, totalHours };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({ title: 'Procesando...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
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