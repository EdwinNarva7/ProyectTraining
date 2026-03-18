@extends('layouts.master')

@section('title', 'Fases - SIAP Admin')
@section('page-title', 'Gestión de Fases')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Usuarios</a></li>
    <li class="breadcrumb-item active">Fases</li>
@endsection

@section('content')
    <!-- Header Section -->
    <div class="mb-10">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-4xl font-extrabold text-slate-900 mb-2 font-outfit tracking-tight uppercase">Gestión de <span class="text-sena">Fases</span></h1>
                <p class="text-slate-500 font-bold text-[11px] uppercase tracking-[0.2em] opacity-60">Administre las cohortes o grupos de aprendices que ingresan al sistema.</p>
            </div>
            <div class="flex gap-4">
                <a href="{{ route('admin.phases.create') }}"
                    class="btn-primary-unified flex items-center gap-2 px-8 py-4 shadow-sena rounded-2xl font-bold uppercase tracking-widest text-xs transition-all hover:scale-105 active:scale-95">
                    <i class="fas fa-layer-group text-sm"></i>
                    Crear Nueva Fase
                </a>
            </div>
        </div>
    </div>

    <!-- Phases Table Card -->
    <div class="bg-white rounded-[2.5rem] shadow-premium border border-slate-100 overflow-hidden group mb-12">
        <div class="p-0 overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Nombre de la Fase</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Periodo</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Aprendices</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Estado</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Fase Actual</th>
                        <th class="px-8 py-6 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($phases as $phase)
                        <tr class="group/item hover:bg-slate-50/80 transition-all duration-300">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 rounded-2xl {{ $phase->is_active ? 'sena-gradient shadow-sena shadow-lg' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center font-black text-lg transition-all duration-500">
                                        <i class="fas fa-layer-group {{ $phase->is_active ? 'text-white' : '' }}"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-slate-800 mb-0.5">{{ $phase->name }}</p>
                                        <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest opacity-70">ID: #{{ $phase->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-sm font-black text-slate-800 mb-0.5 tracking-tighter">
                                    {{ $phase->start_date ? $phase->start_date->format('d/m/Y') : 'N/A' }} - 
                                    {{ $phase->end_date ? $phase->end_date->format('d/m/Y') : 'N/A' }}
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="inline-flex px-4 py-2 bg-slate-50 text-slate-700 rounded-xl border border-slate-100 font-black font-outfit text-sm tracking-tighter">
                                    {{ $phase->apprentices_count }} <span class="text-[10px] ml-1.5 opacity-40">APRENDICES</span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="inline-flex items-center px-4 py-2 {{ $phase->status == 'abierta' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100' }} text-[9px] font-black uppercase tracking-[0.15em] rounded-full border shadow-sm">
                                    <i class="fas {{ $phase->status == 'abierta' ? 'fa-unlock' : 'fa-lock' }} mr-2"></i>
                                    {{ ucfirst($phase->status) }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                @if($phase->is_active)
                                    <span class="inline-flex items-center px-4 py-2 bg-sena/10 text-sena text-[9px] font-black uppercase tracking-[0.15em] rounded-full border border-sena/20 shadow-sm animate-pulse">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        ACTIVA
                                    </span>
                                @else
                                    <form action="{{ route('admin.phases.activate', $phase) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-[9px] font-black text-slate-400 hover:text-sena uppercase tracking-widest transition-all">
                                            Activar Ahora
                                        </button>
                                    </form>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.phases.edit', $phase) }}"
                                        class="w-11 h-11 rounded-2xl bg-slate-50 text-slate-400 hover:bg-sena hover:text-white transition-all shadow-sm active:scale-90 flex items-center justify-center border border-slate-100"
                                        title="Editar Fase">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>

                                    <form action="{{ route('admin.phases.destroy', $phase) }}"
                                        method="POST" class="inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="w-11 h-11 rounded-2xl bg-slate-50 text-slate-400 hover:bg-rose-500 hover:text-white transition-all shadow-sm active:scale-90 flex items-center justify-center border border-slate-100 delete-btn"
                                            title="Eliminar Fase">
                                            <i class="fas fa-trash text-sm"></i>
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
                                            <i class="fas fa-layer-group text-4xl opacity-30"></i>
                                        </div>
                                    </div>
                                    <h3 class="text-lg font-black text-slate-800 font-outfit uppercase tracking-tighter mb-2">No se encontraron fases</h3>
                                    <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest max-w-[280px] mx-auto leading-relaxed">
                                        Cree su primera fase para comenzar a organizar a sus aprendices.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($phases->hasPages())
            <div class="px-8 py-8 border-t border-slate-100 bg-slate-50/30">
                {{ $phases->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('.delete-btn').on('click', function (e) {
                const form = $(this).closest('form');
                
                Swal.fire({
                    title: '¿Eliminar fase?',
                    text: "Esta acción no se puede deshacer y solo es posible si la fase no tiene aprendices vinculados.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#F43F5E',
                    cancelButtonColor: '#64748B',
                    confirmButtonText: 'Sí, eliminar',
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
    </script>
@endpush
