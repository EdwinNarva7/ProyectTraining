<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIEAP - @yield('title', 'Dashboard')</title>

    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700&display=swap"
        rel="stylesheet">

    <!-- Tailwind & Alpine (CDN for instant functionality) -->
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

        .premium-gradient {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
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

        .nav-glow:hover {
            box-shadow: 0 0 15px rgba(57, 169, 0, 0.3);
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
                <a class="flex items-center gap-3 truncate group" href="{{ route('admin.dashboard') }}">
                    <div
                        class="w-10 h-10 sena-gradient rounded-xl flex items-center justify-center shadow-lg shadow-sena/20 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-6 shrink-0">
                        <span class="text-white font-bold text-xl">S</span>
                    </div>
                    <span class="text-2xl font-bold text-slate-900 tracking-tight font-outfit">SIEAP<span
                            class="text-sena">.</span></span>
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 px-4 py-8 space-y-8 overflow-y-auto no-scrollbar bg-white">
                <div>
                    <h3 class="text-[10px] uppercase text-slate-400 font-bold tracking-[0.2em] px-4 mb-6">
                        Navegación
                    </h3>
                    <ul class="space-y-1.5">
                        <!-- Dashboard -->
                        <li>
                            <a href="{{ route('admin.dashboard') }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.dashboard*') ? 'bg-sena/10 text-sena shadow-inner' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 group' }}">
                                <div class="relative">
                                    <svg class="shrink-0 w-6 h-6 transition-colors {{ request()->routeIs('admin.dashboard*') ? 'text-sena' : 'text-slate-500 group-hover:text-slate-700' }}"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    @if(request()->routeIs('admin.dashboard*'))
                                        <span
                                            class="absolute -top-1 -right-1 w-2 h-2 bg-sena rounded-full animate-ping"></span>
                                    @endif
                                </div>
                                <span class="text-sm font-bold transition-opacity duration-300">Dashboard</span>
                            </a>
                        </li>

                        <!-- Usuarios -->
                        <li x-data="{ open: {{ request()->routeIs('admin.users.*') ? 'true' : 'false' }} }">
                            <div
                                class="flex items-center justify-between gap-1 pr-2 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.users.*') ? 'bg-sena/10 text-sena' : 'text-slate-600 hover:bg-slate-50 group' }}">
                                <a href="{{ route('admin.users.index') }}"
                                    class="flex-1 flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300">
                                    <svg class="shrink-0 w-6 h-6 transition-colors {{ request()->routeIs('admin.users.*') ? 'text-sena' : 'text-slate-500 group-hover:text-slate-700' }}"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <span class="text-sm font-bold transition-opacity duration-300">Usuarios</span>
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
                                    <a href="{{ route('admin.users.index') }}"
                                        class="group flex items-center py-2 text-[11px] font-bold {{ request()->routeIs('admin.users.index') ? 'text-sena' : 'text-slate-500 hover:text-slate-900' }} transition-all">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-3 border border-current opacity-40 group-hover:bg-current group-hover:opacity-100 transition-all"></span>
                                        Todos los Usuarios
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.users.create') }}"
                                        class="group flex items-center py-2 text-[11px] font-bold {{ request()->routeIs('admin.users.create') ? 'text-sena' : 'text-slate-500 hover:text-slate-900' }} transition-all">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-3 border border-current opacity-40 group-hover:bg-current group-hover:opacity-100 transition-all"></span>
                                        Registrar Nuevo
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Fases (Cohortes) -->
                        <li x-data="{ open: {{ request()->routeIs('admin.phases.*') ? 'true' : 'false' }} }">
                            <div
                                class="flex items-center justify-between gap-1 pr-2 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.phases.*') ? 'bg-sena/10 text-sena' : 'text-slate-600 hover:bg-slate-50 group' }}">
                                <a href="{{ route('admin.phases.index') }}"
                                    class="flex-1 flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300">
                                    <svg class="shrink-0 w-6 h-6 transition-colors {{ request()->routeIs('admin.phases.*') ? 'text-sena' : 'text-slate-500 group-hover:text-slate-700' }}"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    <span class="text-sm font-bold transition-opacity duration-300">Fases</span>
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
                                    <a href="{{ route('admin.phases.index') }}"
                                        class="group flex items-center py-2 text-[11px] font-bold {{ request()->routeIs('admin.phases.index') ? 'text-sena' : 'text-slate-500 hover:text-slate-900' }} transition-all">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-3 border border-current opacity-40 group-hover:bg-current group-hover:opacity-100 transition-all"></span>
                                        Gestionar Fases
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.phases.create') }}"
                                        class="group flex items-center py-2 text-[11px] font-bold {{ request()->routeIs('admin.phases.create') ? 'text-sena' : 'text-slate-500 hover:text-slate-900' }} transition-all">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-3 border border-current opacity-40 group-hover:bg-current group-hover:opacity-100 transition-all"></span>
                                        Crear Fase
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Asistencia -->
                        <li x-data="{ open: {{ request()->routeIs('admin.attendance.*') ? 'true' : 'false' }} }">
                            <div
                                class="flex items-center justify-between gap-1 pr-2 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.attendance.*') ? 'bg-sena/10 text-sena' : 'text-slate-600 hover:bg-slate-50 group' }}">
                                <a href="{{ route('admin.attendance.index') }}"
                                    class="flex-1 flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300">
                                    <svg class="shrink-0 w-6 h-6 transition-colors {{ request()->routeIs('admin.attendance.*') ? 'text-sena' : 'text-slate-500 group-hover:text-slate-700' }}"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                    <span class="text-sm font-bold transition-opacity duration-300">Asistencia</span>
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
                                    <a href="{{ route('admin.attendance.index') }}"
                                        class="group flex items-center py-2 text-[11px] font-bold {{ request()->routeIs('admin.attendance.index') ? 'text-sena' : 'text-slate-500 hover:text-slate-900' }} transition-all">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-3 border border-current opacity-40 group-hover:bg-current group-hover:opacity-100 transition-all"></span>
                                        Panel de Control
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.attendance.logs') }}"
                                        class="group flex items-center py-2 text-[11px] font-bold {{ request()->routeIs('admin.attendance.logs') ? 'text-sena' : 'text-slate-500 hover:text-slate-900' }} transition-all">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-3 border border-current opacity-40 group-hover:bg-current group-hover:opacity-100 transition-all"></span>
                                        Logs de Registros
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.attendance.detailed-report') }}"
                                        class="group flex items-center py-2 text-[11px] font-bold {{ request()->routeIs('admin.attendance.detailed-report') ? 'text-sena' : 'text-slate-500 hover:text-slate-900' }} transition-all">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-3 border border-current opacity-40 group-hover:bg-current group-hover:opacity-100 transition-all"></span>
                                        Reporte Detallado
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.attendance.sessions') }}"
                                        class="group flex items-center py-2 text-[11px] font-bold {{ request()->routeIs('admin.attendance.sessions') ? 'text-sena' : 'text-slate-500 hover:text-slate-900' }} transition-all">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-3 border border-current opacity-40 group-hover:bg-current group-hover:opacity-100 transition-all"></span>
                                        Sesiones
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Horarios -->
                        <li x-data="{ open: {{ request()->routeIs('admin.schedules.*') ? 'true' : 'false' }} }">
                            <div
                                class="flex items-center justify-between gap-1 pr-2 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.schedules.*') ? 'bg-sena/10 text-sena' : 'text-slate-600 hover:bg-slate-50 group' }}">
                                <a href="{{ route('admin.schedules.index') }}"
                                    class="flex-1 flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300">
                                    <svg class="shrink-0 w-6 h-6 transition-colors {{ request()->routeIs('admin.schedules.*') ? 'text-sena' : 'text-slate-500 group-hover:text-slate-700' }}"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-sm font-bold transition-opacity duration-300">Horarios</span>
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
                                    <a href="{{ route('admin.schedules.index') }}"
                                        class="group flex items-center py-2 text-[11px] font-bold {{ request()->routeIs('admin.schedules.index') ? 'text-sena' : 'text-slate-500 hover:text-slate-900' }} transition-all">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-3 border border-current opacity-40 group-hover:bg-current group-hover:opacity-100 transition-all"></span>
                                        Ver Registros
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.schedules.create') }}"
                                        class="group flex items-center py-2 text-[11px] font-bold {{ request()->routeIs('admin.schedules.create') ? 'text-sena' : 'text-slate-500 hover:text-slate-900' }} transition-all">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-3 border border-current opacity-40 group-hover:bg-current group-hover:opacity-100 transition-all"></span>
                                        Asignar Horario
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Otros Menús (Certificados, Recuperación) ... mantenemos la estructura pero aplicamos el estilo dark -->
                        <!-- Certificados -->
                        <li x-data="{ open: {{ request()->routeIs('admin.certificates.*') ? 'true' : 'false' }} }">
                            <div
                                class="flex items-center justify-between gap-1 pr-2 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.certificates.*') ? 'bg-sena/10 text-sena' : 'text-slate-400 hover:bg-white/5 group sidebar-item-hover' }}">
                                <a href="{{ route('admin.certificates.index') }}"
                                    class="flex-1 flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300">
                                    <svg class="shrink-0 w-6 h-6 transition-colors {{ request()->routeIs('admin.certificates.*') ? 'text-sena' : 'text-slate-500 group-hover:text-slate-300' }}"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path
                                            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                    </svg>
                                    <span class="text-sm font-bold transition-opacity duration-300">Certificados</span>
                                </a>
                                <button @click="open = !open" class="p-2 hover:bg-white/5 rounded-lg transition-colors">
                                    <svg class="w-3 h-3 transition-transform duration-300 text-slate-600"
                                        :class="open ? 'rotate-180 text-sena' : ''" viewBox="0 0 12 12">
                                        <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" fill="currentColor" />
                                    </svg>
                                </button>
                            </div>
                            <ul class="ml-7 pl-7 mt-2 space-y-1 mb-2 border-l-2 border-slate-800/40" x-show="open"
                                x-cloak x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0 text-slate-400">
                                <li>
                                    <a href="{{ route('admin.certificates.index') }}"
                                        class="group flex items-center py-2 text-[11px] font-bold {{ request()->routeIs('admin.certificates.index') ? 'text-sena' : 'text-slate-500 hover:text-white' }} transition-all">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-3 border border-current opacity-40 group-hover:bg-current group-hover:opacity-100 transition-all"></span>
                                        Gestionar
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Cumplimiento -->
                        <li x-data="{ open: {{ request()->routeIs('admin.reports.hours') ? 'true' : 'false' }} }">
                            <div
                                class="flex items-center justify-between gap-1 pr-2 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.reports.hours') ? 'bg-sena/10 text-sena' : 'text-slate-600 hover:bg-slate-50 group' }}">
                                <a href="{{ route('admin.reports.hours') }}"
                                    class="flex-1 flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300">
                                    <svg class="shrink-0 w-6 h-6 transition-colors {{ request()->routeIs('admin.reports.hours') ? 'text-sena' : 'text-slate-500 group-hover:text-slate-700' }}"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-sm font-bold transition-opacity duration-300">Control de Horas</span>
                                </a>
                            </div>
                        </li>

                        <!-- Recuperación -->
                        <li
                            x-data="{ open: {{ (request()->routeIs('admin.penalties.*') || request()->routeIs('admin.recovery-requests.*') || request()->routeIs('admin.recovery-sessions.*')) ? 'true' : 'false' }} }">
                            <div
                                class="flex items-center justify-between gap-1 pr-2 rounded-xl transition-all duration-300 {{ (request()->routeIs('admin.penalties.*') || request()->routeIs('admin.recovery-requests.*') || request()->routeIs('admin.recovery-sessions.*')) ? 'bg-sena/10 text-sena' : 'text-slate-600 hover:bg-slate-50 group' }}">
                                <a href="{{ route('admin.penalties.index') }}"
                                    class="flex-1 flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300">
                                    <svg class="shrink-0 w-6 h-6 transition-colors {{ (request()->routeIs('admin.penalties.*') || request()->routeIs('admin.recovery-requests.*') || request()->routeIs('admin.recovery-sessions.*')) ? 'text-sena' : 'text-slate-500 group-hover:text-slate-700' }}"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <span class="text-sm font-bold transition-opacity duration-300">Cumplimiento</span>
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
                                    <a href="{{ route('admin.penalties.index') }}"
                                        class="group flex items-center py-2 text-[11px] font-bold {{ request()->routeIs('admin.penalties.index') ? 'text-sena' : 'text-slate-500 hover:text-slate-900' }} transition-all">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-3 border border-current opacity-40 group-hover:bg-current group-hover:opacity-100 transition-all"></span>
                                        Penalizaciones
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.recovery-requests.index') }}"
                                        class="group flex items-center py-2 text-[11px] font-bold {{ request()->routeIs('admin.recovery-requests.index') ? 'text-sena' : 'text-slate-500 hover:text-slate-900' }} transition-all">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-3 border border-current opacity-40 group-hover:bg-current group-hover:opacity-100 transition-all"></span>
                                        Solicitudes
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.recovery-sessions.index') }}"
                                        class="group flex items-center py-2 text-[11px] font-bold {{ request()->routeIs('admin.recovery-sessions.index') ? 'text-sena' : 'text-slate-500 hover:text-slate-900' }} transition-all">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-3 border border-current opacity-40 group-hover:bg-current group-hover:opacity-100 transition-all"></span>
                                        Sesiones
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Huella Digital -->
                        <li x-data="{ open: {{ request()->routeIs('admin.fingerprint.*') ? 'true' : 'false' }} }">
                            <div
                                class="flex items-center justify-between gap-1 pr-2 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.fingerprint.*') ? 'bg-sena/10 text-sena' : 'text-slate-600 hover:bg-slate-50 group' }}">
                                <a href="{{ route('admin.fingerprint.enroll') }}"
                                    class="flex-1 flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300">
                                    <i
                                        class="fas fa-fingerprint w-6 h-6 flex items-center justify-center transition-colors {{ request()->routeIs('admin.fingerprint.*') ? 'text-sena' : 'text-slate-500 group-hover:text-slate-700' }}"></i>
                                    <span class="text-sm font-bold transition-opacity duration-300">Gestión de Huellas</span>
                                </a>
                            </div>
                        </li>
                    </ul>
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
                            <span class="sr-only">Abrir menú</span>
                            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                                <rect x="4" y="5" width="16" height="2" rx="1" />
                                <rect x="4" y="11" width="16" height="2" rx="1" />
                                <rect x="4" y="17" width="16" height="2" rx="1" />
                            </svg>
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
                                <svg class="w-5 h-5 group-hover:text-sena transition-colors" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
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
                                    class="w-9 h-9 rounded-xl sena-gradient flex items-center justify-center text-white font-bold text-sm shadow-sena overflow-hidden">
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
                                        Administrador</p>
                                </div>
                                <svg class="w-3 h-3 text-slate-400 transition-transform duration-300"
                                    :class="userMenu ? 'rotate-180 text-sena' : ''" viewBox="0 0 12 12">
                                    <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" fill="currentColor" />
                                </svg>
                            </button>
                            <!-- Dropdown content -->
                            <div class="absolute top-full right-0 w-48 bg-white border border-slate-100 shadow-xl rounded-2xl mt-2 p-2 z-50 overflow-hidden"
                                x-show="userMenu" x-cloak x-transition @click.outside="userMenu = false">
                                <a href="{{ route('profile.edit') }}"
                                    class="flex items-center gap-3 px-3 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-sena rounded-lg transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                            stroke-width="2" />
                                    </svg>
                                    Mi Perfil
                                </a>
                                <hr class="my-1 border-slate-50">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full flex items-center gap-3 px-3 py-2 text-xs font-bold text-rose-500 hover:bg-rose-50 rounded-lg transition-all text-left">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"
                                                stroke-width="2" />
                                        </svg>
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
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-sena transition-colors">Sistema</a>
                        @yield('breadcrumb')
                    </div>

                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div
                            class="mb-6 flex items-center gap-3 bg-green-50 border border-green-100 text-green-700 px-4 py-3 rounded-2xl text-sm font-medium animate-pulse-once">
                            <svg class="w-5 h-5 text-sena" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                            </svg>
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Content -->
                    <div class="fade-in">
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>

    <style>
        .fade-in {
            animation: fadeIn 0.4s ease-out;
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

        .animate-pulse-once {
            animation: pulseOnce 2s ease-in-out;
        }

        @keyframes pulseOnce {
            0% {
                opacity: 0;
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 1;
            }
        }
    </style>

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
