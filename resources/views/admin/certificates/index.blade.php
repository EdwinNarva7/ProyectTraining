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
                <h1 class="text-4xl font-extrabold text-slate-900 mb-2 font-outfit tracking-tight uppercase">Gestión de <span class="text-sena">Certificados</span></h1>
                <p class="text-slate-500 font-bold text-[11px] uppercase tracking-[0.2em] opacity-60">Administre, emita y rastree la entrega de certificados a los aprendices.</p>
            </div>
            <div class="flex gap-4">
                <a href="{{ route('admin.certificates.create') }}"
                    class="btn-primary-unified flex items-center gap-2 px-8 py-4 shadow-sena rounded-2xl font-bold uppercase tracking-widest text-xs transition-all hover:scale-105 active:scale-95">
                    <i class="fas fa-certificate text-sm"></i>
                    Emitir Nuevo Certificado
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
        <!-- Total Certificates -->
        <div class="bg-white p-8 rounded-[2.5rem] shadow-premium border border-slate-100 group hover:-translate-y-2 transition-all duration-500">
            <div class="flex items-center justify-between mb-6">
                <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 shadow-inner group-hover:scale-110 group-hover:rotate-3 transition-all">
                    <i class="fas fa-award text-2xl"></i>
                </div>
                <span class="text-[10px] font-black text-blue-500 bg-blue-50 px-4 py-1.5 rounded-full uppercase tracking-widest border border-blue-100">Total</span>
            </div>
            <div>
                <h3 class="text-4xl font-black text-slate-800 font-outfit mb-1 tracking-tighter">{{ $certificates->total() }}</h3>
                <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em]">Emitidos</p>
            </div>
        </div>

        <!-- Generated -->
        <div class="bg-white p-8 rounded-[2.5rem] shadow-premium border border-slate-100 group hover:-translate-y-2 transition-all duration-500">
            <div class="flex items-center justify-between mb-6">
                <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-inner group-hover:scale-110 group-hover:rotate-3 transition-all">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
                <span class="text-[10px] font-black text-emerald-500 bg-emerald-50 px-4 py-1.5 rounded-full uppercase tracking-widest border border-emerald-100">Generados</span>
            </div>
            <div>
                <h3 class="text-4xl font-black text-slate-800 font-outfit mb-1 tracking-tighter">{{ $statusCounts['generado'] ?? 0 }}</h3>
                <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em]">Listos para entrega</p>
            </div>
        </div>

        <!-- Sent -->
        <div class="bg-white p-8 rounded-[2.5rem] shadow-premium border border-slate-100 group hover:-translate-y-2 transition-all duration-500">
            <div class="flex items-center justify-between mb-6">
                <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 shadow-inner group-hover:scale-110 group-hover:rotate-3 transition-all">
                    <i class="fas fa-paper-plane text-2xl"></i>
                </div>
                <span class="text-[10px] font-black text-amber-500 bg-amber-50 px-4 py-1.5 rounded-full uppercase tracking-widest border border-amber-100">Enviados</span>
            </div>
            <div>
                <h3 class="text-4xl font-black text-slate-800 font-outfit mb-1 tracking-tighter">{{ $statusCounts['enviado'] ?? 0 }}</h3>
                <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em]">Vía correo</p>
            </div>
        </div>

        <!-- Downloaded -->
        <div class="bg-white p-8 rounded-[2.5rem] shadow-premium border border-slate-100 group hover:-translate-y-2 transition-all duration-500">
            <div class="flex items-center justify-between mb-6">
                <div class="w-14 h-14 bg-purple-50 rounded-2xl flex items-center justify-center text-purple-600 shadow-inner group-hover:scale-110 group-hover:rotate-3 transition-all">
                    <i class="fas fa-download text-2xl"></i>
                </div>
                <span class="text-[10px] font-black text-purple-500 bg-purple-50 px-4 py-1.5 rounded-full uppercase tracking-widest border border-purple-100">Descargas</span>
            </div>
            <div>
                <h3 class="text-4xl font-black text-slate-800 font-outfit mb-1 tracking-tighter">{{ $statusCounts['descargado'] ?? 0 }}</h3>
                <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.2em]">Por aprendices</p>
            </div>
        </div>
    </div>

    <!-- Search and Filters Section -->
    <div class="bg-white rounded-[2.5rem] shadow-premium border border-slate-100 p-8 mb-12 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-slate-50 rounded-full blur-3xl opacity-50 -mr-10 -mt-10"></div>
        
        <form method="GET" action="{{ route('admin.certificates.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-6 relative z-10">
            <!-- Search -->
            <div class="xl:col-span-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Buscar colaborador</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none transition-colors group-focus-within:text-sena">
                        <i class="fas fa-search text-slate-300"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre, correo o documento..."
                        class="form-input-tailwind w-full pl-12 pr-4 py-4 rounded-[1.5rem] bg-slate-50/50 border-slate-100 focus:bg-white focus:ring-4 focus:ring-sena/5 transition-all font-medium text-sm">
                </div>
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Desde Fecha</label>
                <div class="relative group">
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="form-input-tailwind w-full px-5 py-4 rounded-[1.5rem] bg-slate-50/50 border-slate-100 focus:bg-white focus:ring-4 focus:ring-sena/5 transition-all appearance-none cursor-pointer font-medium text-sm">
                </div>
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1">Hasta Fecha</label>
                <div class="relative group">
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="form-input-tailwind w-full px-5 py-4 rounded-[1.5rem] bg-slate-50/50 border-slate-100 focus:bg-white focus:ring-4 focus:ring-sena/5 transition-all appearance-none cursor-pointer font-medium text-sm">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-end gap-3">
                <button type="submit"
                    class="flex-1 sena-gradient hover:opacity-90 text-white font-black py-4 rounded-[1.5rem] transition-all shadow-sena shadow-lg active:scale-95 flex items-center justify-center gap-2 uppercase tracking-widest text-xs">
                    <i class="fas fa-filter text-[10px]"></i>
                    Filtrar
                </button>
                <a href="{{ route('admin.certificates.index') }}"
                    class="bg-slate-50 hover:bg-slate-100 text-slate-400 p-4 rounded-[1.5rem] border border-slate-100 transition-all flex items-center justify-center aspect-square shadow-sm active:rotate-180"
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
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Colaborador</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Horas</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Estado</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Emisión</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Seguimiento</th>
                        <th class="px-8 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($certificates as $certificate)
                        <tr class="group/item hover:bg-slate-50/80 transition-all duration-300">
                            <!-- Apprentice Info -->
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="flex-shrink-0">
                                        <div class="h-14 w-14 rounded-2xl sena-gradient flex items-center justify-center text-white font-black text-xl shadow-sena shadow-lg group-hover/item:scale-110 group-hover/item:rotate-3 transition-all duration-500">
                                            {{ strtoupper(substr($certificate->apprentice->full_name ?? 'N', 0, 1)) }}{{ strtoupper(substr(strrchr($certificate->apprentice->full_name ?? '', " ") ?: " ", 1, 1)) }}
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-slate-800 mb-0.5 group-hover/item:text-sena transition-colors">{{ $certificate->apprentice->full_name ?? 'N/A' }}</p>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest opacity-70">{{ $certificate->apprentice->email ?? 'no-email@system.com' }}</p>
                                        @if($certificate->apprentice->apprenticeProfile?->cohort)
                                            <div class="mt-1.5">
                                                <span class="inline-flex px-3 py-1 rounded-lg text-[9px] font-black bg-blue-50 text-blue-600 border border-blue-100 uppercase tracking-widest">
                                                    FICHA: {{ $certificate->apprentice->apprenticeProfile->cohort }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Hours -->
                            <td class="px-8 py-6">
                                <div class="inline-flex px-4 py-2 bg-slate-50 text-slate-700 rounded-xl border border-slate-100 font-black font-outfit text-sm tracking-tighter">
                                    {{ $certificate->hours_completed }} <span class="text-[10px] ml-1.5 opacity-40">HRS</span>
                                </div>
                            </td>

                            <!-- Status Badge -->
                            <td class="px-8 py-6">
                                @php
                                    $statusConfig = [
                                        'generado' => ['color' => 'text-emerald-600', 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-100', 'icon' => 'fa-check-circle', 'animate' => 'animate-pulse'],
                                        'enviado' => ['color' => 'text-amber-600', 'bg' => 'bg-amber-50', 'border' => 'border-amber-100', 'icon' => 'fa-paper-plane', 'animate' => ''],
                                        'descargado' => ['color' => 'text-blue-600', 'bg' => 'bg-blue-50', 'border' => 'border-blue-100', 'icon' => 'fa-download', 'animate' => ''],
                                        'anulado' => ['color' => 'text-rose-600', 'bg' => 'bg-rose-50', 'border' => 'border-rose-100', 'icon' => 'fa-times-circle', 'animate' => '']
                                    ];
                                    $cfg = $statusConfig[$certificate->status] ?? ['color' => 'text-slate-500', 'bg' => 'bg-slate-50', 'border' => 'border-slate-100', 'icon' => 'fa-clock', 'animate' => ''];
                                @endphp
                                <span class="inline-flex items-center px-4 py-2 {{ $cfg['bg'] }} {{ $cfg['color'] }} text-[9px] font-black uppercase tracking-[0.15em] rounded-full border {{ $cfg['border'] }} shadow-sm">
                                    <i class="fas {{ $cfg['icon'] }} mr-2 {{ $cfg['animate'] }}"></i>
                                    {{ ucfirst($certificate->status) }}
                                </span>
                            </td>

                            <!-- Issued Date -->
                            <td class="px-8 py-6">
                                @if($certificate->issued_at)
                                    <div class="text-sm font-black text-slate-800 mb-0.5 tracking-tighter">{{ $certificate->issued_at->format('d/m/Y') }}</div>
                                    <div class="text-[10px] text-slate-400 font-black uppercase tracking-widest opacity-60">{{ $certificate->issued_at->format('H:i') }}</div>
                                @else
                                    <span class="text-[10px] text-slate-300 font-black uppercase tracking-widest italic opacity-40">Pendiente</span>
                                @endif
                            </td>

                            <!-- Email Info -->
                            <td class="px-8 py-6">
                                @if($certificate->email_to)
                                    <div class="text-sm font-bold text-slate-700 truncate max-w-[180px] mb-1 group-hover/item:text-slate-900 transition-colors" title="{{ $certificate->email_to }}">
                                        {{ $certificate->email_to }}
                                    </div>
                                    @if($certificate->email_sent_at)
                                        <div class="text-[9px] text-emerald-500 font-black uppercase tracking-widest flex items-center">
                                            <i class="fas fa-check-double mr-1.5 text-[8px]"></i>
                                            Enviado: {{ $certificate->email_sent_at->format('d/m/Y') }}
                                        </div>
                                    @endif
                                @else
                                    <span class="text-[10px] text-slate-300 font-black uppercase tracking-widest italic opacity-40">No enviado</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.certificates.show', $certificate) }}"
                                        class="w-11 h-11 rounded-2xl bg-slate-50 text-slate-400 hover:bg-sena hover:text-white transition-all shadow-sm active:scale-90 flex items-center justify-center border border-slate-100"
                                        title="Ver Detalles">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>

                                    @if($certificate->pdf_path && Storage::exists($certificate->pdf_path))
                                        <a href="{{ route('admin.certificates.download', $certificate) }}"
                                            class="w-11 h-11 rounded-2xl bg-slate-50 text-slate-400 hover:bg-blue-500 hover:text-white transition-all shadow-sm active:scale-90 flex items-center justify-center border border-slate-100"
                                            title="Descargar PDF">
                                            <i class="fas fa-download text-sm"></i>
                                        </a>

                                        <button type="button"
                                            onclick="sendEmail({{ $certificate->id }}, '{{ $certificate->email_to ?? $certificate->apprentice->email }}')"
                                            class="w-11 h-11 rounded-2xl bg-slate-50 text-slate-400 hover:bg-amber-500 hover:text-white transition-all shadow-sm active:scale-90 flex items-center justify-center border border-slate-100"
                                            title="Reenviar por Correo">
                                            <i class="fas fa-paper-plane text-sm"></i>
                                        </button>
                                    @else
                                        <button type="button"
                                            onclick="generatePDF({{ $certificate->id }})"
                                            class="w-11 h-11 rounded-2xl bg-slate-50 text-slate-400 hover:bg-emerald-500 hover:text-white transition-all shadow-sm active:scale-90 flex items-center justify-center border border-slate-100"
                                            title="Generar PDF">
                                            <i class="fas fa-magic text-sm"></i>
                                        </button>
                                    @endif

                                    <form action="{{ route('admin.certificates.destroy', $certificate) }}"
                                        method="POST" class="inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="w-11 h-11 rounded-2xl bg-slate-50 text-slate-400 hover:bg-rose-500 hover:text-white transition-all shadow-sm active:scale-90 flex items-center justify-center border border-slate-100 delete-btn"
                                            title="Anular Certificado">
                                            <i class="fas fa-ban text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-24 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="relative w-24 h-24 mb-6">
                                        <div class="absolute inset-0 bg-slate-50 rounded-full blur-2xl opacity-70 scale-150"></div>
                                        <div class="relative z-10 w-full h-full bg-white text-slate-200 rounded-[2rem] flex items-center justify-center border border-slate-100 shadow-inner">
                                            <i class="fas fa-award text-4xl opacity-30"></i>
                                        </div>
                                    </div>
                                    <h3 class="text-lg font-black text-slate-800 font-outfit uppercase tracking-tighter mb-2">No se encontraron certificados</h3>
                                    <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest max-w-[280px] mx-auto leading-relaxed">
                                        Intente ajustar sus filtros de búsqueda o emita un nuevo certificado para comenzar.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(isset($certificates) && $certificates->hasPages())
            <div class="px-8 py-8 border-t border-slate-100 bg-slate-50/30">
                {{ $certificates->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Confirmación de eliminación con SweetAlert2
            $('.delete-btn').on('click', function (e) {
                const form = $(this).closest('form');
                
                Swal.fire({
                    title: '¿Anular certificado?',
                    text: "Esta acción marcará el certificado como anulado y no podrá ser utilizado.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#F43F5E',
                    cancelButtonColor: '#64748B',
                    confirmButtonText: 'Sí, anularlo',
                    cancelButtonText: 'Cancelar',
                    customClass: {
                        popup: 'rounded-[2rem]',
                        confirmButton: 'rounded-xl px-6 py-3 font-bold uppercase tracking-widest text-xs',
                        cancelButton: 'rounded-xl px-6 py-3 font-bold uppercase tracking-widest text-xs'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        function generatePDF(certificateId) {
            Swal.fire({
                title: '¿Generar PDF?',
                text: 'Se creará el documento oficial del certificado.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#64748B',
                confirmButtonText: 'Sí, generar',
                cancelButtonText: 'Cancelar',
                customClass: {
                    confirmButton: 'rounded-xl px-4 py-2 font-medium',
                    cancelButton: 'rounded-xl px-4 py-2 font-medium'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Generando...',
                        text: 'Espere un momento mientras se crea el documento.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch(`/admin/certificates/${certificateId}/generate`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: '¡Éxito!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonColor: '#10B981',
                                customClass: { confirmButton: 'rounded-xl px-4 py-2 font-medium' }
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.message,
                                icon: 'error',
                                confirmButtonColor: '#EF4444',
                                customClass: { confirmButton: 'rounded-xl px-4 py-2 font-medium' }
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'Ocurrió un error inesperado al generar el PDF.',
                            icon: 'error',
                            confirmButtonColor: '#EF4444',
                            customClass: { confirmButton: 'rounded-xl px-4 py-2 font-medium' }
                        });
                    });
                }
            });
        }

        function sendEmail(certificateId, email) {
            Swal.fire({
                title: '¿Enviar por Correo?',
                text: `Se enviará el certificado a: ${email}`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4F46E5',
                cancelButtonColor: '#64748B',
                confirmButtonText: 'Sí, enviar',
                cancelButtonText: 'Cancelar',
                customClass: {
                    confirmButton: 'rounded-xl px-4 py-2 font-medium',
                    cancelButton: 'rounded-xl px-4 py-2 font-medium'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Enviando...',
                        text: 'Espere un momento mientras realizamos el envío.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch(`/admin/certificates/${certificateId}/send`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: '¡Enviado!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonColor: '#10B981',
                                customClass: { confirmButton: 'rounded-xl px-4 py-2 font-medium' }
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.message,
                                icon: 'error',
                                confirmButtonColor: '#EF4444',
                                customClass: { confirmButton: 'rounded-xl px-4 py-2 font-medium' }
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'Ocurrió un error inesperado al enviar el correo.',
                            icon: 'error',
                            confirmButtonColor: '#EF4444',
                            customClass: { confirmButton: 'rounded-xl px-4 py-2 font-medium' }
                        });
                    });
                }
            });
        }
    </script>
@endpush
