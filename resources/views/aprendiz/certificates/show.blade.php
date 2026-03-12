@extends('layouts.masteraprendiz')

@section('title', 'Detalles del Certificado - SIEAP')
@section('page-title', 'Expediente de Certificado')

@section('breadcrumb')
    <span class="text-slate-400">/</span>
    <span class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">Certificaciones</span>
    <span class="text-slate-400 font-bold mx-2">/</span>
    <span class="text-slate-600 font-black uppercase tracking-widest text-[10px]">Expediente</span>
@endsection

@section('content')
    <div class="space-y-10 animate-fade-in pb-12">
        
        {{-- Header Navigation --}}
        <div class="flex items-center gap-6">
            <a href="{{ route('apprentice.certificates.index') }}" 
               class="group w-12 h-12 rounded-2xl bg-white border border-slate-100 text-slate-400 flex items-center justify-center hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all shadow-premium active:scale-90">
                <i class="fas fa-chevron-left group-hover:-translate-x-1 transition-transform"></i>
            </a>
            <div>
                <h1 class="text-4xl font-black text-slate-800 tracking-tight font-outfit uppercase">
                    EXPEDIENTE <span class="text-indigo-600 font-black">OFICIAL</span>
                </h1>
                <p class="text-slate-500 font-bold text-[11px] uppercase tracking-widest opacity-60">Garantía de cumplimiento y validación de horas prácticas</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            
            {{-- Main Document Card --}}
            <div class="lg:col-span-12 xl:col-span-8">
                <div class="bg-white rounded-[4rem] shadow-premium border border-slate-50 overflow-hidden group">
                    <div class="p-12 lg:p-16 space-y-12">
                        
                        {{-- Top Branding & Status --}}
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-10">
                            <div class="flex items-center gap-6">
                                <div class="w-20 h-20 rounded-[2rem] sena-gradient text-white flex items-center justify-center text-3xl shadow-2xl group-hover:rotate-12 transition-transform duration-700">
                                    <i class="fas fa-award"></i>
                                </div>
                                <div class="space-y-1">
                                    <h2 class="text-3xl font-black text-slate-800 font-outfit uppercase tracking-tighter leading-none mb-1">Certificación Técnica</h2>
                                    <div class="flex items-center gap-4">
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">FOLIO #{{ str_pad($certificate->id, 6, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                </div>
                            </div>

                            @php
                                $statusMap = [
                                    'generado' => ['color' => 'text-amber-500', 'bg' => 'bg-amber-50'],
                                    'enviado' => ['color' => 'text-blue-500', 'bg' => 'bg-blue-50'],
                                    'descargado' => ['color' => 'text-sena', 'bg' => 'bg-emerald-50'],
                                    'anulado' => ['color' => 'text-rose-500', 'bg' => 'bg-rose-50']
                                ];
                                $st = $statusMap[$certificate->status] ?? $statusMap['generado'];
                            @endphp
                            <div class="px-6 py-3 {{ $st['bg'] }} {{ $st['color'] }} rounded-[1.5rem] border border-current opacity-70 flex items-center gap-3">
                                <span class="w-3 h-3 bg-current rounded-full animate-pulse shadow-[0_0_10px_currentColor]"></span>
                                <span class="text-[10px] font-black uppercase tracking-[0.3em] leading-none">{{ $certificate->status }}</span>
                            </div>
                        </div>

                        {{-- Details Grid --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-16 gap-y-12 py-12 border-y border-slate-50 relative">
                            {{-- Central Decor --}}
                            <div class="absolute left-1/2 top-12 bottom-12 w-px bg-slate-50 hidden md:block"></div>

                            <div class="space-y-8">
                                <div class="group/item">
                                    <span class="block text-[10px] font-black text-slate-300 uppercase tracking-[0.3em] mb-4">Portador del Título</span>
                                    <div class="flex items-center gap-5">
                                        <div class="w-16 h-16 rounded-[2rem] bg-slate-50 text-slate-400 flex items-center justify-center text-lg font-black border border-slate-100 group-hover/item:bg-white group-hover/item:shadow-premium transition-all">
                                            {{ strtoupper(substr($certificate->apprentice->full_name, 0, 2)) }}
                                        </div>
                                        <div class="space-y-1">
                                            <p class="text-xl font-black text-slate-800 font-outfit uppercase tracking-tighter">{{ $certificate->apprentice->full_name }}</p>
                                            <p class="text-[11px] font-bold text-slate-400 tracking-wide">{{ $certificate->apprentice->email }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="group/item">
                                    <span class="block text-[10px] font-black text-slate-300 uppercase tracking-[0.3em] mb-4">Créditos de Formación</span>
                                    <div class="flex items-end gap-3">
                                        <span class="text-6xl font-black text-indigo-600 font-outfit tracking-tighter leading-none">{{ $certificate->hours_completed }}</span>
                                        <span class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-1.5 opacity-60">Horas registradas</span>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-12">
                                <div class="group/item">
                                    <span class="block text-[10px] font-black text-slate-300 uppercase tracking-[0.3em] mb-4">Fecha de Protocolo</span>
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center text-lg border border-slate-100 group-hover/item:text-indigo-500 transition-colors">
                                            <i class="fas fa-calendar-check"></i>
                                        </div>
                                        <div class="space-y-0.5">
                                            <p class="text-base font-black text-slate-800 uppercase tracking-tight">{{ $certificate->issued_at ? $certificate->issued_at->translatedFormat('d F, Y') : '--' }}</p>
                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Hora de cierre: {{ $certificate->issued_at ? $certificate->issued_at->format('H:i') : '--' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="group/item">
                                    <span class="block text-[10px] font-black text-slate-300 uppercase tracking-[0.3em] mb-4">Notificación Digital</span>
                                    @if($certificate->email_to)
                                        <div class="space-y-2">
                                            <p class="text-sm font-black text-slate-700 tracking-tight">{{ $certificate->email_to }}</p>
                                            @if($certificate->email_sent_at)
                                                <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 text-sena rounded-full text-[9px] font-black uppercase tracking-widest border border-emerald-100">
                                                    <i class="fas fa-paper-plane text-[8px]"></i> Enviado el {{ $certificate->email_sent_at->format('d/m/Y') }}
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-xs font-bold text-slate-300 italic uppercase">No se registra envío automático</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Final Verification Info --}}
                        <div class="flex flex-col md:flex-row items-center justify-between gap-8 pt-8 opacity-60">
                             <div class="flex items-center gap-4">
                                 <img src="https://www.sena.edu.co/Style%20Library/LogoSenaTipo.png" class="h-10 grayscale brightness-200 contrast-0 opacity-20" alt="SENA Logo">
                                 <div class="w-px h-8 bg-slate-200 hidden md:block"></div>
                                 <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">SIAP v2.0 • Registro Centralizado</p>
                             </div>
                             <div class="flex items-center gap-3 px-6 py-2 bg-slate-50 rounded-full border border-slate-100">
                                 <i class="fas fa-lock text-emerald-500 text-[10px]"></i>
                                 <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Contenido Cifrado y Validado</span>
                             </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar Actions --}}
            <div class="lg:col-span-12 xl:col-span-4 space-y-8">
                <div class="bg-white rounded-[3rem] shadow-premium border border-slate-50 p-10 space-y-8">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-[0.3em] font-outfit text-center">Gestión Documental</h3>
                    
                    <div class="space-y-4">
                        @if(in_array($certificate->status, ['generado', 'enviado', 'descargado']))
                            <a href="{{ route('apprentice.certificates.download', $certificate) }}" 
                               class="flex items-center justify-center gap-4 w-full py-5 bg-indigo-600 text-white rounded-[2rem] text-xs font-black uppercase tracking-[0.2em] shadow-2xl shadow-indigo-100 hover:bg-indigo-700 hover:scale-[1.02] active:scale-95 transition-all group">
                                <i class="fas fa-cloud-download-alt group-hover:translate-y-1 transition-transform"></i>
                                DESCARGAR PDF
                            </a>
                        @endif
                        
                        <button type="button" onclick="window.print()" 
                                class="flex items-center justify-center gap-4 w-full py-5 bg-white text-slate-600 border-2 border-slate-100 rounded-[2rem] text-xs font-black uppercase tracking-[0.2em] hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all active:scale-95 group">
                            <i class="fas fa-print group-hover:-rotate-12 transition-transform"></i>
                            IMPRIMIR VISTA
                        </button>
                    </div>

                    <div class="pt-4 border-t border-slate-50">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3em] text-center leading-relaxed">
                            Cualquier discrepancia en la información debe reportarse a la coordinación académica de inmediato.
                        </p>
                    </div>
                </div>

                {{-- Support Card --}}
                <div class="bg-slate-900 rounded-[3rem] p-10 text-white shadow-2xl relative overflow-hidden group">
                    <div class="absolute -right-20 -top-20 w-64 h-64 bg-white/5 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700"></div>
                    <div class="relative z-10 space-y-6">
                        <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center border border-white/10 group-hover:text-amber-400 transition-colors">
                            <i class="fas fa-headset text-2xl"></i>
                        </div>
                        <div class="space-y-2">
                             <h4 class="text-xl font-black font-outfit tracking-tight">Soporte Técnico</h4>
                             <p class="text-xs font-medium text-slate-400 leading-relaxed uppercase tracking-widest opacity-80">Asistencia administrativa para la corrección de folios y certificados.</p>
                        </div>
                        <button class="w-full py-4 bg-white/10 backdrop-blur-md rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-white hover:text-slate-900 transition-all">
                             SOPORTE EN LÍNEA
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
