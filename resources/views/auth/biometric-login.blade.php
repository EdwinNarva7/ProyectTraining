<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Acceso Biométrico - SIEAP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
        }

        .sena-gradient {
            background: linear-gradient(135deg, #39A900 0%, #2d8500 100%);
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.05);
        }

        .scanner-container {
            position: relative;
            width: 240px;
            height: 240px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .scanner-circle {
            position: absolute;
            inset: 0;
            border: 3px solid rgba(57, 169, 0, 0.15);
            border-radius: 40%;
            animation: rotate 10s linear infinite;
            box-shadow: inset 0 0 20px rgba(57, 169, 0, 0.05);
        }

        .scanner-circle-inner {
            position: absolute;
            inset: 25px;
            border: 3px dashed rgba(57, 169, 0, 0.3);
            border-radius: 40%;
            animation: rotate-reverse 15s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes rotate-reverse {
            from { transform: rotate(360deg); }
            to { transform: rotate(0deg); }
        }

        .fingerprint-icon {
            font-size: 90px;
            color: #39A900;
            filter: drop-shadow(0 0 10px rgba(57, 169, 0, 0.2));
            transition: all 0.5s ease;
        }

        .scan-line {
            position: absolute;
            top: 0;
            left: 10%;
            width: 80%;
            height: 4px;
            background: linear-gradient(to right, transparent, #39A900, transparent);
            box-shadow: 0 0 20px #39A900, 0 0 40px #39A900;
            border-radius: 50%;
            opacity: 0;
            z-index: 30;
        }

        .scanning .scan-line {
            animation: scan 2s ease-in-out infinite;
            opacity: 1;
        }

        .scanning .scanner-circle {
            border-color: rgba(57, 169, 0, 0.4);
            box-shadow: 0 0 30px rgba(57, 169, 0, 0.2);
        }

        .scanning .scanner-circle-inner {
            border-color: rgba(57, 169, 0, 0.6);
        }

        .scanning #fpVisualBox {
            background: rgba(57, 169, 0, 0.05);
            border-color: rgba(57, 169, 0, 0.3);
            box-shadow: 0 0 50px rgba(57, 169, 0, 0.15);
        }

        @keyframes scan {
            0%, 100% { top: 25%; }
            50% { top: 75%; }
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ef4444;
            box-shadow: 0 0 10px rgba(239, 68, 68, 0.3);
        }

        .status-dot.online {
            background: #39A900;
            box-shadow: 0 0 10px rgba(57, 169, 0, 0.3);
        }

        .btn-tech {
            background: white;
            border: 1px solid #e2e8f0;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .btn-tech:hover {
            border-color: #39A900;
            color: #39A900;
            box-shadow: 0 10px 25px -5px rgba(57, 169, 0, 0.15);
            transform: translateY(-4px);
        }

        .btn-tech i {
            transition: transform 0.3s ease;
        }

        .btn-tech:hover i {
            transform: scale(1.1);
        }

        @keyframes pulse-soft {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 1; }
        }

        .pulse-text {
            animation: pulse-soft 2s infinite;
        }
    </style>
</head>

<body class="text-slate-800 antialiased overflow-hidden">
    <!-- Fondo Decorativo Claro -->
    <div class="fixed inset-0 z-0">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-[#39A900]/5 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-600/5 rounded-full blur-[120px]"></div>
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/pinstriped-suit.png')] opacity-[0.03]"></div>
    </div>

    <div class="min-h-screen flex flex-col items-center justify-center p-6 relative z-10">
        
        <!-- Navbar Superior -->
        <div class="absolute top-0 w-full p-8 flex justify-between items-center max-w-7xl mx-auto">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 sena-gradient rounded-2xl flex items-center justify-center shadow-lg shadow-sena/20">
                    <i class="fas fa-fingerprint text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black tracking-tighter leading-none text-slate-900">SIEAP<span class="text-sena">.</span></h1>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Biometric Access Terminal</p>
                </div>
            </div>
            
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-full border border-slate-100 shadow-sm">
                    <div id="bridgeStatusDot" class="status-dot"></div>
                    <span id="bridgeStatusText" class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Bridge Offline</span>
                </div>
                <a href="{{ route('login') }}" class="text-sm font-bold text-slate-500 hover:text-sena transition-colors flex items-center gap-2">
                    <i class="fas fa-sign-in-alt"></i> Regresar al Login
                </a>
            </div>
        </div>

        <!-- Contenedor Principal -->
        <div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-2 gap-16 items-center bg-white/40 p-12 rounded-[4rem] border border-white/60 backdrop-blur-sm shadow-2xl shadow-slate-200/50">
            
            <!-- Izquierda: Información y Estado -->
            <div class="space-y-8 text-center lg:text-left">
                <div class="inline-flex px-4 py-1.5 bg-sena/10 text-sena rounded-full text-[10px] font-black uppercase tracking-[0.3em] border border-sena/20">
                    Security Level 04
                </div>
                <h2 class="text-5xl lg:text-6xl font-black text-slate-900 leading-[1.1] tracking-tight font-outfit uppercase">
                    Acceso <br><span class="text-sena">Biométrico</span>
                </h2>
                <p class="text-slate-500 text-lg max-w-md mx-auto lg:mx-0 leading-relaxed font-medium">
                    Terminal de autenticación segura. Por favor, coloque su huella en el lector para validar su identidad.
                </p>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4">
                    <button onclick="initiateFingerprintScan('system')" class="btn-tech p-8 rounded-[2.5rem] flex flex-col items-center gap-4 group">
                        <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center group-hover:bg-sena/10 transition-colors">
                            <i class="fas fa-desktop text-2xl text-slate-400 group-hover:text-sena transition-colors"></i>
                        </div>
                        <span class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-600 group-hover:text-sena transition-colors">Ingresar Sistema</span>
                    </button>
                    <button onclick="initiateFingerprintScan('attendance')" class="btn-tech p-8 rounded-[2.5rem] flex flex-col items-center gap-4 group">
                        <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center group-hover:bg-sena/10 transition-colors">
                            <i class="fas fa-clock text-2xl text-slate-400 group-hover:text-sena transition-colors"></i>
                        </div>
                        <span class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-600 group-hover:text-sena transition-colors">Marcar Asistencia</span>
                    </button>
                </div>
            </div>

            <!-- Derecha: El Escáner Visual -->
            <div class="flex flex-col items-center justify-center">
                <div id="scannerWrapper" class="scanner-container">
                    <div class="scanner-circle"></div>
                    <div class="scanner-circle-inner"></div>
                    <div class="scan-line"></div>
                    <div id="fpVisualBox" class="relative z-20 w-44 h-44 bg-white rounded-[3rem] flex items-center justify-center border border-slate-100 shadow-xl transition-all duration-500">
                        <i id="fpMainIcon" class="fas fa-fingerprint fingerprint-icon"></i>
                        <i id="fpCheckIcon" class="fas fa-check text-7xl text-sena hidden"></i>
                        <i id="fpErrorIcon" class="fas fa-times text-7xl text-rose-500 hidden"></i>
                    </div>
                </div>
                
                <div class="mt-16 text-center space-y-3">
                    <p id="scannerStatusMsg" class="text-xs font-black uppercase tracking-[0.4em] text-slate-400 pulse-text">Esperando Lector...</p>
                    <div class="flex justify-center gap-1.5">
                        <div class="w-1.5 h-1.5 bg-sena rounded-full animate-bounce"></div>
                        <div class="w-1.5 h-1.5 bg-sena rounded-full animate-bounce [animation-delay:0.2s]"></div>
                        <div class="w-1.5 h-1.5 bg-sena rounded-full animate-bounce [animation-delay:0.4s]"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Footer -->
        <div class="absolute bottom-8 w-full text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.4em]">SIEAP Digital Identity System &copy; 2026</p>
        </div>
    </div>

    <!-- Modal de Proceso -->
    <div id="fingerprintScanModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 backdrop-blur-md p-6">
        <div class="glass-panel w-full max-w-sm rounded-[3.5rem] p-12 text-center border border-white">
            <div class="mb-10 flex justify-center">
                <div class="w-20 h-20 bg-sena/10 rounded-3xl flex items-center justify-center text-sena shadow-inner">
                    <i id="modalStatusIcon" class="fas fa-spinner fa-spin text-3xl"></i>
                </div>
            </div>
            <h3 id="modalTitle" class="text-2xl font-black text-slate-900 uppercase tracking-tighter mb-3 leading-none font-outfit">Escaneando...</h3>
            <p id="modalMsg" class="text-slate-500 text-sm font-medium mb-12 leading-relaxed">Mantenga el dedo en el lector hasta completar el proceso.</p>
            <button onclick="cancelFingerprintScan()" class="w-full py-5 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-500 rounded-2xl text-[10px] font-black uppercase tracking-[0.3em] transition-all border border-slate-200/50">
                Cancelar Operación
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const BRIDGE_URL = 'http://localhost:8080';
        const FP_LOGIN_URL = '{{ route("biometric.fingerprint-login") }}';
        const FP_MARK_URL = '{{ route("fingerprint.mark") }}';
        const TEMPLATES_URL = '{{ route("fingerprint.templates") }}';
        
        let fpScanActive = false;
        let lastCapturedTemplate = null;
        let scanMode = 'system';

        // Verificar estado del Bridge periódicamente
        async function checkBridge() {
            try {
                const res = await fetch(`${BRIDGE_URL}/status`, { signal: AbortSignal.timeout(1000) });
                if(res.ok) {
                    document.getElementById('bridgeStatusDot').classList.add('online');
                    document.getElementById('bridgeStatusText').textContent = 'Bridge Online';
                    document.getElementById('bridgeStatusText').classList.replace('text-slate-400', 'text-sena');
                }
            } catch (err) {
                document.getElementById('bridgeStatusDot').classList.remove('online');
                document.getElementById('bridgeStatusText').textContent = 'Bridge Offline';
                document.getElementById('bridgeStatusText').classList.replace('text-sena', 'text-slate-400');
            }
        }
        setInterval(checkBridge, 3000);
        checkBridge();

        async function initiateFingerprintScan(mode) {
            scanMode = mode;
            const scannerWrapper = document.getElementById('scannerWrapper');
            const msg = document.getElementById('scannerStatusMsg');
            const mainIcon = document.getElementById('fpMainIcon');
            const checkIcon = document.getElementById('fpCheckIcon');
            const errorIcon = document.getElementById('fpErrorIcon');

            // Reset UI
            mainIcon.classList.remove('hidden');
            checkIcon.classList.add('hidden');
            errorIcon.classList.add('hidden');
            scannerWrapper.classList.add('scanning');
            msg.textContent = 'Iniciando Escaneo...';
            msg.classList.remove('text-slate-400');
            msg.classList.add('text-sena');

            try {
                const statusRes = await fetch(`${BRIDGE_URL}/status`, { signal: AbortSignal.timeout(1500) });
                await statusRes.json();
                await fetch(`${BRIDGE_URL}/reset`, { method: 'POST' }).catch(() => {});
                
                fpScanActive = true;
                msg.textContent = 'Coloque su dedo en el lector';
                startFingerprintIdentification();
            } catch (err) {
                scannerWrapper.classList.remove('scanning');
                msg.textContent = 'Lector no detectado';
                msg.classList.replace('text-sena', 'text-rose-500');
                
                Swal.fire({
                    title: 'Terminal no encontrada',
                    text: 'El software puente para el lector biométrico no está en ejecución.',
                    icon: 'warning',
                    background: '#1e293b',
                    color: '#f8fafc',
                    confirmButtonColor: '#39A900'
                });
            }
        }

        async function startFingerprintIdentification() {
            const scannerWrapper = document.getElementById('scannerWrapper');
            const msg = document.getElementById('scannerStatusMsg');
            const mainIcon = document.getElementById('fpMainIcon');
            const checkIcon = document.getElementById('fpCheckIcon');
            const errorIcon = document.getElementById('fpErrorIcon');

            const pollStart = Date.now();
            const pollInterval = setInterval(async () => {
                if (!fpScanActive) {
                    clearInterval(pollInterval);
                    return;
                }

                try {
                    const captureRes = await fetch(`${BRIDGE_URL}/capture`, { signal: AbortSignal.timeout(800) });
                    if (!captureRes.ok) return;

                    const captureData = await captureRes.json();
                    if (captureData.status !== 'success' || !captureData.template_base64) return;
                    if (captureData.template_base64 === lastCapturedTemplate) return;

                    lastCapturedTemplate = captureData.template_base64;
                    msg.textContent = 'Procesando Identidad...';

                    const templatesRes = await fetch(TEMPLATES_URL);
                    const tplData = await templatesRes.json();
                    
                    if (!tplData.success || !tplData.templates || tplData.templates.length === 0) {
                        throw new Error('No templates found');
                    }

                    const identifyRes = await fetch(`${BRIDGE_URL}/identify`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            captured_template: captureData.template_base64,
                            enrolled_templates: tplData.templates
                        })
                    });
                    
                    const identifyData = await identifyRes.json();
                    clearInterval(pollInterval);
                    fpScanActive = false;
                    scannerWrapper.classList.remove('scanning');

                    if (identifyData.matched && identifyData.apprentice_id) {
                        mainIcon.classList.add('hidden');
                        checkIcon.classList.remove('hidden');
                        msg.textContent = 'Acceso Autorizado';
                        msg.classList.remove('text-sena');
                        msg.classList.add('text-emerald-500');
                        
                        setTimeout(() => authenticateWithFingerprint(identifyData.apprentice_id), 1000);
                    } else {
                        mainIcon.classList.add('hidden');
                        errorIcon.classList.remove('hidden');
                        msg.textContent = 'Identidad no Reconocida';
                        msg.classList.remove('text-sena');
                        msg.classList.add('text-rose-500');
                        
                        setTimeout(() => {
                            mainIcon.classList.remove('hidden');
                            errorIcon.classList.add('hidden');
                            msg.textContent = 'Esperando Lector...';
                            msg.classList.remove('text-rose-500');
                            msg.classList.add('text-slate-400');
                        }, 3000);
                    }
                } catch (err) { }

                if (Date.now() - pollStart > 30000) {
                    clearInterval(pollInterval);
                    fpScanActive = false;
                    scannerWrapper.classList.remove('scanning');
                    msg.textContent = 'Tiempo de espera agotado';
                }
            }, 500);
        }

        function authenticateWithFingerprint(userId) {
            const url = scanMode === 'attendance' ? FP_MARK_URL : FP_LOGIN_URL;
            
            // Ajustar el payload según el endpoint (asistencia usa apprentice_id, login usa user_id)
            const payload = scanMode === 'attendance' 
                ? { apprentice_id: userId } 
                : { user_id: userId, mode: scanMode };

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: scanMode === 'attendance' ? 'Asistencia Registrada' : 'Bienvenido',
                        text: data.message,
                        icon: 'success',
                        background: '#1e293b',
                        color: '#f8fafc',
                        confirmButtonColor: '#39A900',
                        timer: 3000,
                        timerProgressBar: true,
                        willClose: () => {
                            if (data.redirect) window.location.href = data.redirect;
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error de Validación',
                        text: data.message,
                        icon: 'error',
                        background: '#1e293b',
                        color: '#f8fafc',
                        confirmButtonColor: '#39A900'
                    });
                }
            })
            .catch(err => {
                console.error('Error de red:', err);
                Swal.fire({
                    title: 'Error de Red',
                    text: 'No se pudo comunicar con el servidor central. Verifique su conexión.',
                    icon: 'error',
                    background: '#1e293b',
                    color: '#f8fafc',
                    confirmButtonColor: '#39A900'
                });
            });
        }
    </script>
</body>
</html>
