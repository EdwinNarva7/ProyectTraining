@extends('layouts.master')

@section('title', 'Horarios - SIAP Admin')
@section('page-title', 'Gestión de Horarios')

@section('breadcrumb')
    <li class="breadcrumb-item active">Horarios</li>
@endsection

@section('content')
    <!-- Header Section -->
    <div class="mb-10">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-4xl font-extrabold text-slate-900 mb-2 font-outfit tracking-tight">Gestión de Horarios</h1>
                <p class="text-slate-500 font-medium">Planificación y seguimiento de jornadas para los aprendices.</p>
            </div>
            <div class="flex gap-4">
                <a href="{{ route('admin.schedules.create') }}"
                    class="btn-primary-unified flex items-center gap-2 px-6 py-3 shadow-sena">
                    <i class="fas fa-plus-circle"></i>
                    Asignar Nuevo Horario
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
        <div class="bg-white p-8 rounded-[2rem] shadow-premium border border-slate-100 group hover:-translate-y-2 transition-all">
            <div class="flex items-center justify-between mb-6">
                <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 shadow-inner group-hover:scale-110 transition-transform">
                    <i class="fas fa-calendar-alt text-xl"></i>
                </div>
                <span class="text-[10px] font-bold text-blue-500 bg-blue-50 px-3 py-1 rounded-lg uppercase tracking-wider">Total</span>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-slate-800 font-outfit mb-1">{{ count($schedules ?? []) }}</h3>
                <p class="text-sm text-slate-400 font-medium italic">Horarios registrados</p>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2rem] shadow-premium border border-slate-100 group hover:-translate-y-2 transition-all">
            <div class="flex items-center justify-between mb-6">
                <div class="w-12 h-12 bg-sena/10 rounded-2xl flex items-center justify-center text-sena shadow-inner group-hover:scale-110 transition-transform">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <span class="text-[10px] font-bold text-sena bg-sena/10 px-3 py-1 rounded-lg uppercase tracking-wider">Activos</span>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-slate-800 font-outfit mb-1">{{ collect($schedules)->where('status', 'activo')->count() }}</h3>
                <p class="text-sm text-slate-400 font-medium italic">En vigencia</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-[2rem] shadow-premium border border-slate-100 p-8 mb-12 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-slate-50 rounded-full blur-3xl opacity-50 -mr-10 -mt-10"></div>
        
        <form method="GET" action="{{ route('admin.schedules.index') }}"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 relative z-10">
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 ml-1">Aprendiz</label>
                <select name="apprentice_id"
                    class="form-input-tailwind w-full pl-5 pr-10 py-3 rounded-2xl bg-slate-50/50 border-slate-100 focus:bg-white transition-all appearance-none cursor-pointer">
                    <option value="">Todos los Aprendices</option>
                    @foreach($apprentices as $apprentice)
                        <option value="{{ $apprentice->id }}" {{ request('apprentice_id') == $apprentice->id ? 'selected' : '' }}>
                            {{ $apprentice->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 ml-1">Día de la Semana</label>
                <select name="weekday"
                    class="form-input-tailwind w-full pl-5 pr-10 py-3 rounded-2xl bg-slate-50/50 border-slate-100 focus:bg-white transition-all appearance-none cursor-pointer">
                    <option value="">Todos los Días</option>
                    @php($days = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'])
                    @foreach($days as $num => $label)
                        <option value="{{ $num }}" {{ request('weekday') == $num ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 ml-1">Estado</label>
                <select name="status"
                    class="form-input-tailwind w-full pl-5 pr-10 py-3 rounded-2xl bg-slate-50/50 border-slate-100 focus:bg-white transition-all appearance-none cursor-pointer">
                    <option value="">Todos los Estados</option>
                    <option value="activo" {{ request('status') == 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ request('status') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>

            <div class="flex items-end gap-3">
                <button type="submit"
                    class="flex-1 sena-gradient hover:opacity-90 text-white font-bold py-3.5 rounded-2xl transition-all shadow-sena shadow-md">
                    <i class="fas fa-filter mr-2 text-xs"></i>
                    Filtrar
                </button>
                <a href="{{ route('admin.schedules.index') }}"
                    class="bg-slate-50 hover:bg-slate-100 text-slate-400 p-3.5 rounded-2xl border border-slate-100 transition-all active:scale-95"
                    title="Limpiar filtros">
                    <i class="fas fa-sync-alt"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-[2.5rem] shadow-premium border border-slate-100 overflow-hidden group mb-12">
        <div class="p-0 overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Aprendiz</th>
                        <th class="px-8 py-5 text-center text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Programación</th>
                        <th class="px-8 py-5 text-center text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Estado</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Registrado</th>
                        <th class="px-8 py-5 text-right text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($schedules as $schedule)
                        <tr class="group/item hover:bg-slate-50/80 transition-all duration-200">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 rounded-2xl sena-gradient flex items-center justify-center text-white font-bold text-lg shadow-sena shadow-md transition-transform group-hover/item:scale-110 group-hover/item:rotate-3 overflow-hidden">
                                        @if($schedule?->apprentice?->profile_photo_path)
                                            <img src="{{ Storage::url($schedule->apprentice->profile_photo_path) }}" class="w-full h-full object-cover">
                                        @else
                                            {{ strtoupper(substr($schedule?->apprentice?->full_name ?? 'N', 0, 1)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 mb-0.5">
                                            {{ $schedule->apprentice->full_name ?? 'N/A' }}
                                        </p>
                                        <p class="text-xs text-slate-400 font-medium italic">{{ $schedule->apprentice->email ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-col items-center">
                                    <span class="inline-flex px-3 py-1 bg-indigo-50 text-indigo-700 text-[10px] font-bold uppercase tracking-wider rounded-lg border border-indigo-100 mb-2">
                                        {{ $schedule->weekday_name ?? 'N/A' }}
                                    </span>
                                    <div class="flex items-center gap-3 text-sm font-extrabold font-outfit">
                                        <span class="text-sena">{{ $schedule->start_time?->format('H:i') ?? '--:--' }}</span>
                                        <span class="text-slate-300">-</span>
                                        <span class="text-rose-500">{{ $schedule->end_time?->format('H:i') ?? '--:--' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                @if(($schedule?->status ?? 'inactivo') === 'activo')
                                    <span class="inline-flex items-center px-4 py-1.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-100">
                                        <i class="fas fa-check-circle mr-1.5 animate-pulse"></i>
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-4 py-1.5 bg-slate-50 text-slate-400 text-[10px] font-bold uppercase tracking-widest rounded-full border border-slate-100">
                                        <i class="fas fa-times-circle mr-1.5"></i>
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-sm font-bold text-slate-800 mb-0.5">
                                    {{ $schedule->created_at?->format('d M, Y') ?? 'N/A' }}
                                </p>
                                <p class="text-[11px] text-slate-400 font-medium">Por: {{ $schedule->createdBy->full_name ?? 'Sistema' }}</p>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.schedules.show', $schedule) }}"
                                        class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-sena hover:text-white transition-all shadow-sm active:scale-95 flex items-center justify-center">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.schedules.edit', $schedule) }}"
                                        class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-amber-500 hover:text-white transition-all shadow-sm active:scale-95 flex items-center justify-center">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST"
                                        class="inline-block delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-rose-500 hover:text-white transition-all shadow-sm active:scale-95 flex items-center justify-center delete-btn">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center">
                                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200">
                                    <i class="fas fa-calendar-times text-4xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-slate-800 mb-2 font-outfit">Sin horarios asignados</h3>
                                <p class="text-slate-500 font-medium">Intente ajustar sus filtros o asigne un nuevo horario.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($schedules) && $schedules->hasPages())
            <div class="px-8 py-5 bg-slate-50/50 border-t border-slate-100">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            function handleDelete(btn) {
                const form = btn.closest('.delete-form');
                if (!form) return;

                if (window.Swal && typeof Swal.fire === 'function') {
                    Swal.fire({
                        title: 'Eliminar Horario',
                        text: 'Esta acción eliminará definitivamente el horario del aprendiz.',
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
                    if (confirm('¿Eliminar este horario? Esta acción no se puede deshacer.')) {
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