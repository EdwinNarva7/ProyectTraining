@extends('layouts.masteraprendiz')

@section('title', 'Mi Asistencia - SIEAP')
@section('page-title', 'Control de Asistencia')

@section('breadcrumb')
    <span class="text-slate-400">/</span>
    <span class="text-slate-600 font-bold uppercase tracking-widest text-[10px]">Marcación</span>
@endsection

@section('content')
    <div class="space-y-10 animate-fade-in pb-12">
        
        {{-- Header Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 py-2">
            <div>
                <h1 class="text-4xl font-bold text-slate-800 tracking-tight font-outfit">
                    Mi Jornada de <span class="text-sena">Formación</span>
                </h1>
                <p class="text-slate-500 mt-2 font-medium text-lg">Registro y monitoreo de tu cumplimiento diario.</p>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="px-5 py-3 bg-white border border-slate-100 rounded-2xl shadow-premium flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest leading-none mb-1">Fecha de Hoy</p>
                        <p class="text-xs font-black text-slate-600 uppercase">{{ now()->translatedFormat('d F, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            
            {{-- Main Action Panel --}}
            <div class="lg:col-span-12 xl:col-span-8 flex flex-col">
                <div class="bg-white rounded-[3.5rem] shadow-premium border border-slate-50 overflow-hidden flex-1 flex flex-col group min-h-[500px]">
                    <div class="px-10 py-8 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                        <div class="flex items-center gap-4">
                            <div class="w-2.5 h-2.5 rounded-full bg-sena animate-pulse shadow-[0_0_10px_rgba(57,169,0,0.5)]"></div>
                            <h3 class="text-xl font-bold text-slate-800 font-outfit tracking-tight text-left">Estado de <span class="text-sena">Marcación</span></h3>
                        </div>
                        @if($todaySchedule)
                            <div class="px-4 py-1.5 bg-white border border-slate-100 rounded-full text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] shadow-sm">
                                <i class="fas fa-clock text-blue-400 mr-2"></i> {{ $todaySchedule->start_time->format('H:i') }} — {{ $todaySchedule->end_time->format('H:i') }}
                            </div>
                        @endif
                    </div>

                    <div class="p-10 flex-1 flex flex-col items-center justify-center relative overflow-hidden text-center">
                        {{-- Background Ring Decoration --}}
                        <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none scale-150">
                             <i class="fas fa-fingerprint text-[40rem]"></i>
                        </div>

                        @if(!$todaySchedule && !$scheduledStart)
                            {{-- No Schedule State --}}
                            <div class="relative z-10 max-w-sm space-y-8 animate-slide-up">
                                <div class="w-24 h-24 bg-slate-50 rounded-[2.5rem] flex items-center justify-center text-slate-200 mx-auto shadow-inner border border-slate-100">
                                    <i class="fas fa-ban text-4xl"></i>
                                </div>
                                <div class="space-y-4">
                                    <h2 class="text-3xl font-black text-slate-800 font-outfit uppercase tracking-tighter">Sin Programación</h2>
                                    <p class="text-slate-400 font-bold text-sm leading-relaxed uppercase tracking-widest px-8">No tienes un horario asignado para el día de hoy.</p>
                                </div>
                            </div>
                        @elseif($currentStatus['status'] === 'active')
                            {{-- Active Session State --}}
                            <div class="relative z-10 w-full max-w-lg space-y-12 animate-fade-in" 
                                 data-start-time="{{ $currentStatus['start_time']->timestamp }}"
                                 data-scheduled-end="{{ $scheduledEnd->timestamp }}">
                                
                                <div class="relative inline-block">
                                    <div class="absolute inset-0 bg-sena/10 blur-3xl rounded-full scale-150 animate-pulse"></div>
                                    <div class="relative w-48 h-48 rounded-[3.5rem] bg-white border-4 border-slate-50 flex items-center justify-center text-sena shadow-2xl ring-8 ring-sena/5">
                                        <i class="fas fa-hourglass-half text-6xl animate-spin-slow"></i>
                                    </div>
                                    <div class="absolute -bottom-4 left-1/2 -translate-x-1/2 px-6 py-2 bg-sena text-white rounded-full text-[10px] font-black uppercase tracking-[0.3em] shadow-lg shadow-sena/30">
                                        EN CURSO
                                    </div>
                                </div>

                                <div class="space-y-4 px-10">
                                    <h2 class="text-5xl font-black text-slate-800 font-outfit uppercase tracking-tighter leading-none">SESIÓN ACTIVA</h2>
                                    <p class="text-slate-400 font-bold text-xs uppercase tracking-[0.4em]">Iniciada a las {{ $currentStatus['start_time']->format('H:i:s A') }}</p>
                                </div>

                                <div class="grid grid-cols-2 gap-6 max-w-md mx-auto">
                                    <div class="bg-slate-50 rounded-3xl p-6 border border-slate-100 shadow-inner">
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Transcurrido</p>
                                        <p class="text-3xl font-black text-sena font-outfit tracking-tighter" id="live_duration">--h --m</p>
                                    </div>
                                    <div class="bg-slate-50 rounded-3xl p-6 border border-slate-100 shadow-inner">
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Hora Actual</p>
                                        <p class="text-3xl font-black text-slate-800 font-outfit tracking-tighter" id="live_clock">--:--:--</p>
                                    </div>
                                </div>

                                <button onclick="registerExit()"
                                    class="w-full py-6 bg-slate-900 text-white rounded-[3rem] font-black text-lg uppercase tracking-widest shadow-2xl hover:bg-slate-800 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-4 group">
                                    <i class="fas fa-sign-out-alt text-rose-500 group-hover:-translate-x-1 transition-transform"></i>
                                    REGISTRAR SALIDA
                                </button>
                            </div>
                        @elseif($currentStatus['status'] === 'completed')
                            {{-- Completed State --}}
                            <div class="relative z-10 max-w-sm space-y-10 animate-slide-up text-center">
                                <div class="w-32 h-32 rounded-[3rem] sena-gradient text-white flex items-center justify-center mx-auto shadow-2xl ring-8 ring-sena/10">
                                    <i class="fas fa-check-circle text-5xl"></i>
                                </div>
                                <div class="space-y-4">
                                    <h2 class="text-4xl font-black text-slate-800 font-outfit uppercase tracking-tighter">TRABAJO LISTO</h2>
                                    <p class="text-slate-400 font-bold text-sm uppercase tracking-widest italic max-w-xs mx-auto leading-relaxed">Has completado tu registro institucional por el día de hoy.</p>
                                </div>
                                <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 grid grid-cols-2 gap-8 shadow-inner">
                                    <div class="text-center">
                                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-1">Entrada</p>
                                        <p class="text-base font-black text-slate-600 uppercase">{{ $currentStatus['entry_time'] ? $currentStatus['entry_time']->format('H:i') : '--:--' }}</p>
                                    </div>
                                    <div class="text-center border-l border-slate-200">
                                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-1">Salida</p>
                                        <p class="text-base font-black text-slate-600 uppercase">{{ $currentStatus['end_time']->format('H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        @else
                            {{-- Idle / Punch-In State --}}
                            @php
                                $now = now();
                                $scheduledStart = \Carbon\Carbon::today()->setTimeFrom($todaySchedule->start_time);
                                $scheduledEnd = \Carbon\Carbon::today()->setTimeFrom($todaySchedule->end_time);
                                $isWithinTime = $now->between($scheduledStart, $scheduledEnd);
                                $isLate = $now->gt($scheduledStart->copy()->addMinutes(10)) && $isWithinTime;
                            @endphp

                            <div class="relative z-10 w-full max-w-lg space-y-12 animate-fade-in">
                                <div class="relative inline-block">
                                    <div class="absolute inset-0 {{ $isWithinTime ? 'bg-sena/10' : 'bg-slate-100' }} blur-3xl rounded-full scale-150 animate-pulse"></div>
                                    <div class="relative w-48 h-48 rounded-[4rem] bg-white border-4 border-slate-50 flex items-center justify-center {{ $isWithinTime ? 'text-sena' : 'text-slate-300' }} shadow-2xl ring-8 {{ $isWithinTime ? 'ring-sena/5' : 'ring-slate-50' }} transition-all duration-700">
                                        <i class="fas fa-power-off text-6xl"></i>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <h2 class="text-5xl font-black text-slate-800 font-outfit uppercase tracking-tighter leading-none">
                                        {{ $isWithinTime ? 'INICIAR AHORA' : 'FUERA DEL TURNO' }}
                                    </h2>
                                    <div class="flex flex-col items-center gap-2">
                                        @if($now->lt($scheduledStart))
                                            <p class="text-slate-400 font-bold text-xs uppercase tracking-[0.3em]">Tu jornada empieza a las {{ $scheduledStart->format('H:i A') }}</p>
                                        @elseif($isLate)
                                            <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-rose-50 text-rose-500 rounded-full text-[10px] font-black uppercase tracking-widest border border-rose-100 shadow-sm animate-pulse">
                                                <i class="fas fa-clock"></i> Ingreso retrasado detectado
                                            </span>
                                        @elseif($isWithinTime)
                                            <p class="text-slate-400 font-bold text-xs uppercase tracking-[0.3em]">El sistema está listo para recibir tu marcación</p>
                                        @else
                                            <p class="text-slate-400 font-bold text-xs uppercase tracking-[0.3em]">No hay sesiones disponibles en este momento</p>
                                        @endif
                                    </div>
                                </div>

                                <button onclick="registerEntry()" @if(!$isWithinTime) disabled @endif
                                    class="w-full py-6 {{ $isWithinTime ? 'sena-gradient shadow-sena/30' : 'bg-slate-200 cursor-not-allowed text-slate-400' }} text-white rounded-[3rem] font-black text-lg uppercase tracking-[0.3em] shadow-2xl hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-4 group relative overflow-hidden">
                                    @if($isWithinTime)
                                        <div class="absolute inset-0 bg-white/20 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                                    @endif
                                    <i class="fas fa-fingerprint text-2xl"></i>
                                    CONFIRMAR ENTRADA
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right Sidebar Stats --}}
            <div class="lg:col-span-12 xl:col-span-4 space-y-10">
                
                {{-- Circular Progress Card --}}
                <div class="bg-white rounded-[3.5rem] shadow-premium border border-slate-50 p-10 flex flex-col items-center text-center space-y-8 group">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.3em] font-outfit">Cumplimiento Diario</h3>
                    
                    @if($todaySchedule)
                        <div class="relative w-48 h-48 flex items-center justify-center">
                            {{-- SVG Progress Ring --}}
                            <svg class="w-full h-full -rotate-90">
                                <circle cx="96" cy="96" r="88" fill="none" stroke="currentColor" stroke-width="12" class="text-slate-50" />
                                <circle cx="96" cy="96" r="88" fill="none" stroke="currentColor" stroke-width="12" 
                                    class="{{ $progressPercent >= 100 ? 'text-sena' : 'text-blue-500' }} transition-all duration-[1500ms] ease-out-expo" 
                                    stroke-dasharray="552.92" 
                                    stroke-dashoffset="{{ 552.92 - (552.92 * $progressPercent / 100) }}"
                                    stroke-linecap="round" />
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center space-y-1">
                                <span class="text-4xl font-black text-slate-800 font-outfit tracking-tighter">{{ round($progressPercent) }}<span class="text-lg opacity-30">%</span></span>
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest italic">Avance Hoy</span>
                            </div>
                        </div>

                        <div class="w-full space-y-4">
                            <div class="flex items-center justify-between p-5 bg-slate-50 rounded-3xl border border-slate-50 shadow-inner group-hover:bg-white transition-colors">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Registrado</span>
                                <span class="text-sm font-black text-slate-700 font-outfit uppercase tracking-tight">{{ intdiv($attendedMinutes, 60) }}h {{ $attendedMinutes % 60 }}m</span>
                            </div>
                            <div class="flex items-center justify-between p-5 bg-slate-50 rounded-3xl border border-slate-50 shadow-inner group-hover:bg-white transition-colors">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pendiente</span>
                                <span class="text-sm font-black text-slate-700 font-outfit uppercase tracking-tight">{{ max(0, intdiv($scheduledMinutes - $attendedMinutes, 60)) }}h {{ max(0, ($scheduledMinutes - $attendedMinutes) % 60) }}m</span>
                            </div>
                        </div>
                    @else
                        <div class="py-12 space-y-6">
                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-200 mx-auto border border-slate-100">
                                <i class="fas fa-chart-pie text-3xl"></i>
                            </div>
                            <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Sin datos de progreso hoy</p>
                        </div>
                    @endif
                </div>

                {{-- Summary Grid --}}
                <div class="grid grid-cols-2 gap-6">
                    <div class="bg-white p-8 rounded-[2.5rem] shadow-premium border border-slate-50 space-y-4 group hover:bg-slate-900 transition-colors duration-500">
                        <div class="w-12 h-12 bg-indigo-50 text-indigo-500 rounded-2xl flex items-center justify-center group-hover:bg-white/10 group-hover:text-indigo-400 transition-all">
                            <i class="fas fa-history text-lg"></i>
                        </div>
                        <div>
                            <p class="text-3xl font-black text-slate-800 font-outfit tracking-tighter group-hover:text-white">{{ number_format($totalHours, 1) }}</p>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest group-hover:text-white/40 leading-tight">Horas Totales</p>
                        </div>
                    </div>
                    <div class="bg-white p-8 rounded-[2.5rem] shadow-premium border border-slate-50 space-y-4 group hover:bg-slate-900 transition-colors duration-500">
                        <div class="w-12 h-12 bg-emerald-50 text-sena rounded-2xl flex items-center justify-center group-hover:bg-white/10 group-hover:text-sena transition-all">
                            <i class="fas fa-shield-check text-lg"></i>
                        </div>
                        <div>
                            <p class="text-3xl font-black text-slate-800 font-outfit tracking-tighter group-hover:text-white">{{ $completedSessions }}</p>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest group-hover:text-white/40 leading-tight">Sesiones OK</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let durationInterval;
        let autoExitTriggered = false;

        function startDurationUpdate() {
            durationInterval = setInterval(() => {
                const activeContainer = document.querySelector('[data-start-time]');
                if (activeContainer) {
                    const startTimestamp = parseInt(activeContainer.getAttribute('data-start-time')) * 1000;
                    const scheduledEndTimestamp = parseInt(activeContainer.getAttribute('data-scheduled-end')) * 1000;
                    const startTime = new Date(startTimestamp);
                    const scheduledEnd = new Date(scheduledEndTimestamp);
                    const now = new Date();

                    const effectiveEnd = now > scheduledEnd ? scheduledEnd : now;
                    const diffMs = Math.max(0, effectiveEnd - startTime);
                    const diffMins = Math.floor(diffMs / 60000);
                    const hours = Math.floor(diffMins / 60);
                    const minutes = diffMins % 60;

                    const durationText = document.getElementById('live_duration');
                    if (durationText) durationText.textContent = `${hours}h ${minutes}m`;

                    const liveClock = document.getElementById('live_clock');
                    if (liveClock) liveClock.textContent = now.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

                    if (!autoExitTriggered && now >= scheduledEnd) {
                        autoExitTriggered = true;
                        autoRegisterExit();
                    }
                }
            }, 1000);
        }

        function registerEntry() {
            Swal.fire({
                title: '<span class="font-outfit font-black uppercase text-2xl tracking-tight">¿Confirmar Entrada?</span>',
                html: '<p class="text-slate-500 font-medium">Estás a punto de registrar el inicio de tu jornada de formación.</p>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#39A900',
                cancelButtonColor: '#94A3B8',
                confirmButtonText: 'SÍ, INICIAR',
                cancelButtonText: 'DESCONECTAR',
                customClass: { 
                    popup: 'rounded-[3.5rem] p-10 shadow-2xl border-2 border-slate-50',
                    confirmButton: 'rounded-2xl px-10 py-4 font-black text-xs uppercase tracking-widest'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Registrando...', didOpen: () => { Swal.showLoading(); } });
                    fetch('{{ route("apprentice.attendance.entry") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({ title: '¡Entrada Exitosa!', icon: 'success' }).then(() => location.reload());
                        } else {
                            Swal.fire({ title: 'Error', text: data.message, icon: 'error' });
                        }
                    });
                }
            });
        }

        function registerExit() {
            Swal.fire({
                title: '<span class="font-outfit font-black uppercase text-2xl tracking-tight text-rose-600">¿Cerrar Jornada?</span>',
                html: '<p class="text-slate-500 font-medium">Esto marcará tu salida definitiva por hoy.</p>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f43f5e',
                confirmButtonText: 'SÍ, CERRAR',
                cancelButtonText: 'VOLVER',
                customClass: { 
                    popup: 'rounded-[3.5rem] p-10 shadow-2xl border-2 border-rose-50',
                    confirmButton: 'rounded-2xl px-10 py-4 font-black text-xs uppercase tracking-widest'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Cerrando...', didOpen: () => { Swal.showLoading(); } });
                    fetch('{{ route("apprentice.attendance.exit") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({ title: '¡Jornada Cerrada!', icon: 'success' }).then(() => location.reload());
                        } else {
                            Swal.fire({ title: 'Error', text: data.message, icon: 'error' });
                        }
                    });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (document.querySelector('[data-start-time]')) startDurationUpdate();
        });
    </script>
@endpush
