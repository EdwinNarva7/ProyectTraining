@extends('layouts.master')

@section('title', 'Panel de Asistencia Biométrica - SIEAP')
@section('page-title', 'Panel de Huella Digital')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.attendance.index') }}">Asistencia</a></li>
    <li class="breadcrumb-item active text-sena font-bold">Marcación</li>
@endsection

@push('styles')
    <style>
        .scanner-glow {
            box-shadow: 0 0 50px rgba(57, 169, 0, 0.15);
        }

        @keyframes scan-line {
            0% {
                top: 0%;
                opacity: 0;
            }

            50% {
                opacity: 0.8;
            }

            100% {
                top: 100%;
                opacity: 0;
            }
        }

        .scan-line {
            position: absolute;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.8), transparent);
            box-shadow: 0 0 20px rgba(57, 169, 0, 1);
            animation: scan-line 2s infinite linear;
            z-index: 20;
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(0.8);
                opacity: 0.8;
            }

            100% {
                transform: scale(1.4);
                opacity: 0;
            }
        }

        .pulse-ring {
            position: absolute;
            inset: -20px;
            border: 2px solid rgba(57, 169, 0, 0.4);
            border-radius: 50%;
            animation: pulse-ring 2s infinite cubic-bezier(0.4, 0, 0.6, 1);
        }

        .activity-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .activity-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .activity-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        @keyframes slide-in-right {
            from {
                transform: translateX(30px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .animate-slide-in-right {
            animation: slide-in-right 0.4s ease-out;
        }
    </style>
@endpush

@section('content')
    <div class="space-y-8 animate-fade-in pb-12">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 py-2">
            <div>
                <h1 class="text-4xl font-black text-slate-800 tracking-tight font-outfit uppercase">
                    BIOMETRÍA <span class="text-sena font-black">ACTIVA</span>
                </h1>
                <p class="text-slate-500 mt-2 font-bold tracking-wide uppercase text-[11px] opacity-70">Control de
                    asistencia en tiempo real mediante huella digital</p>
            </div>

            <div class="flex items-center gap-4">
                <div class="px-5 py-3 bg-white border border-slate-100 rounded-2xl shadow-premium flex items-center gap-3">
                    <div id="bridge-dot"
                        class="w-3 h-3 bg-amber-400 rounded-full animate-pulse shadow-[0_0_10px_rgba(251,191,36,0.5)]">
                    </div>
                    <span id="bridge-status-text"
                        class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Verificando Lector...</span>
                </div>
                <a href="{{ route('admin.fingerprint.enroll') }}"
                    class="p-3 bg-slate-800 text-white rounded-2xl shadow-xl hover:bg-slate-900 transition-all active:scale-95 group">
                    <i class="fas fa-user-plus group-hover:scale-110 transition-transform"></i>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

            {{-- Panel del Escáner (Izquierda/Centro) --}}
            <div class="lg:col-span-12 xl:col-span-5 space-y-8">
                <div class="relative overflow-hidden group">
                    <div
                        class="p-12 md:p-16 sena-gradient rounded-[4rem] shadow-2xl relative z-10 text-center text-white border-2 border-white/20">
                        {{-- Background FX --}}
                        <div class="absolute inset-0 opacity-10 pointer-events-none overflow-hidden">
                            <svg class="w-full h-full" viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="45" fill="none" stroke="currentColor" stroke-width="0.2" />
                                <circle cx="50" cy="50" r="35" fill="none" stroke="currentColor" stroke-width="0.2" />
                                <circle cx="50" cy="50" r="25" fill="none" stroke="currentColor" stroke-width="0.2" />
                            </svg>
                        </div>

                        <h4 class="text-xs font-black tracking-[0.6em] mb-12 uppercase opacity-80">Sistema de Identificación
                        </h4>

                        {{-- Scanner Visual --}}
                        <div class="relative w-48 h-48 mx-auto mb-12 flex items-center justify-center">
                            <div class="absolute inset-0 border-4 border-white/20 rounded-full"></div>
                            <div
                                class="absolute inset-4 border-2 border-white/10 rounded-full border-dashed animate-spin-slow">
                            </div>

                            <div id="scanner-visual-box"
                                class="relative w-32 h-32 bg-white/20 backdrop-blur-xl rounded-[2.5rem] flex items-center justify-center shadow-2xl border border-white/30 overflow-hidden transition-all duration-500">
                                <i id="fp-icon"
                                    class="fas fa-fingerprint text-6xl text-white transition-all duration-500 group-hover:scale-110"></i>
                                <div id="scan-line" class="hidden scan-line"></div>
                                <div id="status-pulse" class="hidden pulse-ring"></div>
                            </div>
                        </div>

                        {{-- Status Display --}}
                        <div id="fp-status-banner"
                            class="bg-white/10 backdrop-blur-md border border-white/20 rounded-3xl py-6 px-8 mb-12 shadow-inner transition-all duration-500">
                            <div class="flex items-center justify-center gap-4">
                                <i id="fp-status-icon" class="fas fa-circle-notch text-2xl"></i>
                                <span id="fp-status-text" class="text-lg font-black font-outfit tracking-tight">ESPERANDO
                                    INICIO</span>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                            <button id="btn-start-scan" onclick="startScan()"
                                class="w-full sm:w-auto px-10 py-5 bg-white text-sena rounded-[2rem] font-black text-lg shadow-xl hover:shadow-white/20 hover:scale-105 active:scale-95 transition-all flex items-center justify-center gap-3">
                                <i class="fas fa-play"></i> INICIAR CAPTURA
                            </button>
                            <button id="btn-stop-scan" onclick="stopScan()"
                                class="hidden w-full sm:w-auto px-10 py-5 bg-red-500 text-white rounded-[2rem] font-black text-lg shadow-xl hover:bg-red-600 hover:scale-105 active:scale-95 transition-all flex items-center justify-center gap-3">
                                <i class="fas fa-stop"></i> DETENER
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Panel de Resultados y Log (Derecha) --}}
            <div class="lg:col-span-12 xl:col-span-7 space-y-8">

                {{-- Último Marcado Card --}}
                <div
                    class="bg-white rounded-[3.5rem] shadow-premium border border-slate-50 overflow-hidden min-h-[280px] flex flex-col group">
                    <div class="px-10 py-8 border-b border-slate-50 bg-slate-50/30 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-id-card text-sena text-xl"></i>
                            <h3 class="text-xl font-black text-slate-800 font-outfit uppercase tracking-tight">ÚLTIMO
                                REGISTRO</h3>
                        </div>
                        <span id="current-time-badge"
                            class="px-4 py-1.5 bg-white text-slate-400 font-black text-[10px] rounded-full border border-slate-100 shadow-sm leading-none">
                            {{ now()->format('H:i') }}
                        </span>
                    </div>

                    <div id="fp-result-area"
                        class="flex-grow flex items-center justify-center p-10 animate-fade-in text-center">
                        <div class="space-y-6 opacity-40 transition-opacity group-hover:opacity-60">
                            <div
                                class="w-20 h-20 bg-slate-100 rounded-[2rem] flex items-center justify-center mx-auto mb-4 border border-slate-200 shadow-inner">
                                <i class="fas fa-hand-pointer text-3xl text-slate-300"></i>
                            </div>
                            <p class="text-xs font-black text-slate-400 uppercase tracking-[0.4em]">Inicia el escáner para
                                ver resultados</p>
                        </div>
                    </div>
                </div>

                {{-- Historial del Día --}}
                <div class="bg-white rounded-[3.5rem] shadow-premium border border-slate-50 overflow-hidden group">
                    <div class="px-10 py-8 border-b border-slate-50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 group-hover:scale-110 transition-transform">
                                <i class="fas fa-history text-sm"></i>
                            </div>
                            <h3 class="text-xl font-black text-slate-800 font-outfit uppercase tracking-tight">ACTIVIDAD
                                <span class="text-sena">HOY</span></h3>
                        </div>
                        <span id="activity-count"
                            class="w-10 h-10 flex items-center justify-center bg-slate-900 text-white font-black text-xs rounded-2xl shadow-lg border-2 border-white ring-4 ring-slate-50/50">0</span>
                    </div>

                    <div class="p-8">
                        <div id="activity-log" class="space-y-4 activity-scrollbar max-h-[360px] overflow-y-auto pr-4">
                            <div id="no-activity-msg" class="py-12 text-center opacity-30">
                                <div
                                    class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 grayscale">
                                    <i class="fas fa-ghost text-2xl text-slate-300"></i>
                                </div>
                                <p class="text-[10px] font-black uppercase tracking-[0.4em]">Sin registros registrados hoy
                                </p>
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
        const BRIDGE_URL = 'http://localhost:8080';
        const POLL_MS = 1200;
        const MARK_URL = '{{ route("admin.fingerprint.mark") }}';
        const TEMPLATES_URL = '{{ route("admin.fingerprint.templates") }}';
        const CSRF_TOKEN = '{{ csrf_token() }}';

        let scanInterval = null;
        let bridgeOnline = false;
        let activityLog = [];
        let lastTemplate = null;
        let cooldownActive = false;

        // ─────────────────────────────────────────────────────────────────────────────
        async function checkBridgeStatus() {
            try {
                const res = await fetch(`${BRIDGE_URL}/status`, { signal: AbortSignal.timeout(1800) });
                const data = await res.json();
                setBridgeStatus(true, 'LECTOR CONECTADO');
            } catch {
                setBridgeStatus(false, 'BRIDGE DESCONECTADO');
            }
        }

        function setBridgeStatus(online, message) {
            const dot = document.getElementById('bridge-dot');
            const text = document.getElementById('bridge-status-text');
            bridgeOnline = online;

            if (online) {
                dot.className = 'w-3 h-3 bg-sena rounded-full shadow-[0_0_12px_rgba(57,169,0,0.6)] animate-pulse';
                text.className = 'text-[10px] font-black text-sena uppercase tracking-widest';
            } else {
                dot.className = 'w-3 h-3 bg-red-500 rounded-full shadow-[0_0_12px_rgba(239,68,68,0.4)]';
                text.className = 'text-[10px] font-black text-red-500 uppercase tracking-widest';
            }
            text.textContent = message;
        }

        // ─────────────────────────────────────────────────────────────────────────────
        function startScan() {
            if (!bridgeOnline) {
                setStatus('error', 'fa-exclamation-triangle', 'ERROR DE ENLACE');
                Swal.fire({
                    title: 'Bridge no Detectado',
                    text: 'Inicia el software de puente biométrico para continuar.',
                    icon: 'error',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#39A900',
                    customClass: { popup: 'rounded-[1.5rem]' }
                });
                return;
            }

            document.getElementById('btn-start-scan').classList.add('hidden');
            document.getElementById('btn-stop-scan').classList.remove('hidden');
            document.getElementById('scan-line').classList.remove('hidden');
            document.getElementById('status-pulse').classList.remove('hidden');
            document.getElementById('scanner-visual-box').classList.add('shadow-[0_0_40px_rgba(57,169,0,0.5)]', 'border-white/50');

            setStatus('scanning', 'fa-circle-notch fa-spin text-white', 'HUELLA DETECTADA...');
            scanInterval = setInterval(pollCapture, POLL_MS);
        }

        function stopScan() {
            clearInterval(scanInterval);
            scanInterval = null;
            document.getElementById('btn-start-scan').classList.remove('hidden');
            document.getElementById('btn-stop-scan').classList.add('hidden');
            document.getElementById('scan-line').classList.add('hidden');
            document.getElementById('status-pulse').classList.add('hidden');
            document.getElementById('scanner-visual-box').classList.remove('shadow-[0_0_40px_rgba(57,169,0,0.5)]', 'border-white/50');

            setStatus('idle', 'fa-power-off opacity-50', 'SISTEMA DETENIDO');
        }

        // ─────────────────────────────────────────────────────────────────────────────
        async function pollCapture() {
            if (cooldownActive) return;

            try {
                const res = await fetch(`${BRIDGE_URL}/capture`, { signal: AbortSignal.timeout(1800) });
                const data = await res.json();

                if (data.status === 'success' && data.template_base64) {
                    if (data.template_base64 === lastTemplate) return;
                    lastTemplate = data.template_base64;

                    setStatus('success', 'fa-hand-sparkles animate-bounce', 'LECTURA EXITOSA');
                    await identifyAndMark(data.template_base64);
                }
            } catch { }
        }

        // ─────────────────────────────────────────────────────────────────────────────
        async function identifyAndMark(capturedTemplate) {
            try {
                const tplRes = await fetch(TEMPLATES_URL);
                const tplData = await tplRes.json();

                if (!tplData.success || !tplData.templates.length) {
                    setStatus('error', 'fa-database', 'BASE DE DATOS VACÍA');
                    return;
                }

                const identifyRes = await fetch(`${BRIDGE_URL}/identify`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        captured_template: capturedTemplate,
                        enrolled_templates: tplData.templates
                    }),
                    signal: AbortSignal.timeout(6000)
                });
                const identifyData = await identifyRes.json();

                if (!identifyData.matched || !identifyData.apprentice_id) {
                    setStatus('error', 'fa-user-slash', 'SIN COINCIDENCIA');
                    showEmptyResult('HUELLA NO RECONOCIDA EN EL PADRÓN');
                    applyCooldown(3000);
                    return;
                }

                await registerMark(identifyData.apprentice_id);

            } catch (err) {
                setStatus('error', 'fa-wifi', 'FALLO DE SINCRONÍA');
            }
        }

        async function registerMark(apprenticeId) {
            try {
                const res = await fetch(MARK_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ apprentice_id: apprenticeId })
                });
                const data = await res.json();

                if (data.success) {
                    setStatus('success', 'fa-check-double text-white', 'ACCESO AUTORIZADO');
                    showResult(data);
                    addToLog(data);
                    applyCooldown(4000);
                } else {
                    setStatus('error', 'fa-ban', 'ACCESO DENEGADO');
                    showEmptyResult(data.message, true);
                    applyCooldown(3000);
                }
            } catch (err) {
                setStatus('error', 'fa-server', 'LINK CAÍDO');
            }
        }

        // ─────────────────────────────────────────────────────────────────────────────
        function setStatus(type, icon, message) {
            const banner = document.getElementById('fp-status-banner');
            const iconEl = document.getElementById('fp-status-icon');
            const textEl = document.getElementById('fp-status-text');

            iconEl.className = `fas ${icon}`;
            textEl.textContent = message;

            if (type === 'scanning') {
                banner.className = "bg-white/20 backdrop-blur-xl border border-white/40 rounded-3xl py-6 px-8 mb-12 shadow-inner transition-all duration-500 scale-105";
            } else if (type === 'success') {
                banner.className = "bg-green-500/30 backdrop-blur-xl border border-green-400/50 rounded-3xl py-6 px-8 mb-12 shadow-lg transition-all duration-500 scale-110";
            } else if (type === 'error') {
                banner.className = "bg-red-500/30 backdrop-blur-xl border border-red-400/50 rounded-3xl py-6 px-8 mb-12 shadow-lg transition-all duration-500";
            } else {
                banner.className = "bg-white/10 backdrop-blur-md border border-white/20 rounded-3xl py-6 px-8 mb-12 shadow-inner transition-all duration-500";
            }
        }

        function showResult(data) {
            const area = document.getElementById('fp-result-area');
            const isEntrada = data.event_type === 'entrada';
            const colorClass = isEntrada ? 'text-sena bg-green-50 border-green-100' : 'text-amber-600 bg-amber-50 border-amber-100';
            const icon = isEntrada ? 'fa-door-open' : 'fa-door-closed';

            area.innerHTML = `
                    <div class="w-full animate-zoom-in">
                        <div class="mb-6 flex justify-center">
                            <div class="px-6 py-2 rounded-full border ${colorClass} text-[10px] font-black uppercase tracking-[0.4em] shadow-sm flex items-center gap-2">
                                <i class="fas ${icon}"></i> Marcación Exitosa - ${data.event_type.toUpperCase()}
                            </div>
                        </div>
                        <div class="flex flex-col items-center gap-1">
                            <h2 class="text-4xl font-black text-slate-800 tracking-tight font-outfit uppercase">${data.apprentice}</h2>
                            <p class="text-slate-400 font-bold tracking-widest text-sm uppercase">${data.date} <span class="mx-3 opacity-20">|</span> ${data.timestamp}</p>
                        </div>
                    </div>
                `;
        }

        function showEmptyResult(msg, isDenied = false) {
            const area = document.getElementById('fp-result-area');
            const colorClass = isDenied ? 'text-red-500 bg-red-50 border-red-100' : 'text-slate-400 bg-slate-50 border-slate-100';

            area.innerHTML = `
                    <div class="w-full animate-zoom-in">
                        <div class="mb-8 flex justify-center">
                            <div class="px-6 py-3 rounded-[1.5rem] border ${colorClass} text-xs font-black uppercase tracking-[0.2em] shadow-lg flex items-center gap-3">
                                <i class="fas fa-exclamation-circle text-lg"></i> ${msg}
                            </div>
                        </div>
                    </div>
                `;
        }

        function addToLog(data) {
            document.getElementById('no-activity-msg')?.remove();
            const log = document.getElementById('activity-log');
            const isEntrada = data.event_type === 'entrada';

            const html = `
                    <div class="p-5 bg-white border border-slate-100 rounded-[2rem] shadow-sm flex items-center gap-4 animate-slide-in-right hover:shadow-premium transition-all">
                        <div class="w-12 h-12 rounded-2xl ${isEntrada ? 'bg-green-50 text-sena' : 'bg-amber-50 text-amber-500'} flex items-center justify-center shrink-0 border border-slate-50 shadow-inner">
                            <i class="fas ${isEntrada ? 'fa-sign-in-alt' : 'fa-sign-out-alt'}"></i>
                        </div>
                        <div class="flex-grow">
                            <h4 class="font-black text-slate-800 text-sm font-outfit uppercase tracking-tight leading-none mb-1">${data.apprentice}</h4>
                            <div class="flex items-center gap-3">
                                 <span class="text-[10px] font-black ${isEntrada ? 'text-sena' : 'text-amber-500'} uppercase tracking-widest">${data.event_type}</span>
                                 <span class="text-[10px] text-slate-300">•</span>
                                 <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">${data.timestamp}</span>
                            </div>
                        </div>
                        <div class="text-[10px] font-black text-slate-300 uppercase">${data.date}</div>
                    </div>
                `;
            log.insertAdjacentHTML('afterbegin', html);
            activityLog.unshift(data);
            document.getElementById('activity-count').textContent = activityLog.length;
        }

        function applyCooldown(ms) {
            cooldownActive = true;
            setTimeout(() => {
                cooldownActive = false;
                lastTemplate = null;
                if (scanInterval) {
                    setStatus('scanning', 'fa-circle-notch fa-spin text-white', 'SISTEMA ACTIVO: PRESENTE DEDO');
                }
            }, ms);
        }

        document.addEventListener('DOMContentLoaded', () => {
            checkBridgeStatus();
            setInterval(checkBridgeStatus, 10000);
        });
    </script>
@endpush