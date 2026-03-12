@extends('layouts.master')

@section('title', 'Certificados - SIAP Admin')
@section('page-title', 'Gestión de Certificados')

@section('breadcrumb')
    <li class="breadcrumb-item active">Certificados</li>
@endsection

@section('content')
    <!-- Header Section -->
    <div class="mb-10">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-4xl font-extrabold text-slate-900 mb-2 font-outfit tracking-tight">Gestión de Certificados
                </h1>
                <p class="text-slate-500 font-medium">Administre, emita y rastree la entrega de certificados a los
                    aprendices.</p>
            </div>
            <div class="flex gap-4">
                <a href="{{ route('admin.certificates.create') }}"
                    class="btn-primary-unified flex items-center gap-2 px-6 py-3 shadow-sena">
                    <i class="fas fa-certificate"></i>
                    Emitir Nuevo Certificado
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
        <!-- Total Certificates -->
        <div
            class="bg-white p-8 rounded-[2rem] shadow-premium border border-slate-100 group hover:-translate-y-2 transition-all">
            <div class="flex items-center justify-between mb-6">
                <div
                    class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 shadow-inner group-hover:scale-110 transition-transform">
                    <i class="fas fa-award text-xl"></i>
                </div>
                <span
                    class="text-[10px] font-bold text-blue-500 bg-blue-50 px-3 py-1 rounded-lg uppercase tracking-wider">Total</span>
            </div>
            <div>
                @isset($certificates)
                    <h3 class="text-3xl font-bold text-slate-800 font-outfit mb-1">{{ $certificates->total() }}</h3>
                @endisset
                <p class="text-sm text-slate-400 font-medium italic">Emitidos</p>
            </div>
        </div>

        <!-- Generated -->
        <div
            class="bg-white p-8 rounded-[2rem] shadow-premium border border-slate-100 group hover:-translate-y-2 transition-all">
            <div class="flex items-center justify-between mb-6">
                <div
                    class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-inner group-hover:scale-110 transition-transform">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <span
                    class="text-[10px] font-bold text-emerald-500 bg-emerald-50 px-3 py-1 rounded-lg uppercase tracking-wider">Generados</span>
            </div>
            <div>
                @isset($statusCounts)
                    <h3 class="text-3xl font-bold text-slate-800 font-outfit mb-1">{{ $statusCounts['generado'] ?? 0 }}</h3>
                @endisset
                <p class="text-sm text-slate-400 font-medium italic">Listos para entrega</p>
            </div>
        </div>

        <!-- Sent -->
        <div
            class="bg-white p-8 rounded-[2rem] shadow-premium border border-slate-100 group hover:-translate-y-2 transition-all">
            <div class="flex items-center justify-between mb-6">
                <div
                    class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 shadow-inner group-hover:scale-110 transition-transform">
                    <i class="fas fa-paper-plane text-xl"></i>
                </div>
                <span
                    class="text-[10px] font-bold text-amber-500 bg-amber-50 px-3 py-1 rounded-lg uppercase tracking-wider">Enviados</span>
            </div>
            <div>
                @isset($statusCounts)
                    <h3 class="text-3xl font-bold text-slate-800 font-outfit mb-1">{{ $statusCounts['enviado'] ?? 0 }}</h3>
                @endisset
                <p class="text-sm text-slate-400 font-medium italic">Vía correo</p>
            </div>
        </div>

        <!-- Downloaded -->
        <div
            class="bg-white p-8 rounded-[2rem] shadow-premium border border-slate-100 group hover:-translate-y-2 transition-all">
            <div class="flex items-center justify-between mb-6">
                <div
                    class="w-12 h-12 bg-purple-50 rounded-2xl flex items-center justify-center text-purple-600 shadow-inner group-hover:scale-110 transition-transform">
                    <i class="fas fa-download text-xl"></i>
                </div>
                <span
                    class="text-[10px] font-bold text-purple-500 bg-purple-50 px-3 py-1 rounded-lg uppercase tracking-wider">Descargas</span>
            </div>
            <div>
                @isset($statusCounts)
                    <h3 class="text-3xl font-bold text-slate-800 font-outfit mb-1">{{ $statusCounts['descargado'] ?? 0 }}</h3>
                @endisset
                <p class="text-sm text-slate-400 font-medium italic">Por aprendices</p>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
        <form method="GET" action="{{ route('admin.certificates.index') }}"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Apprentice Search -->
            <div class="lg:col-span-1">
    <div class="bg-white rounded-[2rem] shadow-premium border border-slate-100 p-8 mb-12 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-slate-50 rounded-full blur-3xl opacity-50 -mr-10 -mt-10"></div>
        
        <form method="GET" action="{{ route('admin.certificates.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-6 relative z-10">
            <!-- Search -->
            <div class="xl:col-span-2">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 ml-1">Buscar aprendiz</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                        <i class="fas fa-search text-slate-300"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre, correo o documento..."
                        class="form-input-tailwind w-full pl-12 pr-4 py-3 rounded-2xl bg-slate-50/50 border-slate-100 focus:bg-white transition-all">
                </div>
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 ml-1">Desde Fecha</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="form-input-tailwind w-full px-5 py-3 rounded-2xl bg-slate-50/50 border-slate-100 focus:bg-white transition-all appearance-none cursor-pointer">
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 ml-1">Hasta Fecha</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="form-input-tailwind w-full px-5 py-3 rounded-2xl bg-slate-50/50 border-slate-100 focus:bg-white transition-all appearance-none cursor-pointer">
            </div>

            <!-- Action Buttons -->
            <div class="flex items-end gap-3">
                <button type="submit"
                    class="flex-1 sena-gradient hover:opacity-90 text-white font-bold py-3.5 rounded-2xl transition-all shadow-sena shadow-md">
                    <i class="fas fa-filter mr-2 text-xs"></i>
                    Filtrar
                </button>
                <a href="{{ route('admin.certificates.index') }}"
                    class="bg-slate-50 hover:bg-slate-100 text-slate-400 p-3.5 rounded-2xl border border-slate-100 transition-all flex items-center justify-center aspect-square"
                    title="Limpiar filtros">
                    <i class="fas fa-sync-alt"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Certificates Table Card -->
    <div class="bg-white rounded-[2.5rem] shadow-premium border border-slate-100 overflow-hidden group mb-12">
        <div class="p-0 overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Aprendiz</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Horas</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Estado</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Fecha Emisión</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Email Seguimiento</th>
                        <th class="px-8 py-5 text-right text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($certificates as $certificate)
                        <tr class="group/item hover:bg-slate-50/80 transition-all duration-200">
                            <!-- Apprentice Info -->
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        <div
                                            class="h-12 w-12 rounded-2xl sena-gradient flex items-center justify-center text-white font-bold text-lg shadow-sena shadow-md group-hover/item:scale-110 group-hover/item:rotate-3 transition-transform">
                                            {{ strtoupper(substr($certificate->apprentice->full_name ?? 'N', 0, 1)) }}{{ strtoupper(substr(strrchr($certificate->apprentice->full_name ?? '', " ") ?: " ", 1, 1)) }}
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 mb-0.5">{{ $certificate->apprentice->full_name ?? 'N/A' }}</p>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ $certificate->apprentice->email ?? 'no-email@system.com' }}</p>
                                        @if($certificate->apprentice->apprenticeProfile?->cohort)
                                            <div class="mt-1">
                                                <span class="inline-flex px-2 py-0.5 rounded text-[9px] font-bold bg-blue-50 text-blue-600 border border-blue-100 uppercase tracking-tighter">
                                                    FICHA: {{ $certificate->apprentice->apprenticeProfile->cohort }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Hours -->
                            <td class="px-8 py-6">
                                <div class="inline-flex px-3 py-1 bg-slate-50 text-slate-700 rounded-lg border border-slate-100 font-extrabold font-outfit text-sm">
                                    @hm($certificate->hours_completed)
                                </div>
                            </td>

                            <!-- Status Badge -->
                            <td class="px-8 py-6">
                                @if($certificate->status === 'generado')
                                    <span class="inline-flex items-center px-4 py-1.5 bg-emerald-50 text-emerald-600 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-100">
                                        <i class="fas fa-check-circle mr-1.5 animate-pulse"></i>
                                        Generado
                                    </span>
                                @elseif($certificate->status === 'enviado')
                                    <span class="inline-flex items-center px-4 py-1.5 bg-amber-50 text-amber-600 text-[10px] font-bold uppercase tracking-widest rounded-full border border-amber-100">
                                        <i class="fas fa-paper-plane mr-1.5"></i>
                                        Enviado
                                    </span>
                                @elseif($certificate->status === 'descargado')
                                    <span class="inline-flex items-center px-4 py-1.5 bg-blue-50 text-blue-600 text-[10px] font-bold uppercase tracking-widest rounded-full border border-blue-100">
                                        <i class="fas fa-download mr-1.5"></i>
                                        Descargado
                                    </span>
                                @elseif($certificate->status === 'anulado')
                                    <span class="inline-flex items-center px-4 py-1.5 bg-rose-50 text-rose-600 text-[10px] font-bold uppercase tracking-widest rounded-full border border-rose-100">
                                        <i class="fas fa-times-circle mr-1.5"></i>
                                        Anulado
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-4 py-1.5 bg-slate-50 text-slate-500 text-[10px] font-bold uppercase tracking-widest rounded-full border border-slate-100">
                                        Pendiente
                                    </span>
                                @endif
                            </td>

                            <!-- Issued Date -->
                            <td class="px-8 py-6">
                                @if($certificate->issued_at)
                                    <div class="text-sm font-bold text-slate-800 mb-0.5">{{ $certificate->issued_at->format('d/m/Y') }}</div>
                                    <div class="text-[11px] text-slate-400 font-medium">{{ $certificate->issued_at->format('H:i') }}</div>
                                @else
                                    <span class="text-xs text-slate-300 italic font-medium">Pendiente</span>
                                @endif
                            </td>

                            <!-- Email Info -->
                            <td class="px-8 py-6">
                                @if($certificate->email_to)
                                    <div class="text-sm font-bold text-slate-700 truncate max-w-[150px] mb-0.5" title="{{ $certificate->email_to }}">
                                        {{ $certificate->email_to }}
                                    </div>
                                    @if($certificate->email_sent_at)
                                        <div class="text-[10px] text-emerald-500 font-bold uppercase tracking-wider flex items-center">
                                            <i class="fas fa-paper-plane mr-1"></i>
                                            {{ $certificate->email_sent_at->format('d/m/Y') }}
                                        </div>
                                    @endif
                                @else
                                    <span class="text-xs text-slate-300 italic font-medium">No enviado</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.certificates.show', $certificate) }}"
                                        class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-sena hover:text-white transition-all shadow-sm active:scale-95 flex items-center justify-center"
                                        title="Ver Detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if($certificate->pdf_path && Storage::exists($certificate->pdf_path))
                                        <a href="{{ route('admin.certificates.download', $certificate) }}"
                                            class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-blue-500 hover:text-white transition-all shadow-sm active:scale-95 flex items-center justify-center"
                                            title="Descargar PDF">
                                            <i class="fas fa-download"></i>
                                        </a>

                                        <form action="{{ route('admin.certificates.send-email', $certificate) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-amber-500 hover:text-white transition-all shadow-sm active:scale-95 flex items-center justify-center"
                                                title="Enviar por Correo">
                                                <i class="fas fa-envelope"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.certificates.generate-pdf', $certificate) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-emerald-500 hover:text-white transition-all shadow-sm active:scale-95 flex items-center justify-center"
                                                title="Generar PDF">
                                                <i class="fas fa-magic"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.certificates.destroy', $certificate) }}"
                                        method="POST" class="inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-rose-500 hover:text-white transition-all shadow-sm active:scale-95 flex items-center justify-center delete-btn"
                                            title="Anular">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 mb-1">No se encontraron certificados</h3>
                                    <p class="text-xs text-gray-500 max-w-[200px] mx-auto">Intente ajustar sus filtros o cree
                                        un nuevo certificado.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(isset($certificates) && $certificates->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $certificates->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Confirmación de eliminación con SweetAlert2
            $('.delete-form').on('submit', function (e) {
                e.preventDefault();
                const form = this;

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¡No podrás revertir esto!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                } else {
                    if (confirm('¿Estás seguro de que quieres eliminar este certificado?')) {
                        form.submit();
                    }
                }
            });
        });
    </script>
@endpush