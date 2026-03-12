<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Aprendiz - SIEAP</title>
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

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) rotate(0);
            }

            50% {
                transform: translateY(-10px) rotate(2deg);
            }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
    </style>
</head>

<body class="bg-slate-50 overflow-hidden">
    <div class="min-h-screen flex">
        <!-- Columna Izquierda - Formulario -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-12 relative overflow-y-auto">
            <!-- Círculos Decorativos de fondo -->
            <div
                class="absolute top-0 right-0 w-64 h-64 bg-green-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 translate-x-1/2 -translate-y-1/2">
            </div>
            <div
                class="absolute bottom-0 left-0 w-96 h-96 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 -translate-x-1/4 translate-y-1/4">
            </div>

            <div class="w-full max-w-md relative z-10 py-10">
                <!-- Header con logo -->
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-12 h-12 sena-gradient rounded-2xl flex items-center justify-center shadow-lg transform -rotate-3">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-2xl font-bold text-slate-800 tracking-tight">SIEAP<span
                                    class="text-[#39A900]">.</span></span>
                            <p class="text-[10px] text-slate-400 font-medium uppercase tracking-[0.2em] leading-none">
                                Nueva Cuenta Aprendiz</p>
                        </div>
                    </div>
                </div>

                <!-- Título -->
                <div class="mb-8">
                    <h1 class="text-4xl font-bold text-slate-900 mb-3 tracking-tight">
                        Crea tu cuenta<span class="text-[#39A900]">.</span>
                    </h1>
                    <p class="text-slate-500 text-sm">
                        Únete al sistema de seguimiento y agiliza tus registros de asistencia.
                    </p>
                </div>

                <!-- Formulario -->
                <form action="{{ route('register') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Nombre Completo -->
                    <div>
                        <label for="name"
                            class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 ml-1">Nombre
                            Completo</label>
                        <div class="relative group">
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                                placeholder="Juan Pérez"
                                class="w-full px-5 py-3.5 bg-white border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 outline-none transition-all duration-300 focus:ring-4 focus:ring-green-500/10 focus:border-[#39A900] shadow-sm group-hover:border-slate-300">
                            <div
                                class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-[#39A900] transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        </div>
                        @error('name')
                            <p class="mt-2 text-xs text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email"
                            class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 ml-1">Correo
                            Institucional</label>
                        <div class="relative group">
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                placeholder="aprendiz@soy.sena.edu.co"
                                class="w-full px-5 py-3.5 bg-white border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 outline-none transition-all duration-300 focus:ring-4 focus:ring-green-500/10 focus:border-[#39A900] shadow-sm group-hover:border-slate-300">
                            <div
                                class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-[#39A900] transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                        @error('email')
                            <p class="mt-2 text-xs text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Password -->
                        <div>
                            <label for="password"
                                class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 ml-1">Contraseña</label>
                            <div class="relative group">
                                <input type="password" name="password" id="password" required placeholder="••••••••"
                                    class="w-full px-5 py-3.5 bg-white border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 outline-none transition-all duration-300 focus:ring-4 focus:ring-green-500/10 focus:border-[#39A900] shadow-sm group-hover:border-slate-300">
                                <div
                                    class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-[#39A900] transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation"
                                class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 ml-1">Confirmar</label>
                            <div class="relative group">
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                    placeholder="••••••••"
                                    class="w-full px-5 py-3.5 bg-white border border-slate-200 rounded-2xl text-slate-900 placeholder-slate-400 outline-none transition-all duration-300 focus:ring-4 focus:ring-green-500/10 focus:border-[#39A900] shadow-sm group-hover:border-slate-300">
                                <div
                                    class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-[#39A900] transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                    @enderror

                    <!-- Botón de Registro -->
                    <div class="pt-4">
                        <button type="submit"
                            class="w-full py-4 sena-gradient text-white rounded-2xl font-bold text-lg transition-all duration-300 hover:shadow-[0_10px_25px_-5px_rgba(57,169,0,0.4)] active:scale-[0.98] shadow-lg flex items-center justify-center gap-2">
                            <span>Registrarse en SIEAP</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>

                    <p class="text-center text-slate-500 text-sm mt-8">
                        ¿Ya tienes una cuenta?
                        <a href="{{ route('login') }}" class="text-[#39A900] font-bold hover:underline">Inicia
                            sesión</a>
                    </p>
                </form>

                <!-- Footer -->
                <div
                    class="mt-12 pt-8 border-t border-slate-100 flex justify-between items-center text-[11px] text-slate-400 font-medium uppercase tracking-widest">
                    <span>© {{ date('Y') }} SENA - SIEAP</span>
                    <div class="flex gap-4">
                        <a href="#" class="hover:text-slate-600 transition-colors">Ayuda</a>
                        <a href="#" class="hover:text-slate-600 transition-colors">Términos</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Derecha - Imagen Visual -->
        <div class="hidden lg:block lg:w-1/2 relative">
            <div class="absolute inset-0">
                <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=2070&auto=format&fit=crop"
                    alt="Aprendices SENA" class="w-full h-full object-cover">
                <!-- Overlay con gradiente -->
                <div
                    class="absolute inset-0 bg-gradient-to-br from-[#39A900]/90 via-[#39A900]/50 to-transparent mix-blend-multiply">
                </div>

                <!-- Contenido sobre la imagen -->
                <div class="absolute inset-0 flex flex-col justify-center p-16 text-white">
                    <div class="max-w-md">
                        <div class="w-16 h-1 bg-white mb-8"></div>
                        <h2 class="text-6xl font-bold leading-tight mb-6">Tu futuro empieza aquí.</h2>
                        <p class="text-xl text-white/90 font-light leading-relaxed mb-8">
                            El registro formal de tu asistencia es el primer paso hacia el cumplimiento de tus metas
                            académicas en el SENA.
                        </p>

                        <div class="grid grid-cols-2 gap-6 mt-12">
                            <div class="glass-panel p-6 rounded-3xl border-white/20">
                                <p class="text-3xl font-bold mb-1">100%</p>
                                <p class="text-xs uppercase tracking-wider font-semibold opacity-80">Digital</p>
                            </div>
                            <div class="glass-panel p-6 rounded-3xl border-white/20">
                                <p class="text-3xl font-bold mb-1">Rápido</p>
                                <p class="text-xs uppercase tracking-wider font-semibold opacity-80">Seguro</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Elemento decorativo flotante -->
                <div
                    class="absolute bottom-12 right-12 glass-panel p-6 rounded-3xl shadow-2xl flex items-center gap-4 animate-float">
                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-inner">
                        <svg class="w-7 h-7 text-[#39A900]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Validación SENA</p>
                        <p class="text-slate-800 font-bold">Registro Oficial</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>