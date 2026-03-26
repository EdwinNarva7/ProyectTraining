@extends('layouts.master')

@section('title', 'Usuarios - SIAP Admin')
@section('page-title', 'Gestión de Usuarios')

@section('breadcrumb')
    <li class="breadcrumb-item active">Usuarios</li>
@endsection

@section('content')
    <!-- Header Section -->
    <div class="mb-10">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-4xl font-extrabold text-slate-900 mb-2 font-outfit tracking-tight">Gestión de Usuarios</h1>
                <p class="text-slate-500 font-medium">Administre y monitoree todos los colaboradores en un solo
                    lugar.</p>
            </div>
            <div class="flex gap-4">
                <a href="{{ route('admin.users.create') }}"
                    class="btn-primary-unified flex items-center gap-2 px-6 py-3 shadow-sena">
                    <i class="fas fa-user-plus"></i>
                    Crear Nuevo Usuario
                </a>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-[2rem] shadow-premium border border-slate-100 p-8 mb-12 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-slate-50 rounded-full blur-3xl opacity-50 -mr-10 -mt-10"></div>

        <form method="GET" action="{{ route('admin.users.index') }}"
            class="grid grid-cols-1 lg:grid-cols-4 gap-6 relative z-10">
            <!-- Search Bar -->
            <div class="lg:col-span-2">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 ml-1">Búsqueda
                    rápida</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                        <i class="fas fa-search text-slate-300"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Nombre, correo o documento..."
                        class="form-input-tailwind w-full pl-12 pr-4 py-3 rounded-2xl bg-slate-50/50 border-slate-100 focus:bg-white transition-all">
                </div>
            </div>

            <!-- Filter Dropdowns -->
            <div class="flex flex-col md:flex-row gap-6 lg:col-span-2 items-end">
                <div class="flex-1 w-full">
                    <label
                        class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 ml-1">Rol</label>
                    <select name="role"
                        class="form-input-tailwind w-full pl-5 pr-10 py-3 rounded-2xl bg-slate-50/50 border-slate-100 appearance-none cursor-pointer">
                        <option value="">Todos los Roles</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="instructor" {{ request('role') == 'instructor' ? 'selected' : '' }}>Instructor</option>
                        <option value="aprendiz" {{ request('role') == 'aprendiz' ? 'selected' : '' }}>Colaborador</option>
                    </select>
                </div>

                <div class="flex-1 w-full">
                    <label
                        class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 ml-1">Fase</label>
                    <select name="phase_id"
                        class="form-input-tailwind w-full pl-5 pr-10 py-3 rounded-2xl bg-slate-50/50 border-slate-100 appearance-none cursor-pointer">
                        @foreach($phases as $phase)
                            <option value="{{ $phase->id }}" {{ (request('phase_id') == $phase->id) ? 'selected' : '' }}>
                                {{ $phase->name }} {{ $phase->is_active ? '(Activa)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-3">
                    <button type="submit"
                        class="sena-gradient hover:opacity-90 text-white font-bold px-8 py-3.5 rounded-2xl transition-all shadow-sena shadow-md">
                        <i class="fas fa-filter mr-2 text-xs"></i>
                        Filtrar
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                        class="bg-slate-50 hover:bg-slate-100 text-slate-400 p-3.5 rounded-2xl border border-slate-100 transition-all flex items-center justify-center aspect-square"
                        title="Limpiar filtros">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Users Table Card -->
    <div class="bg-white rounded-[2.5rem] shadow-premium border border-slate-100 overflow-hidden group mb-12">
        <div class="p-0 overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                            Usuario</th>
                        <th class="px-8 py-5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                            Email</th>
                        <th class="px-8 py-5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Rol
                        </th>
                        <th class="px-8 py-5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                            Estado</th>
                        <th class="px-8 py-5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                            Ficha / Fase</th>
                        <th class="px-8 py-5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                            Creado</th>
                        <th class="px-8 py-5 text-right text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="group/item hover:bg-slate-50/80 transition-all duration-200">
                            <!-- User (Avatar + Name) -->
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        <div
                                            class="h-12 w-12 rounded-2xl sena-gradient flex items-center justify-center text-white font-bold text-lg shadow-sena shadow-md group-hover/item:scale-110 group-hover/item:rotate-3 transition-transform overflow-hidden">
                                            @if($user->profile_photo_path)
                                                <img src="{{ Storage::url($user->profile_photo_path) }}" class="w-full h-full object-cover">
                                            @else
                                                {{ strtoupper(substr($user->full_name, 0, 1)) }}{{ strtoupper(substr(strrchr($user->full_name, " ") ?: " ", 1, 1)) }}
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 mb-0.5">{{ $user->full_name }}</p>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">ID:
                                            #{{ $user->id }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Email -->
                            <td class="px-8 py-6">
                                <div class="text-sm font-medium text-slate-600">{{ $user->email }}</div>
                            </td>

                            <!-- Role -->
                            <td class="px-8 py-6">
                                @if($user->isAdmin())
                                    <span
                                        class="inline-flex items-center px-4 py-1.5 bg-rose-50 text-rose-700 text-[10px] font-bold uppercase tracking-widest rounded-full border border-rose-100">
                                        <i class="fas fa-user-shield mr-1.5"></i>
                                        {{ $user->role->name }}
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-4 py-1.5 bg-blue-50 text-blue-700 text-[10px] font-bold uppercase tracking-widest rounded-full border border-blue-100">
                                        <i class="fas fa-user-graduate mr-1.5"></i>
                                        {{ $user->role->name === 'Aprendiz' ? 'Colaborador' : $user->role->name }}
                                    </span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="px-8 py-6">
                                @if($user->status === 'activo')
                                    <span
                                        class="inline-flex items-center px-4 py-1.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-100">
                                        <i class="fas fa-check-circle mr-1.5 animate-pulse"></i>
                                        {{ ucfirst($user->status) }}
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-4 py-1.5 bg-slate-50 text-slate-400 text-[10px] font-bold uppercase tracking-widest rounded-full border border-slate-100">
                                        <i class="fas fa-times-circle mr-1.5"></i>
                                        {{ ucfirst($user->status) }}
                                    </span>
                                @endif
                            </td>

                            <!-- Ficha / Fase -->
                            <td class="px-8 py-6">
                                @if($user->apprenticeProfile)
                                    <p class="text-sm font-bold text-slate-700">
                                        {{ $user->apprenticeProfile->cohort ?: ($user->apprenticeProfile->fiche_number ?: 'Sin Ficha') }}
                                    </p>
                                    @if($user->apprenticeProfile->phase)
                                        <p class="text-[10px] text-sena font-bold uppercase tracking-widest">{{ $user->apprenticeProfile->phase->name }}</p>
                                    @else
                                        <p class="text-[10px] text-slate-300 font-bold uppercase tracking-widest">Sin Fase</p>
                                    @endif
                                @else
                                    <span class="text-slate-300">---</span>
                                @endif
                            </td>

                            <!-- Created Date -->
                            <td class="px-8 py-6">
                                <div class="text-sm font-bold text-slate-800 mb-0.5">{{ $user->created_at->format('d/m/Y') }}</div>
                                <div class="text-[11px] text-slate-400 font-medium">{{ $user->created_at->format('H:i') }}</div>
                            </td>

                            <!-- Actions -->
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <!-- View Button -->
                                    <a href="{{ route('admin.users.show', $user) }}"
                                        class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-sena hover:text-white transition-all shadow-sm active:scale-95 flex items-center justify-center"
                                        title="Ver Detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <!-- Edit Button -->
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                        class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-amber-500 hover:text-white transition-all shadow-sm active:scale-95 flex items-center justify-center"
                                        title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <!-- Delete Button -->
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                        class="inline-block delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-rose-500 hover:text-white transition-all shadow-sm active:scale-95 flex items-center justify-center delete-btn"
                                            title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                        </path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">No se encontraron usuarios</h3>
                                    <p class="text-gray-500 text-sm">Comienza creando tu primer usuario</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <!-- Results Info -->
                    <div class="text-sm text-gray-600">
                        Mostrando
                        <span class="font-medium text-gray-900">{{ $users->firstItem() }}</span>
                        a
                        <span class="font-medium text-gray-900">{{ $users->lastItem() }}</span>
                        de
                        <span class="font-medium text-gray-900">{{ $users->total() }}</span>
                        resultados
                    </div>

                    <!-- Pagination Links -->
                    <nav class="flex items-center gap-2">
                        {{-- Previous Page Link --}}
                        @if ($users->onFirstPage())
                            <span
                                class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-white border border-gray-200 text-gray-400 cursor-not-allowed">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                                    </path>
                                </svg>
                            </span>
                        @else
                            <a href="{{ $users->previousPageUrl() }}"
                                class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-colors duration-150">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                                    </path>
                                </svg>
                            </a>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                            @if ($page == $users->currentPage())
                                <span
                                    class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-blue-600 text-white font-medium shadow-sm">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}"
                                    class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-colors duration-150">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($users->hasMorePages())
                            <a href="{{ $users->nextPageUrl() }}"
                                class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-colors duration-150">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        @else
                            <span
                                class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-white border border-gray-200 text-gray-400 cursor-not-allowed">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </span>
                        @endif
                    </nav>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const form = document.querySelector('form[action*="admin/users"]');
            const searchInput = form ? form.querySelector('input[name="search"]') : null;
            let debounceTimer;
            if (searchInput && form) {
                searchInput.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        form.submit();
                    }
                });
                searchInput.addEventListener('input', function () {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        form.submit();
                    }, 400);
                });
            }
        })();

        (function () {
            function handleDelete(btn) {
                const form = btn.closest('.delete-form');
                if (!form) return;

                if (window.Swal && typeof Swal.fire === 'function') {
                    Swal.fire({
                        title: '¿Eliminar usuario?',
                        text: 'Esta acción eliminará el usuario y su perfil asociado.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#EF4444',
                        cancelButtonColor: '#6B7280',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        customClass: {
                            confirmButton: 'rounded-xl px-6 py-2.5 font-medium',
                            cancelButton: 'rounded-xl px-6 py-2.5 font-medium'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) form.submit();
                    });
                } else {
                    if (confirm('¿Eliminar este usuario? Esta acción no se puede deshacer.')) {
                        form.submit();
                    }
                }
            }

            if (window.jQuery) {
                $(document).ready(function () {
                    $('.delete-btn').on('click', function () {
                        handleDelete(this);
                    });
                });
            } else {
                document.addEventListener('DOMContentLoaded', function () {
                    document.querySelectorAll('.delete-btn').forEach(function (btn) {
                        btn.addEventListener('click', function () { handleDelete(btn); });
                    });
                });
            }
        })();
    </script>
@endpush
