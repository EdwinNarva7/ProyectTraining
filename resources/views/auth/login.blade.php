<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - SIEAP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .sena-gradient {
            background: linear-gradient(135deg, #39A900 0%, #2d8500 100%);
        }

        .sena-text-gradient {
            background: linear-gradient(135deg, #39A900 0%, #2d8500 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .input-focus-ring:focus {
            ring-color: #39A900;
            border-color: #39A900;
        }

        @keyframes zoom-in {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .animate-zoom-in { animation: zoom-in 0.3s ease-out; }

        @keyframes spin-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .animate-spin-slow { animation: spin-slow 8s linear infinite; }

        .fp-pulse {
            animation: fp-pulse 2s infinite;
        }

        @keyframes fp-pulse {
            0% { box-shadow: 0 0 0 0 rgba(57, 169, 0, 0.4); }
            70% { box-shadow: 0 0 0 15px rgba(57, 169, 0, 0); }
            100% { box-shadow: 0 0 0 0 rgba(57, 169, 0, 0); }
        }

        @keyframes scan-line {
            0% { top: 0%; opacity: 0; }
            50% { opacity: 0.8; }
            100% { top: 100%; opacity: 0; }
        }

        .scan-line {
            position: absolute;
            left: 0;
            width: 100%;
            height: 3px;
            background: rgba(57, 169, 0, 0.8);
            box-shadow: 0 0 15px rgba(57, 169, 0, 0.8);
            animation: scan-line 2s infinite linear;
            z-index: 20;
        }
    </style>
</head>

<body class="bg-slate-50 overflow-hidden">
    <div class="min-h-screen flex">
        <!-- Columna Izquierda - Formulario -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-12 relative overflow-y-auto">
            <!-- Círculos Decorativos de fondo -->
            <div
                class="absolute top-0 left-0 w-64 h-64 bg-green-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 -translate-x-1/2 -translate-y-1/2">
            </div>
            <div
                class="absolute bottom-0 right-0 w-96 h-96 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 translate-x-1/4 translate-y-1/4">
            </div>

            <div class="w-full max-w-md relative z-10">
                <!-- Header con logo -->
                <div class="flex items-center justify-between mb-12">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-12 h-12 sena-gradient rounded-2xl flex items-center justify-center shadow-lg transform rotate-3">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-2xl font-bold text-slate-800 tracking-tight">SIEAP<span
                                    class="text-[#39A900]">.</span></span>
                            <p class="text-[10px] text-slate-400 font-medium uppercase tracking-[0.2em] leading-none">
                                Sistema de Asistencia</p>
                        </div>
                    </div>
                </div>

                <!-- Título -->
                <div class="mb-10">
                    <h1 class="text-4xl font-bold text-slate-900 mb-3 tracking-tight">
                        Bienvenido de nuevo<span class="text-[#39A900]">.</span>
                    </h1>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-lg border border-green-100">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Formulario -->
                <form action="{{ route('login') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email"
                            class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 ml-1">Correo
                            Electrónico</label>
                        <div class="relative group">
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                                placeholder="ejemplo@sena.edu.co"
                                class="w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 outline-none transition-all duration-300 focus:ring-4 focus:ring-green-500/10 focus:border-[#39A900] shadow-sm group-hover:border-slate-300">
                            <div
                                class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-[#39A900] transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                                </svg>
                            </div>
                        </div>
                        @error('email')
                            <p class="mt-2 text-xs text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-2 px-1">
                            <label for="password"
                                class="block text-xs font-semibold text-slate-500 uppercase tracking-wider">Contraseña</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                    class="text-[11px] text-[#39A900] hover:text-[#2d8500] font-bold transition-colors">
                                    ¿Olvidaste tu contraseña?
                                </a>
                            @endif
                        </div>
                        <div class="relative group">
                            <input type="password" name="password" id="password" required placeholder="••••••••••••"
                                class="w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 outline-none transition-all duration-300 focus:ring-4 focus:ring-green-500/10 focus:border-[#39A900] shadow-sm group-hover:border-slate-300">
                            <button type="button" onclick="togglePassword()"
                                class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-300 hover:text-slate-600 transition-colors">
                                <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-xs text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Recordarme -->
                    <div class="flex items-center px-1">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" name="remember"
                                class="w-4 h-4 text-[#39A900] border-slate-300 rounded focus:ring-[#39A900] focus:ring-offset-0 transition">
                            <span
                                class="ml-3 text-sm text-slate-500 group-hover:text-slate-700 transition-colors">Mantener
                                sesión iniciada</span>
                        </label>
                    </div>

                    <!-- Botón de entrada -->
                    <div class="pt-4">
                        <button type="submit"
                            class="w-full py-4 sena-gradient text-white rounded-2xl font-bold text-lg transition-all duration-300 hover:shadow-[0_10px_25px_-5px_rgba(57,169,0,0.4)] active:scale-[0.98] shadow-lg flex items-center justify-center gap-2">
                            <span>Ingresar al Sistema</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </button>
                    </div>

                    @if (Route::has('register'))
                        <p class="text-center text-slate-500 text-sm mt-8">
                            ¿No tienes una cuenta?
                            <a href="{{ route('register') }}" class="text-[#39A900] font-bold hover:underline">Regístrate
                                ahora</a>
                        </p>
                    @endif
                </form>

                <!-- Separator -->
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-slate-50 text-slate-500 font-medium">¿Prefieres acceso biométrico?</span>
                    </div>
                </div>

                <!-- Botón de Ingreso Biométrico -->
                <a href="{{ route('biometric.panel') }}"
                    class="flex items-center justify-center gap-3 w-full py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-[#39A900]/20 hover:border-[#39A900]/50 rounded-2xl transition-all duration-300 hover:shadow-lg active:scale-[0.98] group">
                    <div class="w-10 h-10 rounded-xl bg-[#39A900]/10 flex items-center justify-center text-[#39A900] group-hover:bg-[#39A900] group-hover:text-white transition-all">
                        <i class="fas fa-fingerprint text-base"></i>
                    </div>
                    <div class="text-left">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider leading-none">Alternativa</p>
                        <p class="text-[#39A900] font-bold text-base leading-none group-hover:text-[#2d8500]">Ingreso Biométrico</p>
                    </div>
                    <i class="fas fa-chevron-right text-[#39A900] ml-auto opacity-60 group-hover:opacity-100 transition-opacity"></i>
                </a>
            </div>
        </div>

        <!-- Modal de Escaneo de Huella - Estética Industrial -->
        <!-- REMOVIDO: Modal de biometría ahora está en biometric-login.blade.php -->

        <!-- Columna Derecha - Imagen Visual -->
        <div class="hidden lg:block lg:w-1/2 relative">
            <div class="absolute inset-0">
                <img src="https://images.unsplash.com/photo-1571260899304-425eee4c7efc?q=80&w=2070&auto=format&fit=crop"
                    alt="SENA Center" class="w-full h-full object-cover">
                <!-- Overlay con gradiente -->
                <div
                    class="absolute inset-0 bg-gradient-to-tr from-[#39A900]/80 via-[#39A900]/40 to-transparent mix-blend-multiply">
                </div>

                <!-- Contenido sobre la imagen -->
                <div
                    class="absolute inset-0 flex flex-col justify-end p-16 text-white bg-gradient-to-t from-black/80 to-transparent">
                    <div class="max-w-md">
                        <div class="w-16 h-1 bg-[#39A900] mb-6"></div>
                        <h2 class="text-5xl font-bold leading-tight mb-4">Control Inteligente de Aprendices.</h2>
                        <p class="text-lg text-slate-200 font-light leading-relaxed">
                            Gestione las entradas y salidas de manera eficiente con nuestro sistema automatizado
                            diseñado para la excelencia institucional.
                        </p>
                    </div>
                </div>

                <!-- Elementos decorativos abstractos -->
                <div
                    class="absolute top-12 right-12 glass-panel p-6 rounded-3xl shadow-2xl flex items-center gap-4 animate-bounce-slow">
                    <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Acceso Seguro</p>
                        <p class="text-slate-800 font-bold">Autenticación Activa</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Toggle para mostrar/ocultar contraseña
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            }
        }
    </script>
    <style>
        @keyframes bounce-slow {
            0%, 100% {
                transform: translateY(-5%);
                animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
            }
            50% {
                transform: none;
                animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
            }
        }
        .animate-bounce-slow {
            animation: bounce-slow 4s infinite;
        }
    </style>
</body>

</html>