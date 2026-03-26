@extends('layouts.master')

@section('title', 'Gestión de Huellas - SIEAP')
@section('page-title', 'Gestión de Huellas Digitales')

@section('breadcrumb')
    <li class="breadcrumb-item active text-sena font-bold">Gestión de Huellas</li>
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
            0%, 100% { transform: scale(1); filter: drop-shadow(0 0 10px rgba(57, 169, 0, 0.3)); }
            50% { transform: scale(1.1); filter: drop-shadow(0 0 20px rgba(57, 169, 0, 0.6)); }
        }
        .enrolling-pulse {
            animation: enroll-pulse 1.5s infinite ease-in-out;
        }

        /* Scan Animations like Login */
        .scanner-visual-wrap {
            position: relative;
            width: 140px;
            height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .scan-circle {
            position: absolute;
            inset: 0;
            border: 2px solid rgba(255, 255, 255, 0.15);
            border-radius: 40%;
            animation: rotate 10s linear infinite;
        }

        .scan-circle-inner {
            position: absolute;
            inset: 15px;
            border: 2px dashed rgba(255, 255, 255, 0.3);
            border-radius: 40%;
            animation: rotate-reverse 15s linear infinite;
        }

        @keyframes rotate { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        @keyframes rotate-reverse { from { transform: rotate(360deg); } to { transform: rotate(0deg); } }

        .scan-line {
            position: absolute;
            top: 20%;
            left: 15%;
            width: 70%;
            height: 3px;
            background: linear-gradient(to right, transparent, white, transparent);
            box-shadow: 0 0 15px white;
            border-radius: 50%;
            opacity: 0;
            z-index: 30;
        }

        .scanning .scan-line {
            animation: scan-move 2s ease-in-out infinite;
            opacity: 1;
        }

        @keyframes scan-move {
            0%, 100% { top: 25%; }
            50% { top: 75%; }
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }
    </style>
@endpush

@section('content')
    <div class="space-y-8 animate-fade-in pb-12">
        {{-- Stats & Hero Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            {{-- Welcome & Info --}}
            <div class="lg:col-span-2 bg-white p-10 rounded-[3rem] shadow-premium border border-slate-100 flex flex-col justify-center relative overflow-hidden group">
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-sena/5 rounded-full blur-3xl group-hover:bg-sena/10 transition-colors"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-sena/10 text-sena rounded-full text-[10px] font-black uppercase tracking-widest">
                            <i class="fas fa-fingerprint"></i> Módulo Biométrico
                        </div>
                        {{-- Bridge Status Indicator --}}
                        <div id="bridgeStatusContainer" class="flex items-center gap-2 px-4 py-2 bg-slate-50 rounded-full border border-slate-100 transition-all duration-500">
                            <span id="bridgeStatusDot" class="w-2 h-2 rounded-full bg-slate-300"></span>
                            <span id="bridgeStatusText" class="text-[9px] font-black uppercase tracking-widest text-slate-400">Bridge Offline</span>
                        </div>
                    </div>
                    <h1 class="text-4xl font-bold text-slate-800 tracking-tight font-outfit mb-4">
                        Enrolamiento <span class="text-sena">Seguro</span>
                    </h1>
                    <p class="text-slate-500 font-medium text-base leading-relaxed">
                        Administre el acceso biométrico de los aprendices. El indicador superior muestra si el sistema detecta el lector.
                    </p>
                </div>
            </div>

            {{-- Mini Stats --}}
            <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-premium border border-slate-100 group">
                    <div class="flex items-center gap-5">
                        <div class="w-14 h-14 bg-emerald-50 text-sena rounded-2xl flex items-center justify-center text-xl shadow-sm group-hover:scale-110 transition-transform">
                            <i class="fas fa-id-badge"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Enrolados</p>
                            <h3 class="text-2xl font-black text-slate-800 font-outfit">{{ $enrolledCount }} <span class="text-slate-300 font-medium text-sm">/ {{ $totalCount }}</span></h3>
                        </div>
                    </div>
                    <div class="mt-6 w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-sena h-full transition-all duration-1000" style="width: {{ ($enrolledCount / max(1, $totalCount)) * 100 }}%"></div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[2.5rem] shadow-premium border border-slate-100 group">
                    <div class="flex items-center gap-5">
                        <div class="w-14 h-14 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center text-xl shadow-sm group-hover:scale-110 transition-transform">
                            <i class="fas fa-user-clock"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Pendientes</p>
                            <h3 class="text-2xl font-black text-slate-800 font-outfit">{{ $totalCount - $enrolledCount }}</h3>
                        </div>
                    </div>
                    <p class="mt-4 text-[10px] text-slate-400 font-bold uppercase tracking-tighter italic">Requieren acción inmediata</p>
                </div>
            </div>
        </div>

        {{-- Main Content: Search & List --}}
        <div class="bg-white rounded-[3.5rem] shadow-premium border border-slate-100 overflow-hidden group">
            <div class="p-10 border-b border-slate-50 flex flex-col md:flex-row items-center justify-between gap-6 bg-slate-50/30">
                <div class="flex items-center gap-4">
                    <div class="w-1.5 h-8 bg-sena rounded-full"></div>
                    <h3 class="text-2xl font-bold text-slate-800 font-outfit">Listado de Aprendices</h3>
                </div>
                
                <div class="relative w-full md:w-96">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-slate-300">
                        <i class="fas fa-search"></i>
                    </div>
                    <input type="text" id="apprenticeSearch" onkeyup="filterApprentices()" placeholder="Buscar por nombre o documento..." 
                        class="w-full pl-12 pr-6 py-4 rounded-2xl bg-white border-slate-200 text-sm font-medium focus:ring-2 focus:ring-sena/20 focus:border-sena transition-all shadow-sm">
                </div>
            </div>

            <div class="max-h-[600px] overflow-y-auto custom-scrollbar">
                <table class="w-full text-left border-collapse" id="apprenticesTable">
                    <thead class="sticky top-0 bg-white z-10 shadow-sm">
                        <tr class="bg-slate-50/80">
                            <th class="px-10 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Colaborador</th>
                            <th class="px-6 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Documento</th>
                            <th class="px-6 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Estado</th>
                            <th class="px-10 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($apprentices as $apprentice)
                            @php
                                $profile = $apprentice->apprenticeProfile;
                                $enrolled = $profile && $profile->hasFingerprint();
                                $initials = collect(explode(' ', $apprentice->full_name))->take(2)->map(fn($w) => strtoupper($w[0] ?? ''))->implode('');
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-all group apprentice-row">
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 sena-gradient rounded-2xl flex items-center justify-center text-white font-bold text-sm shadow-lg group-hover:scale-110 transition-transform shrink-0 border-2 border-white">
                                            @if($apprentice->profile_photo_path)
                                                <img src="{{ Storage::url($apprentice->profile_photo_path) }}" class="w-full h-full object-cover rounded-2xl">
                                            @else
                                                {{ $initials }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800 text-sm tracking-tight apprentice-name">{{ $apprentice->full_name }}</div>
                                            <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Ficha: {{ $profile->cohort ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-6 text-xs font-bold text-slate-500 apprentice-doc">
                                    {{ $profile->document_number ?? '—' }}
                                </td>
                                <td class="px-6 py-6 text-center">
                                    @if($enrolled)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-sena rounded-full text-[9px] font-black uppercase tracking-widest border border-emerald-100">
                                            <i class="fas fa-check-circle"></i> LISTO
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-50 text-rose-500 rounded-full text-[9px] font-black uppercase tracking-widest border border-rose-100">
                                            <i class="fas fa-fingerprint"></i> PENDIENTE
                                        </span>
                                    @endif
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button onclick="openEnrollModal({{ $apprentice->id }}, '{{ addslashes($apprentice->full_name) }}', {{ $enrolled ? 'true' : 'false' }})"
                                            class="w-9 h-9 flex items-center justify-center text-sena bg-white border border-slate-100 rounded-xl shadow-sm hover:bg-sena hover:text-white hover:scale-110 transition-all active:scale-95"
                                            title="Enrolar">
                                            <i class="fas fa-fingerprint text-sm"></i>
                                        </button>
                                        @if($enrolled)
                                            <button onclick="deleteFingerprint({{ $apprentice->id }}, '{{ addslashes($apprentice->full_name) }}')"
                                                class="w-9 h-9 flex items-center justify-center text-rose-500 bg-white border border-slate-100 rounded-xl shadow-sm hover:bg-rose-500 hover:text-white hover:scale-110 transition-all active:scale-95"
                                                title="Eliminar">
                                                <i class="fas fa-trash-alt text-sm"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-10 py-20 text-center">
                                    <p class="text-slate-400 font-bold uppercase tracking-widest text-xs">No hay aprendices disponibles</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal de Enrolamiento - Pro Redesign --}}
    <div id="enrollModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-hidden">
        <div class="bg-white w-full max-w-md rounded-[3rem] shadow-2xl overflow-hidden animate-zoom-in border border-slate-100">
            {{-- Status Header --}}
            <div id="enroll-scanner-header" class="relative p-10 sena-gradient text-white text-center">
                <div class="absolute inset-0 opacity-10">
                    <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <path d="M0 0 L100 0 L100 100 L0 100 Z" fill="none" stroke="currentColor" stroke-width="0.5" stroke-dasharray="2 2" />
                    </svg>
                </div>
                
                <div id="scannerVisualWrap" class="scanner-visual-wrap">
                    <div class="scan-circle"></div>
                    <div class="scan-circle-inner"></div>
                    <div class="scan-line"></div>
                    <div id="enroll-icon-wrap" class="relative z-10 w-24 h-24 bg-white/20 rounded-[2rem] flex items-center justify-center backdrop-blur-md border border-white/30 shadow-xl transition-all duration-500">
                        <i class="fas fa-fingerprint text-5xl" id="enroll-fp-icon"></i>
                        <i class="fas fa-check text-5xl text-emerald-400 hidden" id="enroll-check-icon"></i>
                    </div>
                </div>
                
                <h4 class="text-2xl font-bold font-outfit mb-1 tracking-tight uppercase" id="enroll-modal-title">Enrolamiento</h4>
                <p class="text-white/80 font-bold text-[10px] uppercase tracking-[0.3em]" id="enroll-modal-subtitle">Esperando Lector</p>
            </div>

            <div class="p-10">
                <div class="text-center mb-8">
                    <p class="text-[9px] font-black text-slate-300 uppercase tracking-[0.4em] mb-2">Colaborador Seleccionado</p>
                    <h3 id="enroll-apprentice-name" class="text-xl font-black text-slate-800 tracking-tight font-outfit truncate"></h3>
                </div>

                {{-- Progress Area --}}
                <div id="enroll-progress-wrap" class="hidden mb-8">
                    <div class="flex justify-between items-center mb-2 px-1">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Calidad de Señal</span>
                        <span id="enroll-progress-label" class="text-sm font-black text-sena font-outfit">0%</span>
                    </div>
                    <div class="h-3 bg-slate-50 border border-slate-100 rounded-full overflow-hidden p-0.5">
                        <div id="enroll-progress-bar" class="h-full sena-gradient rounded-full transition-all duration-500" style="width: 0%"></div>
                    </div>
                </div>

                {{-- Result Feedback --}}
                <div id="enroll-result" class="hidden p-4 rounded-2xl text-[10px] font-black uppercase tracking-widest text-center mb-8 animate-fade-in shadow-sm border"></div>

                {{-- Action Buttons --}}
                <div class="space-y-3">
                    <button type="button" id="btn-start-enroll" onclick="startEnroll()"
                        class="w-full py-4 sena-gradient text-white rounded-2xl font-black text-sm shadow-lg shadow-sena/20 hover:shadow-sena/40 active:scale-[0.98] transition-all flex items-center justify-center gap-3 group">
                        <i class="fas fa-fingerprint group-hover:rotate-12 transition-transform"></i>
                        <span>COMENZAR CAPTURA</span>
                    </button>
                    
                    <button type="button" id="btn-save-enroll" onclick="saveEnroll()"
                        class="hidden w-full py-4 bg-slate-900 text-white rounded-2xl font-black text-sm shadow-lg hover:bg-slate-800 active:scale-[0.98] transition-all flex items-center justify-center gap-3">
                        <i class="fas fa-cloud-upload-alt text-sena"></i>
                        <span>GUARDAR REGISTRO</span>
                    </button>
                    
                    <button type="button" onclick="closeEnrollModal()" 
                        class="w-full py-3 text-slate-400 text-[9px] font-black uppercase tracking-[0.3em] hover:text-rose-500 transition-colors">
                        Cancelar Operación
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Permitimos que el usuario cambie la IP si es necesario (localhost por defecto)
        let BRIDGE_URL = 'http://localhost:8080';
        const ENROLL_URL = '{{ route("admin.fingerprint.enroll.save") }}';
        const DELETE_URL_TPL = '{{ url("admin/fingerprint") }}';
        const CSRF_TOKEN = '{{ csrf_token() }}';

        let currentApprenticeId = null;
        let capturedTemplateBase64 = null;
        let isBridgeOnline = false;
        let enrollPollingInterval = null;

        // Verificar estado del Bridge periódicamente
        async function checkBridge() {
            try {
                // Usamos un fetch muy básico para el status
                const res = await fetch(`${BRIDGE_URL}/status`).catch(() => {
                    // Si falla localhost, intentamos 127.0.0.1 automáticamente
                    if (BRIDGE_URL.includes('localhost')) {
                        BRIDGE_URL = 'http://127.0.0.1:8080';
                    } else {
                        BRIDGE_URL = 'http://localhost:8080';
                    }
                    return fetch(`${BRIDGE_URL}/status`);
                });

                if(res.ok) {
                    isBridgeOnline = true;
                    document.getElementById('bridgeStatusDot').className = 'w-2 h-2 rounded-full bg-sena shadow-[0_0_8px_#39A900]';
                    document.getElementById('bridgeStatusText').textContent = 'Bridge Online (' + BRIDGE_URL.split('//')[1].split(':')[0] + ')';
                    document.getElementById('bridgeStatusText').className = 'text-[9px] font-black uppercase tracking-widest text-sena';
                    document.getElementById('bridgeStatusContainer').className = 'flex items-center gap-2 px-4 py-2 bg-emerald-50 rounded-full border border-emerald-100 transition-all duration-500';
                }
            } catch (err) {
                isBridgeOnline = false;
                document.getElementById('bridgeStatusDot').className = 'w-2 h-2 rounded-full bg-slate-300';
                document.getElementById('bridgeStatusText').textContent = 'Bridge Offline';
                document.getElementById('bridgeStatusText').className = 'text-[9px] font-black uppercase tracking-widest text-slate-400';
                document.getElementById('bridgeStatusContainer').className = 'flex items-center gap-2 px-4 py-2 bg-slate-50 rounded-full border border-slate-100 transition-all duration-500';
            }
        }
        setInterval(checkBridge, 5000);
        checkBridge();

        function filterApprentices() {
            const search = document.getElementById('apprenticeSearch').value.toLowerCase();
            const rows = document.querySelectorAll('.apprentice-row');
            
            rows.forEach(row => {
                const name = row.querySelector('.apprentice-name').textContent.toLowerCase();
                const doc = row.querySelector('.apprentice-doc').textContent.toLowerCase();
                if (name.includes(search) || doc.includes(search)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function openEnrollModal(apprenticeId, name, isAlreadyEnrolled) {
            currentApprenticeId = apprenticeId;
            capturedTemplateBase64 = null;
            document.getElementById('enroll-apprentice-name').textContent = name;
            document.getElementById('enroll-modal-title').textContent = isAlreadyEnrolled ? 'Actualizar' : 'Enrolar';
            document.getElementById('enrollModal').classList.remove('hidden');
            document.getElementById('enrollModal').classList.add('flex');
            resetModalUI();
        }

        function closeEnrollModal() {
            document.getElementById('enrollModal').classList.add('hidden');
            document.getElementById('enrollModal').classList.remove('flex');
        }

        function resetModalUI() {
            if (enrollPollingInterval) clearInterval(enrollPollingInterval);
            document.getElementById('enroll-progress-wrap').classList.add('hidden');
            document.getElementById('enroll-result').classList.add('hidden');
            document.getElementById('btn-start-enroll').classList.remove('hidden');
            document.getElementById('btn-save-enroll').classList.add('hidden');
            document.getElementById('enroll-fp-icon').className = 'fas fa-fingerprint text-5xl';
            document.getElementById('enroll-fp-icon').classList.remove('hidden');
            document.getElementById('enroll-check-icon').classList.add('hidden');
            document.getElementById('scannerVisualWrap').classList.remove('scanning');
            document.getElementById('enroll-modal-subtitle').textContent = 'Esperando Lector';
            document.getElementById('enroll-progress-bar').style.width = '0%';
        }

        async function startEnroll() {
            const btn = document.getElementById('btn-start-enroll');
            const scannerWrap = document.getElementById('scannerVisualWrap');
            const subtitle = document.getElementById('enroll-modal-subtitle');
            const fpIcon = document.getElementById('enroll-fp-icon');
            
            btn.disabled = true;
            btn.classList.add('opacity-50', 'pointer-events-none');
            scannerWrap.classList.add('scanning');
            subtitle.textContent = 'Coloque el dedo en el lector';
            
            try {
                // Resetear el estado del bridge para nueva captura
                await fetch(`${BRIDGE_URL}/reset`, { method: 'POST', mode: 'no-cors' }).catch(() => {});
                
                // Iniciamos el polling igual que en el login
                const pollStart = Date.now();
                enrollPollingInterval = setInterval(async () => {
                    try {
                        const response = await fetch(`${BRIDGE_URL}/capture`, { signal: AbortSignal.timeout(800) });
                        const data = await response.json();

                        if (data.status === 'success' && data.template_base64) {
                            clearInterval(enrollPollingInterval);
                            capturedTemplateBase64 = data.template_base64;
                            showSuccessUI();
                            scannerWrap.classList.remove('scanning');
                            btn.disabled = false;
                            btn.classList.remove('opacity-50', 'pointer-events-none');
                        }
                    } catch (e) {}

                    // Timeout personal de 30 segundos
                    if (Date.now() - pollStart > 30000) {
                        clearInterval(enrollPollingInterval);
                        scannerWrap.classList.remove('scanning');
                        showErrorUI('Tiempo agotado. Intente de nuevo');
                        btn.disabled = false;
                        btn.classList.remove('opacity-50', 'pointer-events-none');
                    }
                }, 600);

            } catch (error) {
                console.error('Error:', error);
                showErrorUI('¿Bridge SIEAP activo?');
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'pointer-events-none');
                scannerWrap.classList.remove('scanning');
            }
        }

        function showSuccessUI() {
            const result = document.getElementById('enroll-result');
            const fpIcon = document.getElementById('enroll-fp-icon');
            const checkIcon = document.getElementById('enroll-check-icon');
            
            document.getElementById('enroll-progress-wrap').classList.remove('hidden');
            document.getElementById('enroll-progress-bar').style.width = '100%';
            document.getElementById('enroll-progress-label').textContent = '100%';
            
            // Animación de éxito
            fpIcon.classList.add('hidden');
            checkIcon.classList.remove('hidden');
            
            result.className = 'p-4 rounded-2xl text-[10px] font-black uppercase tracking-widest text-center mb-8 animate-fade-in shadow-sm border bg-emerald-50 text-emerald-600 border-emerald-100 block';
            result.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Captura Exitosa';
            
            document.getElementById('btn-start-enroll').classList.add('hidden');
            document.getElementById('btn-save-enroll').classList.remove('hidden');
            document.getElementById('enroll-modal-subtitle').textContent = 'Huella Verificada';
        }

        function showErrorUI(msg) {
            const result = document.getElementById('enroll-result');
            const fpIcon = document.getElementById('enroll-fp-icon');
            
            fpIcon.className = 'fas fa-exclamation-triangle text-4xl text-rose-400';
            
            result.className = 'p-4 rounded-2xl text-[10px] font-black uppercase tracking-widest text-center mb-8 animate-fade-in shadow-sm border bg-rose-50 text-rose-600 border-rose-100 block';
            result.innerHTML = `<i class="fas fa-exclamation-triangle mr-2"></i> ${msg}`;
            document.getElementById('enroll-modal-subtitle').textContent = 'Intente de nuevo';
        }

        async function saveEnroll() {
            if (!currentApprenticeId || !capturedTemplateBase64) return;

            try {
                const response = await fetch(ENROLL_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                    body: JSON.stringify({
                        apprentice_id: currentApprenticeId,
                        template_base64: capturedTemplateBase64
                    })
                });

                const data = await response.json();
                if (data.success) {
                    Swal.fire({
                        title: '¡Éxito!',
                        text: 'Biometría guardada correctamente.',
                        icon: 'success',
                        confirmButtonColor: '#39A900'
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
            }
        }

        function deleteFingerprint(id, name) {
            Swal.fire({
                title: '¿Eliminar huella?',
                text: `Se borrará el acceso biométrico de ${name}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch(`${DELETE_URL_TPL}/${id}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN }
                        });
                        const data = await response.json();
                        if (data.success) {
                            Swal.fire('Eliminado', data.message, 'success').then(() => location.reload());
                        }
                    } catch (error) {
                        Swal.fire('Error', 'No se pudo eliminar', 'error');
                    }
                }
            });
        }
    </script>
@endpush
