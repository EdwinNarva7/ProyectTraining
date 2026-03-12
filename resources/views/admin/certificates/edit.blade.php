@extends('layouts.master')

@section('title', 'Editar Certificado - SIAP Admin')
@section('page-title', 'Editar Certificado')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.certificates.index') }}">Certificados</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.certificates.show', $certificate) }}">Detalles</a></li>
<li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<!-- Header Section -->
<div class="mb-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Certificate</h1>
            <p class="text-gray-500 text-sm">Update issuance details or status for #{{ $certificate->id }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.certificates.show', $certificate) }}" 
               class="inline-flex items-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                View Details
            </a>
            <a href="{{ route('admin.certificates.index') }}" 
               class="inline-flex items-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to List
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Left Column - Form -->
    <div class="lg:col-span-2">
        <form id="certificate-form" action="{{ route('admin.certificates.update', $certificate) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Form Section 1: Apprentice & Hours -->
                <div class="p-8 border-b border-gray-100">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Issuance Core Details</h2>
                            <p class="text-sm text-gray-500">Update the primary record info</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Apprentice Selection -->
                        <div>
                            <label for="apprentice_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Apprentice <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <select name="apprentice_id" id="apprentice_id" required
                                        class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('apprentice_id') border-red-300 focus:ring-red-500 @enderror">
                                    @foreach($eligibleApprentices as $apprentice)
                                    <option value="{{ $apprentice->id }}" 
                                            {{ old('apprentice_id', $certificate->apprentice_id) == $apprentice->id ? 'selected' : '' }}
                                            data-hours="{{ $apprentice->hours_completed }}"
                                            data-email="{{ $apprentice->email }}"
                                            data-cohort="{{ $apprentice->apprenticeProfile?->cohort ?? 'N/A' }}">
                                        {{ $apprentice->full_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('apprentice_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hours Completed -->
                        <div>
                            <label for="hours_completed" class="block text-sm font-medium text-gray-700 mb-2">
                                Completed Hours <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <input type="number" name="hours_completed" id="hours_completed" required step="0.5" min="80"
                                       class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('hours_completed') border-red-300 focus:ring-red-500 @enderror"
                                       value="{{ old('hours_completed', $certificate->hours_completed) }}" placeholder="e.g. 120">
                            </div>
                            @error('hours_completed')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Section 2: Status & Delivery -->
                <div class="p-8 bg-gray-50 border-b border-gray-100">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Status & Delivery</h2>
                            <p class="text-sm text-gray-500">Manage issuance lifecycle and contact</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Status Selection -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Record Status <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <select name="status" id="status" required
                                        class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('status') border-red-300 focus:ring-red-500 @enderror">
                                    @php($current = old('status', $certificate->status))
                                    <option value="generado" {{ $current === 'generado' ? 'selected' : '' }}>Generado</option>
                                    <option value="enviado" {{ $current === 'enviado' ? 'selected' : '' }}>Enviado</option>
                                    <option value="descargado" {{ $current === 'descargado' ? 'selected' : '' }}>Descargado</option>
                                    <option value="anulado" {{ $current === 'anulado' ? 'selected' : '' }}>Anulado</option>
                                </select>
                            </div>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email Delivery -->
                        <div>
                            <label for="email_to" class="block text-sm font-medium text-gray-700 mb-2">
                                Recipient Email
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <input type="email" name="email_to" id="email_to" 
                                       class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('email_to') border-red-300 focus:ring-red-500 @enderror"
                                       value="{{ old('email_to', $certificate->email_to) }}" placeholder="Recipient email">
                            </div>
                            @error('email_to')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Footer / Action -->
                <div class="p-8 flex justify-end gap-4 bg-gray-50/50">
                    <a href="{{ route('admin.certificates.show', $certificate) }}" 
                       class="px-6 py-3 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition drop-shadow-sm">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:scale-[1.02]">
                        Update Record
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Right Column - Sidebar Context -->
    <div class="space-y-6">
        <!-- Live Preview / Selection Details -->
        <div id="apprentice-preview" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all duration-300">
            <h3 class="text-sm font-semibold text-gray-900 mb-6 flex items-center gap-2 uppercase tracking-wider">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Current Data
            </h3>
            
            <div id="preview-content" class="space-y-4">
                <div class="flex items-center gap-4 mb-6">
                    <div id="preview-avatar" class="w-14 h-14 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xl font-bold shadow-md">
                        {{ strtoupper(substr($certificate->apprentice->full_name, 0, 2)) }}
                    </div>
                    <div>
                        <p id="preview-name" class="text-lg font-bold text-gray-900 leading-tight">{{ $certificate->apprentice->full_name }}</p>
                        <p id="preview-email" class="text-xs text-gray-500">{{ $certificate->apprentice->email }}</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between items-center text-sm p-3 bg-gray-50 rounded-lg border border-gray-100">
                        <span class="text-gray-500">Certificate ID:</span>
                        <span class="font-bold text-gray-900 font-mono">#{{ $certificate->id }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm p-3 bg-gray-50 rounded-lg border border-gray-100">
                        <span class="text-gray-500">Cohort / Ficha:</span>
                        <span id="preview-cohort" class="font-bold text-gray-900">{{ $certificate->apprentice->apprenticeProfile?->cohort ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm p-3 bg-gray-50 rounded-lg border border-gray-100">
                        <span class="text-gray-500">Issuance Date:</span>
                        <span class="font-bold text-gray-900">{{ $certificate->issued_at ? $certificate->issued_at->format('d/m/Y') : 'Not issued' }}</span>
                    </div>
                    
                    @if($certificate->pdf_path)
                    <div class="flex items-center gap-2 p-3 bg-emerald-50 rounded-lg border border-emerald-100">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-xs text-emerald-700 font-medium uppercase tracking-tighter">PDF File Generated</span>
                    </div>
                    @else
                    <div class="flex items-center gap-2 p-3 bg-amber-50 rounded-lg border border-amber-100">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <span class="text-xs text-amber-700 font-medium uppercase tracking-tighter">PDF Pending Generation</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">Record History</h3>
            <div class="space-y-4">
                <div class="flex gap-3">
                    <div class="w-1.5 h-1.5 rounded-full bg-blue-500 mt-1.5"></div>
                    <div>
                        <p class="text-xs font-bold text-gray-900">Created</p>
                        <p class="text-[10px] text-gray-500">{{ $certificate->created_at->format('d M, Y \a\t H:i') }}</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="w-1.5 h-1.5 rounded-full bg-indigo-500 mt-1.5"></div>
                    <div>
                        <p class="text-xs font-bold text-gray-900">Modified</p>
                        <p class="text-[10px] text-gray-500 text-blue-600 font-medium">Auto-updated metadata</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const apprenticeSelect = $('#apprentice_id');

    apprenticeSelect.on('change', function() {
        const selected = $(this).find('option:selected');
        const id = $(this).val();

        if (id) {
            const hours = selected.data('hours');
            const email = selected.data('email');
            const cohort = selected.data('cohort');
            const name = selected.text().trim();

            // Update Preview
            $('#preview-name').text(name);
            $('#preview-email').text(email);
            $('#preview-cohort').text(cohort);
            $('#preview-avatar').text(name.substring(0, 2).toUpperCase());
            
            // Only update fields if they were default/empty or match the previous data
            if ($('#email_to').val() === '' || $('#email_to').val() === '{{ $certificate->email_to }}') {
                $('#email_to').val(email);
            }
        }
    });

    // Form Submission with SweetAlert2
    $('#certificate-form').on('submit', function(e) {
        e.preventDefault();
        const form = this;

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Update Certificate?',
                text: "The details for this record will be modified.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#4F46E5',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, update now',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'rounded-xl px-6 py-2.5 font-medium',
                    cancelButton: 'rounded-xl px-6 py-2.5 font-medium'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        } else {
            if (confirm('Are you sure you want to update this record?')) {
                form.submit();
            }
        }
    });
});
</script>
@endpush


