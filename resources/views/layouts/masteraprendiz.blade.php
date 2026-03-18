<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIEAP Aprendiz - @yield('title', 'Dashboard')</title>

    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700&display=swap"
        rel="stylesheet">

    <!-- Tailwind & Alpine -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        sena: '#39A900',
                        'sena-dark': '#2d8500',
                        'sena-light': '#4ade80',
                        premium: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c4b5fd',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95',
                        },
                        dark: {
                            sidebar: '#0f172a',
                            'sidebar-hover': '#1e293b',
                            'sidebar-active': '#1e293b',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                        outfit: ['Outfit', 'sans-serif'],
                    },
                    boxShadow: {
                        'premium': '0 10px 30px -10px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
                        'sena': '0 10px 15px -3px rgba(57, 169, 0, 0.2), 0 4px 6px -2px rgba(57, 169, 0, 0.1)',
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        h1,
        h2,
        h3,
        .font-outfit {
            font-family: 'Outfit', sans-serif;
        }

        .sena-gradient {
            background: linear-gradient(135deg, #39A900 0%, #2d8500 100%);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .sidebar-item-active {
            background: linear-gradient(90deg, rgba(57, 169, 0, 0.15) 0%, rgba(57, 169, 0, 0.05) 100%);
            border-left: 3px solid #39A900;
        }

        .sidebar-item-hover:hover {
            background: rgba(255, 255, 255, 0.05);
            transform: translateX(4px);
        }

        .fade-in {
            animation: fadeIn 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    @stack('styles')
</head>

<body class="antialiased bg-slate-50 text-slate-600 overflow-hidden" x-data="{ sidebarOpen: false }"
    @resize.window="if (window.innerWidth >= 1024) sidebarOpen = false">

    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar Backdrop (Mobile) -->
        <div class="fixed inset-0 bg-slate-900/40 z-40 lg:hidden lg:z-auto transition-opacity duration-300"
            x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="sidebarOpen = false" x-cloak aria-hidden="true"></div>

        <!-- Sidebar -->
        <aside id="sidebar"
            class="flex flex-col absolute z-50 left-0 top-0 lg:static lg:left-auto lg:top-auto lg:translate-x-0 h-screen overflow-y-auto no-scrollbar w-72 shrink-0 bg-white border-r border-slate-100 transition-all duration-300 ease-in-out shadow-2xl lg:shadow-none"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-72'">

            <!-- Sidebar header -->
            <div class="flex justify-between items-center px-6 h-20 border-b border-slate-100 bg-white">
                <!-- Close button (Mobile) -->
                <button class="lg:hidden text-slate-500 hover:text-slate-800 transition-colors"
                    @click="sidebarOpen = false">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <!-- Logo -->
                <a class="flex items-center gap-3 truncate group" href="{{ route('apprentice.dashboard') }}">
                    <div
                        class="w-10 h-10 sena-gradient rounded-xl flex items-center justify-center shadow-lg shadow-sena/20 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-6 shrink-0">
                        <i class="fas fa-graduation-cap text-white text-lg"></i>
                    </div>
                    <div>
                        <span
                            class="text-2xl font-bold text-slate-900 tracking-tight font-outfit block leading-none">SIEAP<span
                                class="text-sena">.</span></span>
                    </div>
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 px-4 py-8 space-y-8 overflow-y-auto no-scrollbar bg-white">
                <div>
                    <h3 class="text-[10px] uppercase text-slate-400 font-bold tracking-[0.2em] px-4 mb-6">
                        Panel de Aprendiz
                    </h3>
                    <ul class="space-y-1.5">
                        <!-- Dashboard -->
                        <li>
                            <a href="{{ route('apprentice.dashboard') }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('apprentice.dashboard*') ? 'bg-sena/10 text-sena shadow-inner' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 group' }}">
                                <i
                                    class="fas fa-th-large w-6 h-6 flex items-center justify-center transition-colors {{ request()->routeIs('apprentice.dashboard*') ? 'text-sena' : 'text-slate-500 group-hover:text-slate-700' }}"></i>
                                <span class="text-sm font-bold transition-opacity duration-300">Mi Resumen</span>
                            </a>
                        </li>

                        <!-- Mis Horarios -->
                        <li>
                            <a href="{{ route('apprentice.schedules.index') }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('apprentice.schedules.*') ? 'bg-sena/10 text-sena shadow-inner' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 group' }}">
                                <i
                                    class="fas fa-clock-rotate-left w-6 h-6 flex items-center justify-center transition-colors {{ request()->routeIs('apprentice.schedules.*') ? 'text-sena' : 'text-slate-500 group-hover:text-slate-700' }}"></i>
                                <span class="text-sm font-bold transition-opacity duration-300">Mis Horarios</span>
                            </a>
                        </li>

                        <!-- Mi Asistencia -->
                        <li x-data="{ open: {{ request()->routeIs('apprentice.attendance.*') ? 'true' : 'false' }} }">
                            <div
                                class="flex items-center justify-between gap-1 pr-2 rounded-xl transition-all duration-300 {{ request()->routeIs('apprentice.attendance.*') ? 'bg-sena/10 text-sena' : 'text-slate-600 hover:bg-slate-50 group' }}">
                                <a href="{{ route('apprentice.attendance.index') }}"
                                    class="flex-1 flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300">
                                    <i
                                        class="fas fa-calendar-check w-6 h-6 flex items-center justify-center transition-colors {{ request()->routeIs('apprentice.attendance.*') ? 'text-sena' : 'text-slate-500 group-hover:text-slate-700' }}"></i>
                                    <span class="text-sm font-bold transition-opacity duration-300">Mi Asistencia</span>
                                </a>
                                <button @click="open = !open" class="p-2 hover:bg-slate-50 rounded-lg transition-colors">
                                    <svg class="w-3 h-3 transition-transform duration-300 text-slate-700"
                                        :class="open ? 'rotate-180 text-sena' : ''" viewBox="0 0 12 12">
                                        <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" fill="currentColor" />
                                    </svg>
                                </button>
                            </div>
                            <ul class="ml-7 pl-7 mt-2 space-y-1 mb-2 border-l-2 border-slate-100" x-show="open"
                                x-cloak x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0">
                                <li>
                                    <a href="{{ route('apprentice.attendance.index') }}"
                                        class="group flex items-center py-2 text-[11px] font-bold {{ request()->routeIs('apprentice.attendance.index') ? 'text-sena' : 'text-slate-500 hover:text-slate-900' }} transition-all">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-3 border border-current opacity-40 group-hover:bg-current group-hover:opacity-100 transition-all"></span>
                                        Panel Diario
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('apprentice.attendance.logs') }}"
                                        class="group flex items-center py-2 text-[11px] font-bold {{ request()->routeIs('apprentice.attendance.logs') ? 'text-sena' : 'text-slate-500 hover:text-slate-900' }} transition-all">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-3 border border-current opacity-40 group-hover:bg-current group-hover:opacity-100 transition-all"></span>
                                        Mi Bitácora
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Certificados -->
                        <li>
                            <a href="{{ route('apprentice.certificates.index') }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('apprentice.certificates.*') ? 'bg-sena/10 text-sena shadow-inner' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 group' }}">
                                <i
                                    class="fas fa-certificate w-6 h-6 flex items-center justify-center transition-colors {{ request()->routeIs('apprentice.certificates.*') ? 'text-sena' : 'text-slate-500 group-hover:text-slate-700' }}"></i>
                                <span class="text-sm font-bold transition-opacity duration-300">Certificados</span>
                            </a>
                        </li>

                        <!-- Mi Progreso de Horas -->
                        <li>
                            <a href="{{ route('apprentice.hours.progress') }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('apprentice.hours.progress') ? 'bg-sena/10 text-sena shadow-inner' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 group' }}">
                                <div class="relative">
                                    <i class="fas fa-history text-lg transition-colors {{ request()->routeIs('apprentice.hours.progress') ? 'text-sena' : 'text-slate-500 group-hover:text-slate-700' }}"></i>
                                </div>
                                <span class="text-sm font-bold transition-opacity duration-300">Mi Progreso</span>
                            </a>
                        </li>

                        <!-- Cumplimiento / Deuda -->
                        <li
                            x-data="{ open: {{ (request()->routeIs('apprentice.penalties.*') || request()->routeIs('apprentice.recovery.*')) ? 'true' : 'false' }} }">
                            <div
                                class="flex items-center justify-between gap-1 pr-2 rounded-xl transition-all duration-300 {{ (request()->routeIs('apprentice.penalties.*') || request()->routeIs('apprentice.recovery.*')) ? 'bg-rose-500/10 text-rose-500' : 'text-slate-600 hover:bg-slate-50 group' }}">
                                <a href="{{ route('apprentice.penalties.index') }}"
                                    class="flex-1 flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300">
                                    <i
                                        class="fas fa-exclamation-triangle w-6 h-6 flex items-center justify-center transition-colors {{ (request()->routeIs('apprentice.penalties.*') || request()->routeIs('apprentice.recovery.*')) ? 'text-rose-500' : 'text-slate-500 group-hover:text-slate-700' }}"></i>
                                    <span class="text-sm font-bold transition-opacity duration-300">Cumplimiento</span>
                                </a>
                                <button @click="open = !open" class="p-2 hover:bg-slate-50 rounded-lg transition-colors">
                                    <svg class="w-3 h-3 transition-transform duration-300 text-slate-700"
                                        :class="open ? 'rotate-180 text-rose-500' : ''" viewBox="0 0 12 12">
                                        <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" fill="currentColor" />
                                    </svg>
                                </button>
                            </div>
                            <ul class="ml-7 pl-7 mt-2 space-y-1 mb-2 border-l-2 border-slate-100" x-show="open"
                                x-cloak x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0">
                                <li>
                                    <a href="{{ route('apprentice.penalties.index') }}"
                                        class="group flex items-center py-2 text-[11px] font-bold {{ request()->routeIs('apprentice.penalties.index') ? 'text-rose-500' : 'text-slate-500 hover:text-slate-900' }} transition-all">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-3 border border-current opacity-40 group-hover:bg-current group-hover:opacity-100 transition-all"></span>
                                        Deuda de Horas
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('apprentice.penalties.requests') }}"
                                        class="group flex items-center py-2 text-[11px] font-bold {{ request()->routeIs('apprentice.penalties.requests') ? 'text-rose-500' : 'text-slate-500 hover:text-slate-900' }} transition-all">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-3 border border-current opacity-40 group-hover:bg-current group-hover:opacity-100 transition-all"></span>
                                        Solicitudes
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('apprentice.recovery.index') }}"
                                        class="group flex items-center py-2 text-[11px] font-bold {{ request()->routeIs('apprentice.recovery.index') ? 'text-rose-500' : 'text-slate-500 hover:text-slate-900' }} transition-all">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-3 border border-current opacity-40 group-hover:bg-current group-hover:opacity-100 transition-all"></span>
                                        Recuperación
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Sidebar Footer -->
            <div class="p-4 border-t border-slate-100 bg-white">
                <div class="bg-slate-50 p-3 rounded-2xl flex items-center gap-3 border border-slate-100">
                    <div
                        class="w-9 h-9 rounded-xl sena-gradient flex items-center justify-center text-white font-bold text-sm shadow-lg shadow-sena/10 border border-white/10 overflow-hidden">
                        @if(Auth::user()->profile_photo_path)
                            <img src="{{ Storage::url(Auth::user()->profile_photo_path) }}" class="w-full h-full object-cover">
                        @else
                            {{ substr(Auth::user()->name, 0, 1) }}
                        @endif
                    </div>
                    <div class="flex-1 overflow-hidden text-center">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">
                            Aprendiz</p>
                        <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->name }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">

            <!-- Site Header -->
            <header
                class="sticky top-0 glass-effect border-b border-white z-30 h-16 flex items-center shrink-0 shadow-premium">
                <div class="px-4 sm:px-6 lg:px-8 w-full flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <!-- Hamburger button -->
                        <button class="text-slate-500 hover:text-sena lg:hidden transition-all active:scale-95"
                            @click.stop="sidebarOpen = true">
                            <i class="fas fa-bars-staggered text-xl"></i>
                        </button>
                        <h1 class="text-xl font-bold text-slate-800 tracking-tight font-outfit">
                            @yield('page-title', 'Inicio')</h1>
                    </div>

                    <!-- Right Side Header -->
                    <div class="flex items-center gap-3">
                        <!-- Notifications -->
                        <div class="relative group">
                            <button
                                class="w-10 h-10 flex items-center justify-center bg-white border border-slate-100 hover:border-sena/30 rounded-xl text-slate-400 transition-all hover:shadow-lg active:scale-90 relative">
                                <i class="fas fa-bell group-hover:text-sena transition-colors"></i>
                                <span
                                    class="absolute top-2.5 right-2.5 w-2 h-2 bg-sena border-2 border-white rounded-full"></span>
                            </button>
                        </div>

                        <div class="w-px h-6 bg-slate-200 mx-2"></div>

                        <!-- User Profile Dropdown -->
                        <div class="relative" x-data="{ userMenu: false }">
                            <button @click="userMenu = !userMenu"
                                class="flex items-center gap-3 group px-2 py-1.5 rounded-xl hover:bg-white transition-all hover:shadow-md border border-transparent hover:border-slate-100">
                                <div
                                    class="w-9 h-9 rounded-xl sena-gradient flex items-center justify-center text-white font-bold text-sm shadow-md border-2 border-white overflow-hidden">
                                    @if(Auth::user()->profile_photo_path)
                                        <img src="{{ Storage::url(Auth::user()->profile_photo_path) }}" class="w-full h-full object-cover">
                                    @else
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    @endif
                                </div>
                                <div class="text-left hidden sm:block">
                                    <p class="text-[12px] font-bold text-slate-800 leading-none mb-0.5">
                                        {{ Auth::user()->name }}
                                    </p>
                                    <p class="text-[10px] font-bold text-sena uppercase tracking-wider opacity-80">
                                        Aprendiz</p>
                                </div>
                                <i class="fas fa-chevron-down text-[10px] text-slate-300 transition-transform duration-300"
                                    :class="userMenu ? 'rotate-180 text-sena' : ''"></i>
                            </button>
                            <!-- Dropdown content -->
                            <div class="absolute top-full right-0 w-52 bg-white border border-slate-100 shadow-xl rounded-2xl mt-2 p-2 z-50 overflow-hidden"
                                x-show="userMenu" x-cloak x-transition @click.outside="userMenu = false">
                                <a href="{{ route('profile.edit') }}"
                                    class="flex items-center gap-3 px-3 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-sena rounded-lg transition-all">
                                    <i class="fas fa-user-circle opacity-50 text-base"></i>
                                    Mi Perfil
                                </a>
                                <div class="my-1 border-t border-slate-50"></div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full flex items-center gap-3 px-3 py-2.5 text-xs font-bold text-rose-500 hover:bg-rose-50 rounded-lg transition-all text-left">
                                        <i class="fas fa-power-off opacity-50 text-base"></i>
                                        Cerrar Sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <div class="w-full max-w-[1400px] mx-auto">
                    <!-- Breadcrumbs -->
                    <div
                        class="flex items-center gap-2 mb-8 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                        <a href="{{ route('apprentice.dashboard') }}"
                            class="hover:text-sena transition-colors uppercase">Sistema</a>
                        <i class="fas fa-chevron-right text-[8px] opacity-30"></i>
                        @yield('breadcrumb')
                    </div>

                    <!-- Content -->
                    <div class="fade-in">
                        @yield('content')
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="p-8 text-center border-t border-slate-100">
                <p class="text-[10px] font-bold text-slate-300 uppercase tracking-[0.2em]">&copy; {{ date('Y') }} SIEAP
                    - SENA BIOPROP</p>
            </footer>
        </div>
    </div>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#39A900',
                    customClass: { popup: 'rounded-[2rem]' }
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: '¡Atención!',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#F43F5E',
                    customClass: { popup: 'rounded-[2rem]' }
                });
            @endif

            @if(session('info'))
                Swal.fire({
                    icon: 'info',
                    title: 'Información',
                    text: "{{ session('info') }}",
                    confirmButtonColor: '#39A900',
                    customClass: { popup: 'rounded-[2rem]' }
                });
            @endif
        });
    </script>
</body>

</html>