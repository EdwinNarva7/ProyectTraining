@extends('layouts.master')

@section('title', 'Gestión de Huellas - SIEAP')
@section('page-title', 'Gestión de Huellas Digitales')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.fingerprint.scanner') }}">Biométrico</a></li>
    <li class="breadcrumb-item active text-sena font-bold">Enrolamiento</li>
@endsection

@push('styles')
    <style>
        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        @keyframes zoom-in {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .animate-zoom-in { animation: zoom-in 0.3s ease-out; }

        @keyframes enroll-pulse {
            0%, 100% { transform: scale(1); filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.3)); }
            50% { transform: scale(1.1); filter: drop-shadow(0 0 20px rgba(235, 255, 235, 0.8)); }
        }
        .enrolling-pulse {
            animation: enroll-pulse 1s infinite cubic-bezier(0.4, 0, 0.6, 1);
        }
    </style>
@endpush

@section('content')
    <div class="space-y-8 animate-fade-in pb-12">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 py-4">
            <div>
                <h1 class="text-4xl font-bold text-slate-800 tracking-tight font-outfit">
                    Gestión de <span class="text-sena">Huellas</span>
                </h1>
                <p class="text-slate-500 mt-2 font-medium text-lg">Enrola y administra el acceso biométrico de los aprendices.</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.fingerprint.scanner') }}"
                    class="flex items-center gap-3 px-6 py-4 bg-white border border-slate-200 text-slate-700 rounded-[1.5rem] font-bold hover:bg-slate-50 transition-all shadow-premium active:scale-95 group">
                    <i class="fas fa-desktop text-sena group-hover:scale-110 transition-transform"></i>
                    Ir al Panel de Marcación
                </a>
            </div>
        </div>

        {{-- Estadísticas - Premium Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-6">
            <!-- Total Aprendices -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-premium group hover:-translate-y-2 transition-all duration-300 border border-slate-100/50">
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 bg-blue-50/50 rounded-3xl flex items-center justify-center text-blue-600 group-hover:scale-110 transition-transform duration-300 shadow-sm border border-blue-100/50">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-1">Total Aprendices</p>
                        <h3 class="text-3xl font-bold text-slate-800 font-outfit leading-none">{{ $totalCount }}</h3>
                    </div>
                </div>
            </div>

            <!-- Enrolados -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-premium group hover:-translate-y-2 transition-all duration-300 border border-slate-100/50">
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 bg-green-50/50 rounded-3xl flex items-center justify-center text-sena group-hover:scale-110 transition-transform duration-300 shadow-sm border border-green-100/50">
                        <i class="fas fa-fingerprint text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-1">Con Huella Enrolada</p>
                        <h3 class="text-3xl font-bold text-slate-800 font-outfit leading-none">{{ $enrolledCount }}</h3>
                    </div>
                </div>
            </div>

            <!-- Sin Huella -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-premium group hover:-translate-y-2 transition-all duration-300 border border-slate-100/50">
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 bg-red-50/50 rounded-3xl flex items-center justify-center text-red-500 group-hover:scale-110 transition-transform duration-300 shadow-sm border border-red-100/50">
                        <i class="fas fa-user-times text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-1">Sin Huella</p>
                        <h3 class="text-3xl font-bold text-slate-800 font-outfit leading-none">{{ $totalCount - $enrolledCount }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabla de Aprendices --}}
        <div class="bg-white rounded-[3rem] shadow-premium border border-slate-100 overflow-hidden group">
            <div class="p-10 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                <div>
                    <h3 class="text-2xl font-bold text-slate-800 font-outfit">Aprendices - <span class="text-sena font-black">Biométrico</span></h3>
                    <p class="text-[11px] text-slate-400 mt-2 font-bold uppercase tracking-[0.2em]">Listado general de enrolamiento</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80">
                            <th class="px-10 py-6 text-[11px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100">Aprendiz</th>
                            <th class="px-6 py-6 text-[11px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100">Documento</th>
                            <th class="px-6 py-6 text-[11px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100">Ficha</th>
                            <th class="px-6 py-6 text-[11px] font-bold text-slate-500 uppercase tracking-widest text-center border-b border-slate-100">Estado</th>
                            <th class="px-6 py-6 text-[11px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100">Enrolado el</th>
                            <th class="px-10 py-6 text-[11px] font-bold text-slate-500 uppercase tracking-widest text-right border-b border-slate-100">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50/50">
                        @forelse($apprentices as $apprentice)
                            @php
                                $profile = $apprentice->apprenticeProfile;
                                $enrolled = $profile && $profile->hasFingerprint();
                                $initials = collect(explode(' ', $apprentice->full_name))
                                    ->take(2)
                                    ->map(fn($w) => strtoupper($w[0] ?? ''))
                                    ->implode('');
                            @endphp
                            <tr class="hover:bg-slate-50/40 transition-all group">
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-5">
                                        <div class="w-14 h-14 sena-gradient rounded-[1.25rem] flex items-center justify-center text-white font-bold text-base shadow-lg group-hover:scale-110 transition-transform shrink-0 border-2 border-white">
                                            {{ $initials }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800 text-base tracking-tight mb-0.5">{{ $apprentice->full_name }}</div>
                                            <div class="text-xs text-slate-400 font-semibold">{{ $apprentice->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-6 text-sm font-bold text-slate-600">
                                    {{ $profile->document_number ?? '—' }}
                                </td>
                                <td class="px-6 py-6">
                                    <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-lg text-[11px] font-bold uppercase tracking-wider">
                                        {{ $profile->cohort ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    @if($enrolled)
                                        <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-green-50 text-sena rounded-full text-[10px] font-black uppercase tracking-widest border border-green-100 shadow-sm">
                                            <i class="fas fa-check-circle text-xs"></i> ENROLADO
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-red-50 text-red-500 rounded-full text-[10px] font-black uppercase tracking-widest border border-red-100 shadow-sm">
                                            <i class="fas fa-times-circle text-xs"></i> SIN HUELLA
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-6 text-xs font-bold text-slate-400 uppercase tracking-widest">
                                    {{ $enrolled ? $profile->fingerprint_enrolled_at->format('d/m/Y H:i') : '—' }}
                                </td>
                                <td class="px-10 py-6 text-right border-l border-transparent group-hover:border-slate-100">
                                    <div class="flex items-center justify-end gap-3 text-right">
                                        <button 
                                            onclick="openEnrollModal({{ $apprentice->id }}, '{{ addslashes($apprentice->full_name) }}', {{ $enrolled ? 'true' : 'false' }})"
                                            class="w-10 h-10 flex items-center justify-center text-blue-500 bg-white hover:bg-blue-500 hover:text-white rounded-2xl transition-all shadow-premium border border-slate-100 hover:scale-110 active:scale-95"
                                            title="{{ $enrolled ? 'Re-enrolar' : 'Enrolar' }}">
                                            <i class="fas fa-fingerprint text-lg"></i>
                                        </button>
                                        @if($enrolled)
                                            <button 
                                                onclick="deleteFingerprint({{ $apprentice->id }}, '{{ addslashes($apprentice->full_name) }}')"
                                                class="w-10 h-10 flex items-center justify-center text-red-500 bg-white hover:bg-red-500 hover:text-white rounded-2xl transition-all shadow-premium border border-slate-100 hover:scale-110 active:scale-95"
                                                title="Eliminar Huella">
                                                <i class="fas fa-trash-alt text-lg"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-10 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-24 h-24 bg-slate-50 rounded-[2rem] flex items-center justify-center text-slate-200 mb-6 shadow-inner border border-slate-100">
                                            <i class="fas fa-users-slash text-4xl"></i>
                                        </div>
                                        <p class="text-slate-400 font-black uppercase tracking-[0.3em] text-xs">Sin aprendices activos</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal de Enrolamiento - Premium Redesign --}}
    <div id="enrollModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/60 backdrop-blur-md p-4 overflow-hidden">
        <div class="bg-white w-full max-w-lg rounded-[3.5rem] shadow-2xl overflow-hidden animate-zoom-in border border-white/20">
            {{-- Header del Modal --}}
            <div id="enroll-scanner-header" class="relative p-12 sena-gradient text-white text-center transition-all duration-500">
                <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none overflow-hidden">
                    <svg class="h-full w-full opacity-40" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <circle cx="50" cy="50" r="40" fill="none" stroke="currentColor" stroke-width="0.5" />
                        <circle cx="50" cy="50" r="30" fill="none" stroke="currentColor" stroke-width="0.5" />
                        <circle cx="50" cy="50" r="20" fill="none" stroke="currentColor" stroke-width="0.5" />
                    </svg>
                </div>
                
                <div id="enroll-icon-wrap" class="relative z-10 w-28 h-28 bg-white/20 rounded-[2.5rem] flex items-center justify-center mx-auto mb-8 backdrop-blur-xl shadow-2xl border border-white/30 group">
                    <i class="fas fa-fingerprint text-6xl shadow-white/10" id="enroll-fp-icon"></i>
                </div>
                
                <h4 class="text-3xl font-black font-outfit mb-2 tracking-tight uppercase" id="enroll-modal-title">Enrolando huella</h4>
                <p class="text-white/90 font-bold text-base tracking-wide" id="enroll-modal-subtitle">Conecta tu lector para iniciar</p>
            </div>

            <div class="p-12">
                <div class="text-center mb-10">
                    <span class="text-[10px] font-black text-slate-300 uppercase tracking-[0.4em] block mb-3">Expediente del Aprendiz</span>
                    <h3 id="enroll-apprentice-name" class="text-2xl font-black text-slate-800 tracking-tight font-outfit"></h3>
                </div>

                {{-- Barra de progreso --}}
                <div id="enroll-progress-wrap" class="hidden mb-10 space-y-4">
                    <div class="flex justify-between items-center px-1">
                        <span class="text-[11px] font-black text-slate-400 uppercase tracking-[0.3em]">Calidad de Captura</span>
                        <span id="enroll-progress-label" class="text-lg font-black text-sena font-outfit">0%</span>
                    </div>
                    <div class="h-4 bg-slate-50 border border-slate-100 rounded-full overflow-hidden p-1 shadow-inner">
                        <div id="enroll-progress-bar" class="h-full sena-gradient rounded-full shadow-lg transition-all duration-500 ease-out" style="width: 0%"></div>
                    </div>
                </div>

                {{-- Instrucciones --}}
                <div id="enroll-instructions" class="bg-slate-50/50 rounded-[2rem] p-8 mb-10 border border-slate-100 shadow-inner">
                    <h5 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.3em] mb-6">Procedimiento Seguro</h5>
                    <ul class="space-y-4 text-xs font-bold text-slate-600">
                        <li class="flex items-center gap-4 group">
                            <span class="w-8 h-8 bg-white rounded-2xl border border-slate-200 flex items-center justify-center text-sena shadow-premium shrink-0 group-hover:scale-110 transition-transform">1</span>
                            Bridge SIEAP activo en el sistema.
                        </li>
                        <li class="flex items-center gap-4 group text-slate-500">
                            <span class="w-8 h-8 bg-white rounded-2xl border border-slate-200 flex items-center justify-center text-sena shadow-premium shrink-0 group-hover:scale-110 transition-transform">2</span>
                            Limpiar lector con paño seco.
                        </li>
                        <li class="flex items-center gap-4 group text-slate-500">
                            <span class="w-8 h-8 bg-white rounded-2xl border border-slate-200 flex items-center justify-center text-sena shadow-premium shrink-0 group-hover:scale-110 transition-transform">3</span>
                            Colocar dedo índice centrado.
                        </li>
                    </ul>
                </div>

                {{-- Resultado Banner --}}
                <div id="enroll-result" class="hidden p-6 rounded-[2rem] text-xs font-black uppercase tracking-widest text-center mb-10 animate-fade-in shadow-lg border"></div>

                <div class="flex flex-col gap-4">
                    <button type="button" id="btn-start-enroll" onclick="startEnroll()"
                        class="w-full py-5 sena-gradient text-white rounded-[2rem] font-black text-lg shadow-xl hover:shadow-sena/40 active:scale-[0.97] transition-all flex items-center justify-center gap-4 group">
                        <i class="fas fa-fingerprint text-xl group-hover:rotate-12 transition-transform"></i>
                        <span>INICIAR CAPTURA</span>
                    </button>
                    
                    <button type="button" id="btn-save-enroll" onclick="saveEnroll()"
                        class="hidden w-full py-5 bg-slate-900 text-white rounded-[2rem] font-black text-lg shadow-xl hover:shadow-black/20 active:scale-[0.97] transition-all flex items-center justify-center gap-4 group">
                        <i class="fas fa-shield-alt text-xl group-hover:scale-110 transition-transform text-sena"></i>
                        <span>RESGUARDAR DATOS</span>
                    </button>
                    
                    <button type="button" onclick="closeEnrollModal()" id="btn-cancel-enroll"
                        class="w-full py-3 text-slate-400 text-[10px] font-black uppercase tracking-[0.4em] hover:text-red-500 transition-all active:scale-95">
                        Abortar Operación
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const BRIDGE_URL = 'http://localhost:8080';
        const ENROLL_URL = '{{ route("admin.fingerprint.enroll.save") }}';
        const DELETE_URL_TPL = '{{ url("admin/fingerprint") }}';
        const CSRF_TOKEN = '{{ csrf_token() }}';

        let currentApprenticeId = null;
        let capturedTemplateBase64 = null;

        // ─────────────────────────────────────────────────────────────────────────────
        function openEnrollModal(apprenticeId, name, isAlreadyEnrolled) {
            currentApprenticeId = apprenticeId;
            capturedTemplateBase64 = null;

            document.getElementById('enroll-apprentice-name').textContent = name;
            document.getElementById('enroll-modal-title').textContent = isAlreadyEnrolled ? 'Re-enrolando' : 'Enrolando';
            document.getElementById('enroll-modal-subtitle').textContent = 'Preparado para capturar';
            document.getElementById('enroll-instructions').style.display = 'block';
            document.getElementById('enroll-progress-wrap').classList.add('hidden');
            document.getElementById('enroll-result').classList.add('hidden');
            document.getElementById('btn-start-enroll').classList.remove('hidden');
            document.getElementById('btn-save-enroll').classList.add('hidden');
            document.getElementById('enroll-icon-wrap').classList.remove('enrolling-pulse');
            document.getElementById('btn-start-enroll').disabled = false;

            document.getElementById('enrollModal').classList.remove('hidden');
            document.getElementById('enrollModal').classList.add('flex');
        }

        function closeEnrollModal() {
            document.getElementById('enrollModal').classList.add('hidden');
            document.getElementById('enrollModal').classList.remove('flex');
        }

        // ─────────────────────────────────────────────────────────────────────────────
        async function startEnroll() {
            const btn = document.getElementById('btn-start-enroll');
            btn.disabled = true;
            document.getElementById('enroll-modal-subtitle').textContent = 'Coloca el dedo en el lector...';
            document.getElementById('enroll-progress-wrap').classList.remove('hidden');
            document.getElementById('enroll-icon-wrap').classList.add('enrolling-pulse');

            let progress = 0;
            const progressBar = document.getElementById('enroll-progress-bar');
            const progressLabel = document.getElementById('enroll-progress-label');

            const progressInterval = setInterval(() => {
                progress = Math.min(progress + 3, 92);
                progressBar.style.width = progress + '%';
                progressLabel.textContent = progress + '%';
            }, 180);

            try {
                const template = await waitForCapture(12000);

                clearInterval(progressInterval);
                progressBar.style.width = '100%';
                progressLabel.textContent = '100%';

                capturedTemplateBase64 = template;

                document.getElementById('enroll-icon-wrap').classList.remove('enrolling-pulse');
                document.getElementById('enroll-modal-subtitle').textContent = '✓ Huella capturada con éxito';
                document.getElementById('enroll-instructions').style.display = 'none';

                const result = document.getElementById('enroll-result');
                result.className = 'p-6 rounded-[2rem] text-xs font-black uppercase tracking-widest text-center mb-10 animate-fade-in shadow-lg border bg-green-50/50 text-sena border-green-100/50';
                result.classList.remove('hidden');
                result.innerHTML = '<i class="fas fa-check-circle mr-3"></i>Datos Biométricos Verificados. Procede a Guardar.';

                document.getElementById('btn-save-enroll').classList.remove('hidden');
                btn.classList.add('hidden');

            } catch (err) {
                clearInterval(progressInterval);
                document.getElementById('enroll-icon-wrap').classList.remove('enrolling-pulse');
                const result = document.getElementById('enroll-result');
                result.className = 'p-6 rounded-[2rem] text-xs font-black uppercase tracking-widest text-center mb-10 animate-fade-in shadow-lg border bg-red-50 text-red-500 border-red-100';
                result.classList.remove('hidden');
                result.innerHTML = '<i class="fas fa-times-circle mr-3"></i>' + err.message;
                btn.disabled = false;
            }
        }

        // ─────────────────────────────────────────────────────────────────────────────
        async function waitForCapture(timeoutMs) {
            const deadline = Date.now() + timeoutMs;
            let lastTemplate = null;

            try {
                const initRes = await fetch(`${BRIDGE_URL}/capture`, { signal: AbortSignal.timeout(1500) });
                const initData = await initRes.json();
                if (initData.status === 'success') lastTemplate = initData.template_base64;
            } catch { }

            return new Promise((resolve, reject) => {
                const interval = setInterval(async () => {
                    if (Date.now() > deadline) {
                        clearInterval(interval);
                        reject(new Error('Límite de tiempo excedido'));
                        return;
                    }
                    try {
                        const res = await fetch(`${BRIDGE_URL}/capture`, { signal: AbortSignal.timeout(1800) });
                        const data = await res.json();
                        if (data.status === 'success' && data.template_base64 && data.template_base64 !== lastTemplate) {
                            clearInterval(interval);
                            resolve(data.template_base64);
                        }
                    } catch { }
                }, 850);
            });
        }

        // ─────────────────────────────────────────────────────────────────────────────
        async function saveEnroll() {
            const btn = document.getElementById('btn-save-enroll');
            btn.disabled = true;

            try {
                const res = await fetch(ENROLL_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        apprentice_id: currentApprenticeId,
                        template_base64: capturedTemplateBase64
                    })
                });
                const data = await res.json();

                const result = document.getElementById('enroll-result');
                if (data.success) {
                    result.className = 'p-6 rounded-[2rem] text-xs font-black uppercase tracking-widest text-center mb-10 animate-fade-in shadow-lg border bg-blue-50 text-blue-600 border-blue-100';
                    result.innerHTML = '<i class="fas fa-shield-check mr-3"></i>' + data.message;
                    setTimeout(() => { location.reload(); }, 1200);
                } else {
                    result.className = 'p-6 rounded-[2rem] text-xs font-black uppercase tracking-widest text-center mb-10 animate-fade-in shadow-lg border bg-red-50 text-red-500 border-red-100';
                    result.innerHTML = '<i class="fas fa-exclamation-triangle mr-3"></i>' + data.message;
                    btn.disabled = false;
                }
            } catch (err) {
                const result = document.getElementById('enroll-result');
                result.className = 'p-6 rounded-[2rem] text-xs font-black uppercase tracking-widest text-center mb-10 animate-fade-in shadow-lg border bg-red-50 text-red-500 border-red-100';
                result.innerHTML = '<i class="fas fa-server mr-3 text-red-900"></i>Error de conexión con el Servidor';
                btn.disabled = false;
            }
        }

        // ─────────────────────────────────────────────────────────────────────────────
        function deleteFingerprint(apprenticeId, name) {
            Swal.fire({
                title: '¿Eliminar Registro?',
                text: `Se borrará la huella digital de ${name}. Deberá enrolarse nuevamente para el acceso.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#39A900',
                cancelButtonColor: '#ff4b4b',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                customClass: {
                    popup: 'rounded-[2rem]',
                    confirmButton: 'rounded-xl px-6 py-3',
                    cancelButton: 'rounded-xl px-6 py-3'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`${DELETE_URL_TPL}/${apprenticeId}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Eliminado', data.message, 'success');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    });
                }
            });
        }
    </script>
@endpush