@extends('layouts.master')

@section('title', 'Ver Certificado - SIAP Admin')
@section('page-title', 'Detalles del Certificado')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.certificates.index') }}">Certificados</a></li>
    <li class="breadcrumb-item active">Ver</li>
@endsection

@section('content')
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Certificate Details</h1>
                <p class="text-gray-500 text-sm">Detailed information and issuance status</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.certificates.edit', $certificate) }}"
                    class="inline-flex items-center px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-xl transition-all duration-200 shadow-lg shadow-amber-500/30">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                    Edit Certificate
                </a>
                <a href="{{ route('admin.certificates.index') }}"
                    class="inline-flex items-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Sidebar - Apprentice Info -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <!-- Avatar -->
                <div class="flex justify-center mb-6">
                    <div
                        class="w-24 h-24 rounded-full bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center text-white text-3xl font-bold shadow-lg">
                        {{ strtoupper(substr($certificate->apprentice->full_name, 0, 2)) }}
                    </div>
                </div>

                <!-- Name & Role -->
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $certificate->apprentice->full_name }}</h2>
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-50 text-blue-700 border border-blue-100">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 14l9-5-9-5-9 5 9 5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z">
                            </path>
                        </svg>
                        Apprentice
                    </span>
                </div>

                <!-- Info List -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500 font-medium">Email</p>
                            <p class="text-sm text-gray-900 truncate">{{ $certificate->apprentice->email }}</p>
                        </div>
                    </div>

                    @if($certificate->apprentice->apprenticeProfile?->cohort)
                        <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-gray-500 font-medium">Cohort / Ficha</p>
                                <p class="text-sm text-gray-900 truncate">
                                    {{ $certificate->apprentice->apprenticeProfile->cohort }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mt-6 pt-6 border-t border-gray-100 flex flex-col gap-3">
                    @if($certificate->pdf_path && Storage::exists($certificate->pdf_path))
                        <a href="{{ route('admin.certificates.download', $certificate) }}"
                            class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-colors shadow-lg shadow-blue-500/20">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Download PDF
                        </a>

                        <button type="button"
                            onclick="sendEmail({{ $certificate->id }}, '{{ $certificate->email_to ?? $certificate->apprentice->email }}')"
                            class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-500/20">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                            Send via Email
                        </button>
                    @endif

                    @if(!$certificate->pdf_path || !Storage::exists($certificate->pdf_path))
                        <button type="button" onclick="generatePDF({{ $certificate->id }})"
                            class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-emerald-600 text-white font-medium rounded-xl hover:bg-emerald-700 transition-colors shadow-lg shadow-emerald-500/20">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Generate PDF
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Content - Certificate Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Status and Main Info -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Certificate Information</h2>
                        <p class="text-sm text-gray-500">Official record details</p>
                    </div>
                    <div>
                        @if($certificate->status === 'generado')
                            <span
                                class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2 animate-pulse"></span>
                                Generado
                            </span>
                        @elseif($certificate->status === 'enviado')
                            <span
                                class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium bg-amber-50 text-amber-700 border border-amber-100">
                                Enviado
                            </span>
                        @elseif($certificate->status === 'descargado')
                            <span
                                class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                Descargado
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium bg-gray-50 text-gray-700 border border-gray-100">
                                {{ ucfirst($certificate->status) }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- ID -->
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Certificate ID</p>
                            <p class="text-lg font-bold text-gray-900">#{{ $certificate->id }}</p>
                        </div>
                    </div>

                    <!-- Hours -->
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Completed Hours</p>
                            <p class="text-lg font-bold text-gray-900">{{ $certificate->hours_completed }} Hours</p>
                        </div>
                    </div>

                    <!-- Issued At -->
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Issuance Date</p>
                            <p class="text-lg font-bold text-gray-900">
                                {{ $certificate->issued_at ? $certificate->issued_at->format('d M, Y H:i') : 'Not issued yet' }}
                            </p>
                        </div>
                    </div>

                    <!-- Recipient Email -->
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Recipient Email</p>
                            <p class="text-lg font-bold text-gray-900 truncate max-w-[200px]"
                                title="{{ $certificate->email_to }}">
                                {{ $certificate->email_to ?? 'Not specified' }}
                            </p>
                        </div>
                    </div>
                </div>

                @if($certificate->email_sent_at)
                    <div class="px-8 py-4 bg-emerald-50/50 border-t border-emerald-100 flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm text-emerald-700">
                            Successfully sent to {{ $certificate->email_to }} on <span
                                class="font-bold">{{ $certificate->email_sent_at->format('d/m/Y \a\t H:i') }}</span>
                        </p>
                    </div>
                @endif
            </div>

            <!-- System Metadata -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-6">System Metadata</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Created By</label>
                        <p class="text-sm text-gray-900 font-semibold">
                            {{ $certificate->createdBy->full_name ?? 'System Identity' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Last Updated</label>
                        <p class="text-sm text-gray-900 font-semibold">{{ $certificate->updated_at->diffForHumans() }}</p>
                    </div>
                    @if($certificate->pdf_path)
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">File Location</label>
                            <p
                                class="text-xs text-blue-600 break-all bg-blue-50 p-2 rounded-lg border border-blue-100 font-mono">
                                {{ $certificate->pdf_path }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function generatePDF(certificateId) {
            cancelButton: 'rounded-xl px-4 py-2 font-medium'
        }
                    }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Generating...',
                    text: 'Please wait while we create your document.',
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
                    },
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Success!',
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
                        Swal.fire({
                            title: 'Error',
                            text: 'An unexpected error occurred during generation.',
                            icon: 'error',
                            confirmButtonColor: '#EF4444',
                            customClass: { confirmButton: 'rounded-xl px-4 py-2 font-medium' }
                        });
                    });
            }
        });
                } else {
            if (confirm('¿Generar PDF?')) {
                // Fallback basic fetch logic
            }
        }
            }
    </script>
@endpush