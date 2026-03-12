@extends('layouts.masteraprendiz')

@section('title', 'Sesión en Curso - Recuperación')
@section('page-title', 'Cronómetro de Jornada')

@section('breadcrumb')
    <span class="text-slate-400">/</span>
    <span class="text-slate-400 uppercase tracking-widest text-[10px]">Recuperaciones</span>
    <span class="text-slate-400 font-bold mx-2">/</span>
    <span class="text-slate-600 font-black uppercase tracking-widest text-[10px]">En Vivo</span>
@endsection

@section('content')
    @php
        $requiredMinutes = $session->recoveryRequest->hours_requested * 60;
        $scheduledEnd = Carbon\Carbon::parse($session->start_time)->addMinutes($requiredMinutes);
    @endphp

    <div class="max-w-5xl mx-auto space-y-10 animate-fade-in pb-12">

        {{-- Main Control Panel --}}
        <div class="bg-white rounded-[4rem] shadow-premium border border-slate-50 overflow-hidden relative group">

            {{-- Status Header --}}
            <div class="p-12 text-center relative z-10">
                <div class="mb-10 flex flex-col items-center">
                    <div class="relative">
                        <div class="absolute inset-0 bg-emerald-400 blur-2xl opacity-20 animate-pulse"></div>
                        <span
                            class="relative inline-flex items-center gap-3 px-6 py-2.5 bg-emerald-50 text-sena rounded-full text-[10px] font-black uppercase tracking-[0.3em] border border-emerald-100 shadow-sm">
                            <span class="w-2.5 h-2.5 bg-sena rounded-full animate-ping"></span>
                            CRONÓMETRO DE RECTIFICACIÓN
                        </span>
                    </div>
                </div>

                <div class="space-y-4 mb-16">
                    <h1 class="text-5xl font-black text-slate-800 font-outfit uppercase tracking-tighter leading-none">
                        MISIÓN <span class="text-indigo-600">CENTRAL</span></h1>
                    <div class="flex items-center justify-center gap-3">
                        <span class="text-[10px] font-black text-slate-300 uppercase tracking-[0.3em]">OBJETIVO
                            OPERATIVO:</span>
                        <span
                            class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-indigo-100 italic">
                            {{ number_format($session->recoveryRequest->hours_requested, 1) }} HORAS DE COMPENSACIÓN
                        </span>
                    </div>
                </div>

                {{-- Timer Grid --}}
                <div class="flex flex-wrap items-center justify-center gap-6 mb-16 px-4">
                    <div
                        class="timer-node bg-slate-900 shadow-2xl group/timer hover:scale-110 transition-transform duration-500">
                        <div
                            class="timer-overlay bg-indigo-500/5 absolute inset-0 rounded-[2.5rem] opacity-0 group-hover/timer:opacity-100">
                        </div>
                        <span id="hours" class="timer-val font-outfit text-white">00</span>
                        <span class="timer-label text-slate-400">Horas</span>
                    </div>
                    <div class="text-4xl font-black text-slate-100 animate-pulse">:</div>
                    <div
                        class="timer-node bg-slate-900 shadow-2xl group/timer hover:scale-110 transition-transform duration-500">
                        <div
                            class="timer-overlay bg-emerald-500/5 absolute inset-0 rounded-[2.5rem] opacity-0 group-hover/timer:opacity-100">
                        </div>
                        <span id="minutes" class="timer-val font-outfit text-white">00</span>
                        <span class="timer-label text-slate-400">Minutos</span>
                    </div>
                    <div class="text-4xl font-black text-slate-100 animate-pulse">:</div>
                    <div
                        class="timer-node border-4 border-indigo-500/10 group/timer hover:scale-110 transition-transform duration-500">
                        <span id="seconds" class="timer-val font-outfit text-indigo-500">00</span>
                        <span class="timer-label text-slate-300">Segundos</span>
                    </div>
                </div>

                {{-- Progress Area --}}
                <div class="max-w-2xl mx-auto space-y-4 mb-16">
                    <div class="flex justify-between items-end px-2">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-chart-line text-indigo-400"></i>
                            <span class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Porcentaje de
                                Cumplimiento</span>
                        </div>
                        <span id="progress-text"
                            class="text-2xl font-black font-outfit text-slate-800 leading-none">0%</span>
                    </div>
                    <div
                        class="relative h-6 bg-slate-100 rounded-full p-1 border border-slate-50 overflow-hidden shadow-inner flex items-center">
                        <div id="progress-bar"
                            class="h-full sena-gradient rounded-full transition-all duration-1000 relative shadow-lg shadow-emerald-500/20"
                            style="width: 0%">
                            <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                        </div>
                    </div>
                </div>

                {{-- Markers --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-3xl mx-auto mb-16">
                    <div class="bg-slate-50 rounded-[2.5rem] p-8 border border-slate-50 shadow-inner group/marker">
                        <span
                            class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] block mb-2 px-1">Check-in
                            Detectado</span>
                        <div class="flex items-center gap-4">
                            <i
                                class="fas fa-sign-in-alt text-emerald-500 text-xl group-hover/marker:scale-125 transition-transform"></i>
                            <span
                                class="text-3xl font-black text-slate-800 font-outfit uppercase tracking-tighter">{{ Carbon\Carbon::parse($session->start_time)->format('h:i') }}
                                <span
                                    class="text-sm font-medium text-slate-300 italic">{{ Carbon\Carbon::parse($session->start_time)->format('A') }}</span></span>
                        </div>
                    </div>
                    <div class="bg-rose-50 rounded-[2.5rem] p-8 border border-rose-100 group/marker">
                        <span
                            class="text-[10px] font-black text-rose-300 uppercase tracking-[0.3em] block mb-2 px-1">Check-out
                            Programado</span>
                        <div class="flex items-center gap-4">
                            <i
                                class="fas fa-sign-out-alt text-rose-500 text-xl group-hover/marker:scale-125 transition-transform"></i>
                            <span
                                class="text-3xl font-black text-rose-600 font-outfit uppercase tracking-tighter">{{ $scheduledEnd->format('h:i') }}
                                <span
                                    class="text-sm font-medium text-rose-300 italic">{{ $scheduledEnd->format('A') }}</span></span>
                        </div>
                    </div>
                </div>

                {{-- Final Actions --}}
                <div class="flex flex-col md:flex-row items-center justify-center gap-6">
                    <form id="endSessionForm" action="{{ route('apprentice.recovery.end', $session) }}" method="POST">
                        @csrf
                        <button type="button" onclick="confirmEndSession()"
                            class="px-12 py-5 bg-slate-900 text-white rounded-[2rem] font-black uppercase tracking-[0.2em] text-[11px] hover:bg-rose-500 hover:scale-[1.05] active:scale-95 transition-all shadow-2xl shadow-indigo-100 flex items-center gap-4 group/btn">
                            <i class="fas fa-stop-circle text-rose-400 group-hover/btn:rotate-90 transition-transform"></i>
                            Finalizar Transmisión de Tiempo
                        </button>
                    </form>
                    <a href="{{ route('apprentice.recovery.index') }}"
                        class="px-8 py-5 bg-white text-slate-400 border border-slate-100 rounded-[2rem] font-black uppercase tracking-[0.2em] text-[10px] hover:bg-slate-900 hover:text-white transition-all">
                        Pausar Monitoreo
                    </a>
                </div>
            </div>

            {{-- Background Decors --}}
            <div class="absolute -right-32 -bottom-32 w-80 h-80 bg-slate-50 rounded-full blur-3xl opacity-50 -z-10"></div>
            <div class="absolute -left-32 -top-32 w-80 h-80 bg-indigo-50 rounded-full blur-3xl opacity-50 -z-10"></div>
        </div>

        {{-- Enforcement Box --}}
        <div
            class="bg-slate-900 border border-slate-800 rounded-[3rem] p-10 flex flex-col md:flex-row items-center gap-8 text-white relative overflow-hidden group">
            <div class="absolute inset-0 bg-rose-500/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div
                class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center border border-white/10 shrink-0 text-rose-500 text-2xl group-hover:bg-rose-500 group-hover:text-white transition-all">
                <i class="fas fa-gavel"></i>
            </div>
            <div class="space-y-1">
                <h4 class="text-rose-500 font-black text-sm uppercase tracking-widest leading-none mb-2">Protocolo de
                    Cumplimiento Estricto</h4>
                <p class="text-slate-400 text-[11px] font-bold uppercase tracking-widest leading-relaxed opacity-70">
                    El sistema invalidará la sesión si se detecta inactividad o cierre prematuro antes de completar las
                    <span class="text-white font-black">{{ number_format($session->recoveryRequest->hours_requested, 1) }}
                        HORAS</span> pactadas inicialmente.
                </p>
            </div>
        </div>
    </div>

    <style>
        .timer-node {
            padding: 2.5rem 3rem;
            border-radius: 3rem;
            min-width: 180px;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .timer-val {
            font-size: 5rem;
            font-weight: 950;
            line-height: 1;
            letter-spacing: -4px;
        }

        .timer-label {
            font-size: 0.7rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.4em;
            margin-top: 1rem;
            opacity: 0.6;
        }

        @media (max-width: 640px) {
            .timer-node {
                min-width: 140px;
                padding: 1.5rem 1rem;
            }

            .timer-val {
                font-size: 3rem;
                letter-spacing: -2px;
            }
        }
    </style>

    @push('scripts')
        <script>
            // Timer Logic
            const startTime = new Date("{{ $session->start_time?->toIso8601String() ?? now() }}").getTime();
            const requiredMinutes = {{ $requiredMinutes }};
            const requiredMillis = requiredMinutes * 60 * 1000;

            function updateTimer() {
                const now = new Date().getTime();
                const distance = now - startTime;

                const hours = Math.floor(distance / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                document.getElementById('hours').innerText = hours.toString().padStart(2, '0');
                document.getElementById('minutes').innerText = minutes.toString().padStart(2, '0');
                document.getElementById('seconds').innerText = seconds.toString().padStart(2, '0');

                // Update Progress
                const progress = Math.min(100, (distance / requiredMillis) * 100);
                document.getElementById('progress-bar').style.width = progress + '%';
                document.getElementById('progress-text').innerText = Math.round(progress) + '%';

                // Finish session automatically if 100% reached? (Optional requested usually but here manual confirmed)
            }

            setInterval(updateTimer, 1000);
            updateTimer();

            function confirmEndSession() {
                const now = new Date().getTime();
                const distance = now - startTime;
                const progress = (distance / requiredMillis) * 100;

                if (progress < 99) {
                    Swal.fire({
                        title: '<span class="text-rose-600 font-outfit uppercase font-black">¡ADVERTENCIA DE AVANCE!</span>',
                        text: 'Aún no has completado la meta de tiempo establecida. Si finalizas ahora, el sistema podría reportar cumplimiento parcial.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'ENTIENDO, TERMINAR YA',
                        cancelButtonText: 'SEGUIR TRABAJANDO',
                        confirmButtonColor: '#F43F5E',
                        customClass: {
                            popup: 'rounded-[3.5rem] p-10 border-4 border-rose-50 shadow-2xl',
                            confirmButton: 'rounded-2xl px-8 py-4 font-black text-xs uppercase tracking-widest',
                            cancelButton: 'rounded-2xl px-8 py-4 font-black text-xs uppercase tracking-widest'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('endSessionForm').submit();
                        }
                    });
                } else {
                    Swal.fire({
                        title: '¿Confirmar Cierre de Misión?',
                        text: 'Has alcanzado la meta establecida. Se procederá a consolidar tus horas.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'SÍ, FINALIZAR',
                        cancelButtonText: 'REVISAR',
                        confirmButtonColor: '#312e81',
                        customClass: {
                            popup: 'rounded-[3.5rem] p-10 shadow-2xl',
                            confirmButton: 'rounded-2xl px-8 py-4 font-black text-xs uppercase tracking-widest',
                            cancelButton: 'rounded-2xl px-8 py-4 font-black text-xs uppercase tracking-widest'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('endSessionForm').submit();
                        }
                    });
                }
            }
        </script>
    @endpush
@endsection