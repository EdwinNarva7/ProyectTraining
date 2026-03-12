@extends('layouts.masteraprendiz')

@section('title', 'Dashboard - Aprendiz')
@section('page-title', 'Mi Resumen de Actividad')

@section('breadcrumb')
    <span class="text-slate-600 font-bold uppercase tracking-widest text-[10px]">Mi Progreso</span>
@endsection

@section('content')
    <div class="space-y-10 animate-fade-in pb-12">
        
        {{-- Welcome Section --}}
        <div class="relative overflow-hidden group">
            <div class="absolute inset-0 sena-gradient opacity-90 rounded-[3rem]"></div>
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all duration-700"></div>
            
            <div class="relative p-10 md:p-14 flex flex-col md:flex-row items-center justify-between gap-10">
                <div class="text-center md:text-left space-y-4">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/20 backdrop-blur-md rounded-full border border-white/20 text-[10px] font-bold text-white uppercase tracking-[0.3em]">
                        <i class="fas fa-sparkles text-amber-300 animate-pulse"></i> Bienvenido al Sistema
                    </div>
                    <h1 class="text-4xl md:text-6xl font-bold text-white font-outfit leading-tight tracking-tighter">
                        ¡Hola, <span class="text-emerald-300">{{ explode(' ', Auth::user()->name)[0] }}</span>!
                    </h1>
                    <p class="text-white/80 font-medium text-lg max-w-md">Tu camino hacia el éxito se construye con cada sesión de formación. ¡Sigue adelante!</p>
                </div>
                
                <div class="flex items-center gap-4 bg-white/10 backdrop-blur-xl p-6 rounded-[2.5rem] border border-white/20 shadow-2xl">
                    <div class="w-20 h-20 rounded-2xl bg-white flex flex-col items-center justify-center text-slate-800 shadow-xl">
                        <span class="text-[10px] font-bold uppercase tracking-tighter leading-none mb-1 text-slate-400">{{ now()->format('M') }}</span>
                        <span class="text-3xl font-bold font-outfit leading-none">{{ now()->format('d') }}</span>
                    </div>
                    <div class="text-white border-l border-white/10 pl-6">
                        <p class="text-[10px] font-black uppercase tracking-[0.3em] opacity-60 mb-1">Estado Hoy</p>
                        @if($activeSession)
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 bg-emerald-400 rounded-full animate-pulse shadow-[0_0_10px_rgba(52,211,153,0.8)]"></span>
                                <span class="text-lg font-bold font-outfit uppercase tracking-tight">En Formación</span>
                            </div>
                        @else
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 bg-slate-400 rounded-full opacity-50"></span>
                                <span class="text-lg font-bold font-outfit uppercase tracking-tight opacity-80">Fuera de Sesión</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistics Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-8">
            <!-- Horas Totales -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-premium border border-slate-50 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                <div class="flex items-center justify-between mb-8">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all duration-500 shadow-inner">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                    <span class="text-xs font-black text-blue-500 bg-blue-50 px-4 py-1.5 rounded-full border border-blue-100 italic">Horas Acumuladas</span>
                </div>
                <div class="space-y-1">
                    <h3 class="text-5xl font-black text-slate-800 font-outfit tracking-tighter">{{ number_format($totalHours, 1) }}</h3>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Total Horas Registradas</p>
                </div>
            </div>

            <!-- Sesiones Realizadas -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-premium border border-slate-50 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                <div class="flex items-center justify-between mb-8">
                    <div class="w-16 h-16 bg-sena/10 text-sena rounded-2xl flex items-center justify-center group-hover:bg-sena group-hover:text-white transition-all duration-500 shadow-inner">
                        <i class="fas fa-check-double text-2xl"></i>
                    </div>
                    <span class="text-xs font-black text-sena bg-sena/5 px-4 py-1.5 rounded-full border border-sena/10 italic">Sesiones Listas</span>
                </div>
                <div class="space-y-1">
                    <h3 class="text-5xl font-black text-slate-800 font-outfit tracking-tighter">{{ $completedSessions }}</h3>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Jornadas Completadas</p>
                </div>
            </div>

            <!-- Certificados -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-premium border border-slate-50 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                <div class="flex items-center justify-between mb-8">
                    <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center group-hover:bg-amber-500 group-hover:text-white transition-all duration-500 shadow-inner">
                        <i class="fas fa-award text-2xl"></i>
                    </div>
                    <span class="text-xs font-black text-amber-500 bg-amber-50 px-4 py-1.5 rounded-full border border-amber-100 italic">Logros</span>
                </div>
                <div class="space-y-1">
                    <h3 class="text-5xl font-black text-slate-800 font-outfit tracking-tighter">{{ $certificates->count() }}</h3>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Certificados de Corte</p>
                </div>
            </div>

            <!-- Progreso Formación -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-premium border border-slate-50 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                @php
                    $progreso = $totalSessions > 0 ? round(($completedSessions / $totalSessions) * 100, 1) : 0;
                @endphp
                <div class="flex items-center justify-between mb-8">
                    <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center group-hover:bg-rose-500 group-hover:text-white transition-all duration-500 shadow-inner">
                        <i class="fas fa-chart-line text-2xl"></i>
                    </div>
                    <span class="text-xs font-black text-rose-500 bg-rose-50 px-4 py-1.5 rounded-full border border-rose-100 italic">{{ $progreso }}%</span>
                </div>
                <div class="space-y-4">
                    <div class="space-y-1">
                        <h3 class="text-3xl font-black text-slate-800 font-outfit tracking-tighter">Mi Avance</h3>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Nivel de cumplimiento</p>
                    </div>
                    <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden border border-slate-200 p-0.5">
                        <div class="bg-rose-500 h-full rounded-full transition-all duration-1000 relative" style="width: {{ $progreso }}%">
                            <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            
            {{-- Hoy Timeline & Activity --}}
            <div class="lg:col-span-12 xl:col-span-7 space-y-8">
                <div class="bg-white rounded-[3.5rem] shadow-premium border border-slate-50 overflow-hidden group">
                    <div class="px-10 py-8 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-2xl bg-white shadow-sm flex items-center justify-center text-sena">
                                <i class="fas fa-fingerprint"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-800 font-outfit tracking-tight">Actividad en <span class="text-sena">Tiempo Real</span></h3>
                        </div>
                        <span class="px-5 py-2 bg-white text-slate-400 font-black text-[10px] rounded-full border border-slate-100 shadow-sm leading-none uppercase tracking-widest">
                            {{ now()->format('l, d M Y') }}
                        </span>
                    </div>
                    
                    <div class="p-10">
                        <div class="relative flex flex-col items-center py-8">
                            {{-- Timeline Line --}}
                            <div class="absolute left-1/2 top-0 bottom-0 w-1 bg-slate-100 -translate-x-1/2 rounded-full overflow-hidden">
                                <div class="w-full bg-sena animate-pulse" style="height: {{ $activeSession ? '50%' : ($todayEntry ? '30%' : '0%') }}"></div>
                            </div>
                            
                            {{-- Timeline Nodes --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 w-full relative z-10 gap-20">
                                <!-- Entry Node -->
                                <div class="flex flex-col items-center md:items-end text-center md:text-right space-y-4">
                                    <div class="w-20 h-20 rounded-[2rem] {{ $todayEntry ? 'bg-green-500 text-white shadow-green-200' : 'bg-slate-100 text-slate-300' }} flex items-center justify-center shadow-xl border-4 border-white transition-all duration-500">
                                        <i class="fas fa-door-open text-2xl"></i>
                                    </div>
                                    <div class="space-y-1">
                                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-tighter">Entrada de formación</h4>
                                        <p class="text-2xl font-black font-outfit {{ $todayEntry ? 'text-slate-800' : 'text-slate-200' }}">
                                            {{ $todayEntry ? $todayEntry->occurred_at->format('H:i A') : '--:--' }}
                                        </p>
                                        @if($todayEntry)
                                            <span class="inline-block px-3 py-1 bg-green-50 text-sena text-[9px] font-black rounded-lg border border-green-100 uppercase tracking-widest">Marcación OK</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Exit Node -->
                                <div class="flex flex-col items-center md:items-start text-center md:text-left space-y-4">
                                    <div class="w-20 h-20 rounded-[2rem] {{ $todayExit ? 'bg-amber-500 text-white shadow-amber-200' : 'bg-slate-100 text-slate-300' }} flex items-center justify-center shadow-xl border-4 border-white transition-all duration-500">
                                        <i class="fas fa-door-closed text-2xl"></i>
                                    </div>
                                    <div class="space-y-1">
                                        <h4 class="text-sm font-black text-slate-800 uppercase tracking-tighter">Salida de formación</h4>
                                        <p class="text-2xl font-black font-outfit {{ $todayExit ? 'text-slate-800' : 'text-slate-200' }}">
                                            {{ $todayExit ? $todayExit->occurred_at->format('H:i A') : '--:--' }}
                                        </p>
                                        @if($todayExit)
                                            <span class="inline-block px-3 py-1 bg-amber-50 text-amber-500 text-[9px] font-black rounded-lg border border-amber-100 uppercase tracking-widest">Registrada</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Final Message --}}
                        <div class="mt-12 text-center p-6 bg-slate-50 rounded-3xl border border-slate-100 border-dashed">
                             @if($activeSession)
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Tu sesión está activa. ¡No olvides marcar tu salida al finalizar!</p>
                             @elseif($todayEntry && $todayExit)
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">¡Excelente! Has completado tu jornada institucional hoy.</p>
                             @else
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Presenta tu huella en el lector para iniciar tu jornada.</p>
                             @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Panel: Certificates & Actions --}}
            <div class="lg:col-span-12 xl:col-span-5 space-y-8">
                
                {{-- Quick Certificates --}}
                <div class="bg-white rounded-[3.5rem] shadow-premium border border-slate-50 flex flex-col group overflow-hidden">
                    <div class="px-10 py-8 border-b border-slate-50 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-8 h-8 rounded-xl bg-amber-400/10 text-amber-500 flex items-center justify-center">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <h3 class="text-xl font-black text-slate-800 font-outfit uppercase tracking-tight">Mis <span class="text-amber-500">Logros</span></h3>
                        </div>
                        <a href="{{ route('apprentice.certificates.index') }}" class="text-[10px] font-black text-slate-400 hover:text-sena transition-colors uppercase tracking-widest">Ver Todos</a>
                    </div>
                    
                    <div class="p-8 flex-1">
                        @forelse($certificates->take(3) as $certificate)
                            <div class="flex items-center gap-5 p-5 bg-slate-50/50 rounded-3xl border border-slate-50 hover:bg-white hover:shadow-premium hover:border-slate-100 transition-all mb-4 last:mb-0 group/cert">
                                <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center text-amber-500 shadow-sm border border-slate-100 group-hover/cert:scale-110 group-hover/cert:rotate-3 transition-transform">
                                    <i class="fas fa-medal text-xl"></i>
                                </div>
                                <div class="flex-grow">
                                    <h4 class="text-sm font-black text-slate-800 uppercase tracking-tighter">Certificado de Corte</h4>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Expedido: {{ $certificate->created_at->format('d M, Y') }}</p>
                                </div>
                                <div class="shrink-0">
                                    @if($certificate->status === 'descargado')
                                         <span class="w-8 h-8 rounded-full bg-green-50 text-sena flex items-center justify-center text-xs">
                                             <i class="fas fa-check"></i>
                                         </span>
                                    @else
                                         <span class="w-8 h-8 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center text-xs">
                                             <i class="fas fa-download"></i>
                                         </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center">
                                <div class="w-16 h-16 bg-slate-50 rounded-[1.5rem] flex items-center justify-center text-slate-200 mx-auto mb-4 border border-slate-100">
                                    <i class="fas fa-folder-open text-2xl"></i>
                                </div>
                                <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.3em]">Sin documentos aún</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="p-8 pt-0">
                        <a href="{{ route('apprentice.certificates.index') }}" 
                           class="w-full flex items-center justify-center gap-3 py-5 bg-slate-900 text-white rounded-[2rem] text-xs font-black uppercase tracking-[0.2em] shadow-xl hover:bg-slate-800 transition-all hover:scale-[1.02] active:scale-95 group">
                            <i class="fas fa-external-link-alt text-[10px] group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
                            Solicitar Nueva Certificación
                        </a>
                    </div>
                </div>

                {{-- Help Card --}}
                <div class="bg-indigo-600 rounded-[3.5rem] p-10 text-white shadow-2xl relative overflow-hidden group">
                    <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-white/10 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700"></div>
                    <div class="relative z-10 space-y-6">
                        <div class="w-14 h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20">
                            <i class="fas fa-graduation-cap text-2xl"></i>
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-2xl font-black font-outfit tracking-tighter">¿Necesitas Ayuda?</h3>
                            <p class="text-indigo-100 text-sm font-medium leading-relaxed">Si tienes dudas sobre tus registros de asistencia o deudas de horas, contacta a tu instructor encargado.</p>
                        </div>
                        <button class="px-8 py-3 bg-white text-indigo-600 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:shadow-xl transition-all">
                            Preguntas Frecuentes
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
