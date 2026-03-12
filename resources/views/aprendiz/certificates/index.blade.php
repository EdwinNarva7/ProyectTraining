@extends('layouts.masteraprendiz')

@section('title', 'Mis Certificados - SIEAP')
@section('page-title', 'Mis Documentos Oficiales')

@section('breadcrumb')
    <span class="text-slate-400">/</span>
    <span class="text-slate-600 font-bold uppercase tracking-widest text-[10px]">Certificaciones</span>
@endsection

@section('content')
    <div class="space-y-10 animate-fade-in pb-12">

        {{-- Header & Summary --}}
        <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-10">
            <div class="max-w-2xl">
                <h1 class="text-4xl font-black text-slate-800 tracking-tight font-outfit uppercase">
                    MIS <span class="text-indigo-600 font-black">LOGROS</span>
                </h1>
                <p class="text-slate-500 font-bold text-[11px] uppercase tracking-[0.2em] mt-2 opacity-60">Repositorio
                    oficial de certificaciones por horas de práctica</p>
            </div>

            @if($certificates->total() > 0)
                <div class="flex items-center gap-6">
                    <div
                        class="bg-white px-8 py-5 rounded-[2rem] shadow-premium border border-slate-50 flex flex-col items-center group hover:bg-slate-900 transition-all duration-500">
                        <span
                            class="text-3xl font-black text-slate-800 group-hover:text-white font-outfit tracking-tighter leading-none mb-1">{{ $certificates->total() }}</span>
                        <span
                            class="text-[9px] font-black text-slate-300 group-hover:text-white/30 uppercase tracking-[0.3em]">Total</span>
                    </div>
                    <div
                        class="bg-emerald-50 px-8 py-5 rounded-[2rem] border border-emerald-100 flex flex-col items-center group hover:bg-sena transition-all duration-500">
                        <span
                            class="text-3xl font-black text-emerald-600 group-hover:text-white font-outfit tracking-tighter leading-none mb-1">{{ $certificates->where('status', 'descargado')->count() }}</span>
                        <span
                            class="text-[9px] font-black text-emerald-400 group-hover:text-white/30 uppercase tracking-[0.3em]">Listos</span>
                    </div>
                </div>
            @endif
        </div>

        @if($certificates->count() > 0)
            {{-- Grid of Certificates --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($certificates as $certificate)
                    <div
                        class="group bg-white rounded-[3rem] shadow-premium border border-slate-50 overflow-hidden hover:shadow-2xl hover:border-slate-100 transition-all duration-500 flex flex-col">

                        {{-- Card Header Decor --}}
                        <div class="relative h-32 sena-gradient p-10 overflow-hidden">
                            <div
                                class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-1000">
                            </div>
                            <div class="relative z-10 flex justify-between items-start">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white border border-white/20 shadow-xl group-hover:rotate-6 transition-transform">
                                    <i class="fas fa-certificate text-xl"></i>
                                </div>
                                <span
                                    class="px-4 py-1.5 bg-black/20 backdrop-blur-md rounded-full text-[9px] font-black text-white uppercase tracking-widest border border-white/10 italic">
                                    REF #{{ str_pad($certificate->id, 5, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>
                        </div>

                        {{-- Body --}}
                        <div class="p-10 flex-grow space-y-8">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <h3
                                        class="text-xl font-black text-slate-800 font-outfit uppercase tracking-tight group-hover:text-indigo-600 transition-colors leading-none">
                                        Certificado Técnico</h3>
                                    @php
                                        $statusConfig = [
                                            'generado' => ['color' => 'text-amber-500', 'bg' => 'bg-amber-50', 'border' => 'border-amber-100'],
                                            'enviado' => ['color' => 'text-blue-500', 'bg' => 'bg-blue-50', 'border' => 'border-blue-100'],
                                            'descargado' => ['color' => 'text-sena', 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-100'],
                                            'anulado' => ['color' => 'text-rose-500', 'bg' => 'bg-rose-50', 'border' => 'border-rose-100']
                                        ];
                                        $cfg = $statusConfig[$certificate->status] ?? $statusConfig['generado'];
                                    @endphp
                     <span
                                        class="px-3 py-1 rounded-lg text-[8px] font-black {{ $cfg['bg'] }} {{ $cfg['color'] }} border {{ $cfg['border'] }} uppercase tracking-widest leading-none">
                                        {{ $certificate->status }}
                                    </span>
                                </div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest opacity-60">Expedido:
                                    {{ $certificate->issued_at->translatedFormat('d M, Y') }}</p>
                            </div>

                            <div class="p-6 bg-slate-50/50 rounded-3xl border border-slate-50 shadow-inner space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Inversión
                                        horaria</span>
                                    <span
                                        class="text-xl font-black text-slate-800 font-outfit tracking-tighter">{{ $certificate->hours_completed }}<span
                                            class="text-xs font-medium ml-1">HORAS</span></span>
                                </div>
                                @if($certificate->email_sent_at)
                                    <div class="flex items-center gap-3 pt-3 border-t border-slate-100/50">
                                        <div
                                            class="w-6 h-6 rounded-lg bg-white flex items-center justify-center text-slate-300 shadow-sm">
                                            <i class="fas fa-envelope text-[10px]"></i>
                                        </div>
                                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tight truncate">Enviado:
                                            {{ $certificate->email_sent_at->format('d/m/Y') }}</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Actions --}}
                            <div class="grid grid-cols-2 gap-4">
                                <a href="{{ route('apprentice.certificates.show', $certificate) }}"
                                    class="w-full py-4 bg-slate-50 text-slate-500 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 hover:text-white transition-all text-center">
                                    Explorar
                                </a>
                                @if(in_array($certificate->status, ['generado', 'enviado', 'descargado']))
                                    <a href="{{ route('apprentice.certificates.download', $certificate) }}"
                                        class="w-full py-4 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 shadow-xl shadow-indigo-100 text-center flex items-center justify-center gap-2 group/pdf">
                                        <i class="fas fa-file-pdf group-hover:scale-110 transition-transform"></i>
                                        PDF
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pb-12 pagination-premium">
                {{ $certificates->links() }}
            </div>
        @else
            {{-- Empty State --}}
            <div
                class="bg-white rounded-[4rem] shadow-premium border-4 border-dashed border-slate-50 p-20 lg:p-32 flex flex-col items-center justify-center text-center">
                <div class="relative w-48 h-48 mb-12 animate-float">
                    <div class="absolute inset-0 bg-slate-50 rounded-full blur-3xl opacity-50 scale-150"></div>
                    <div
                        class="relative z-10 w-full h-full bg-slate-50/50 text-slate-200 rounded-[3.5rem] flex items-center justify-center border border-slate-100 shadow-inner">
                        <i class="fas fa-award text-7xl opacity-40"></i>
                    </div>
                </div>

                <div class="max-w-md space-y-4">
                    <h2 class="text-4xl font-black text-slate-800 font-outfit uppercase tracking-tighter">Sin Certificados</h2>
                    <p class="text-slate-400 font-bold uppercase tracking-widest text-xs leading-relaxed opacity-60">Tus
                        certificaciones se activarán automáticamente una vez completado el ciclo de horas institucional.</p>
                </div>

                <div
                    class="mt-12 p-8 bg-indigo-50/50 border border-indigo-100 rounded-[2.5rem] max-w-lg text-left flex items-start gap-6 group hover:bg-indigo-50 transition-colors">
                    <div
                        class="w-14 h-14 bg-white text-indigo-500 rounded-2xl flex items-center justify-center shadow-xl border border-white group-hover:rotate-12 transition-transform">
                        <i class="fas fa-info-circle text-2xl"></i>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-sm font-black text-indigo-900 uppercase tracking-widest">Información Clave</h4>
                        <p class="text-[11px] font-bold text-indigo-700 leading-relaxed uppercase tracking-tight opacity-70">
                            Para la expedición de estos documentos se requiere la validación previa de todas tus bitácoras de
                            asistencia por parte de la coordinación.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection