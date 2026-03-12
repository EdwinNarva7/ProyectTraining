@extends('layouts.master')

@section('title', 'Crear Certificado - SIAP Admin')
@section('page-title', 'Crear Nuevo Certificado')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.certificates.index') }}">Certificados</a></li>
<li class="breadcrumb-item active">Crear</li>
@endsection

@section('content')
<!-- Header Section -->
<div class="mb-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Issue New Certificate</h1>
            <p class="text-gray-500 text-sm">Create a graduation or hours-completion certificate</p>
        </div>
        <div>
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
        <form id="certificate-form" action="{{ route('admin.certificates.store') }}" method="POST">
            @csrf
            
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
                            <h2 class="text-lg font-semibold text-gray-900">Apprentice Information</h2>
                            <p class="text-sm text-gray-500">Select eligible apprentice and confirm hours</p>
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
                                    <option value="">Select an apprentice</option>
                                    @foreach($eligibleApprentices as $apprentice)
                                    <option value="{{ $apprentice->id }}" 
                                            {{ old('apprentice_id') == $apprentice->id ? 'selected' : '' }}
                                            data-hours="{{ $apprentice->hours_completed }}"
                                            data-email="{{ $apprentice->email }}"
                                            data-cohort="{{ $apprentice->apprenticeProfile?->cohort ?? 'N/A' }}">
                                        {{ $apprentice->full_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">Only apprentices with 80+ hours are eligible</p>
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
                                       value="{{ old('hours_completed') }}" placeholder="e.g. 120">
                            </div>
                            @error('hours_completed')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Section 2: Delivery -->
                <div class="p-8 bg-gray-50 border-b border-gray-100">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Delivery Options</h2>
                            <p class="text-sm text-gray-500">Configure how the certificate reaches the apprentice</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Email Delivery -->
                        <div>
                            <label for="email_to" class="block text-sm font-medium text-gray-700 mb-2">
                                Notification Email
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"></path>
                                    </svg>
                                </div>
                                <input type="email" name="email_to" id="email_to" 
                                       class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 transition @error('email_to') border-red-300 focus:ring-red-500 @enderror"
                                       value="{{ old('email_to') }}" placeholder="Enter recipient email">
                            </div>
                            @error('email_to')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Info Box -->
                        <div class="flex items-center p-4 bg-white rounded-xl border border-gray-100 shadow-sm">
                            <div class="w-8 h-8 bg-blue-50 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <p class="text-xs text-gray-600 italic leading-relaxed">
                                The certificate will be generated as a PDF file and can be sent automatically if an email is provided.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Footer / Action -->
                <div class="p-8 flex justify-end gap-4 bg-gray-50/50">
                    <a href="{{ route('admin.certificates.index') }}" 
                       class="px-6 py-3 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition drop-shadow-sm">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl shadow-lg shadow-blue-500/30 transition-all hover:scale-[1.02]">
                        Create Certificate
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Right Column - Sidebar Context -->
    <div class="space-y-6">
        <!-- Live Preview / Selection Details -->
        <div id="apprentice-preview" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 opacity-50 transition-all duration-300">
            <h3 class="text-sm font-semibold text-gray-900 mb-6 flex items-center gap-2 uppercase tracking-wider">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Selection Preview
            </h3>
            
            <div id="preview-no-selection" class="text-center py-8">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <p class="text-sm text-gray-400 font-medium">Select an apprentice to see details</p>
            </div>

            <div id="preview-content" class="hidden space-y-4">
                <div class="flex items-center gap-4 mb-6">
                    <div id="preview-avatar" class="w-14 h-14 rounded-full bg-blue-600 flex items-center justify-center text-white text-xl font-bold shadow-md">
                        JD
                    </div>
                    <div>
                        <p id="preview-name" class="text-lg font-bold text-gray-900 leading-tight">Juan Doe</p>
                        <p id="preview-email" class="text-xs text-gray-500">juan@example.com</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between items-center text-sm p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-500">Cohort / Ficha:</span>
                        <span id="preview-cohort" class="font-bold text-gray-900">ADSO-2024-1</span>
                    </div>
                    <div class="flex justify-between items-center text-sm p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-500">Recorded Hours:</span>
                        <span id="preview-hours" class="font-bold text-blue-600">120.5 hrs</span>
                    </div>
                    <div class="flex items-center gap-2 p-3 bg-emerald-50 rounded-lg border border-emerald-100">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-xs text-emerald-700 font-medium uppercase">Eligible for Certification</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Usage Info -->
        <div class="bg-gradient-to-br from-indigo-900 to-blue-900 rounded-2xl p-6 text-white shadow-xl shadow-indigo-200">
            <h3 class="text-sm font-semibold mb-4 uppercase tracking-wider flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Issuance Rules
            </h3>
            <ul class="space-y-3 text-xs text-indigo-100">
                <li class="flex gap-2">
                    <span class="w-1.5 h-1.5 bg-blue-400 rounded-full mt-1 flex-shrink-0"></span>
                    <span>Min completion: <strong>80 hours</strong>.</span>
                </li>
                <li class="flex gap-2">
                    <span class="w-1.5 h-1.5 bg-blue-400 rounded-full mt-1 flex-shrink-0"></span>
                    <span>Status will be marked as "Generado" after PDF creation.</span>
                </li>
                <li class="flex gap-2">
                    <span class="w-1.5 h-1.5 bg-blue-400 rounded-full mt-1 flex-shrink-0"></span>
                    <span>Emails are sent only if the recipient address is valid.</span>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const apprenticeSelect = $('#apprentice_id');
    const previewBox = $('#apprentice-preview');
    const noSelection = $('#preview-no-selection');
    const previewContent = $('#preview-content');

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
            $('#preview-hours').text(hours + ' hrs');
            $('#preview-avatar').text(name.substring(0, 2).toUpperCase());
            $('#email_to').val(email);
            $('#hours_completed').val(hours);

            // Show Content
            previewBox.removeClass('opacity-50').addClass('shadow-lg shadow-blue-500/10 scale-[1.02]');
            noSelection.addClass('hidden');
            previewContent.removeClass('hidden');
        } else {
            previewBox.addClass('opacity-50').removeClass('shadow-lg shadow-blue-500/10 scale-[1.02]');
            noSelection.removeClass('hidden');
            previewContent.addClass('hidden');
            $('#email_to').val('');
            $('#hours_completed').val('');
        }
    });

    // Form Submission with SweetAlert2
    $('#certificate-form').on('submit', function(e) {
        e.preventDefault();
        const form = this;
        const apprenticeName = $('#apprentice_id option:selected').text();

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Confirm Issuance?',
                text: `You are about to create a certificate for ${apprenticeName}.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3B82F6',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, create it',
                cancelButtonText: 'Adjust',
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
            if (confirm('Are you sure you want to issue this certificate?')) {
                form.submit();
            }
        }
    });
});
</script>
@endpush
