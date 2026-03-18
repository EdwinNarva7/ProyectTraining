@extends('layouts.masteraprendiz')

@section('title', 'Mis Certificados')
@section('page-title', 'Gestión de Certificados')

@section('breadcrumb')
    <li class="breadcrumb-item active">Certificados</li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400">
                    <i class="fas fa-award text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-wider">Total</p>
                    <p class="text-3xl font-bold text-slate-800">{{ $certificates->total() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-green-500">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-wider">Descargados</p>
                    <p class="text-3xl font-bold text-slate-800">{{ $certificates->where('status', 'descargado')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center text-amber-500">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-wider">Pendientes</p>
                    <p class="text-3xl font-bold text-slate-800">{{ $certificates->where('status', '!=', 'descargado')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Certificate List -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm">
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-800">Mis Certificados</h3>
        </div>
        <div class="p-2">
            @if($certificates->count() > 0)
                <table class="w-full">
                    <thead>
                        <tr class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                            <th class="p-4 text-left">Referencia</th>
                            <th class="p-4 text-left">Horas</th>
                            <th class="p-4 text-left">Fecha Expedición</th>
                            <th class="p-4 text-left">Estado</th>
                            <th class="p-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($certificates as $certificate)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="p-4 font-bold text-slate-800">#{{ str_pad($certificate->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td class="p-4 font-bold text-slate-600">{{ $certificate->hours_completed }}</td>
                                <td class="p-4 text-slate-500">{{ $certificate->issued_at->translatedFormat('d M, Y') }}</td>
                                <td class="p-4">
                                    @php
                                        $statusConfig = [
                                            'generado' => ['color' => 'text-amber-500', 'bg' => 'bg-amber-100'],
                                            'enviado' => ['color' => 'text-blue-500', 'bg' => 'bg-blue-100'],
                                            'descargado' => ['color' => 'text-green-500', 'bg' => 'bg-green-100'],
                                            'anulado' => ['color' => 'text-rose-500', 'bg' => 'bg-rose-100']
                                        ];
                                        $cfg = $statusConfig[$certificate->status] ?? $statusConfig['generado'];
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $cfg['bg'] }} {{ $cfg['color'] }}">
                                        {{ $certificate->status }}
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <a href="{{ route('apprentice.certificates.download', $certificate) }}" class="px-4 py-2 bg-sena text-white rounded-lg text-xs font-bold hover:bg-sena-dark transition-colors @if(!in_array($certificate->status, ['generado', 'enviado', 'descargado'])) opacity-50 cursor-not-allowed @endif">
                                        <i class="fas fa-download mr-2"></i>Descargar
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-center p-12">
                    <i class="fas fa-award text-5xl text-slate-300 mb-4"></i>
                    <h4 class="text-lg font-bold text-slate-700">Aún no tienes certificados</h4>
                    <p class="text-sm text-slate-500">Tus certificados aparecerán aquí cuando completes las horas requeridas.</p>
                </div>
            @endif
        </div>
        @if($certificates->hasPages())
            <div class="p-6 border-t border-slate-100">
                {{ $certificates->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
