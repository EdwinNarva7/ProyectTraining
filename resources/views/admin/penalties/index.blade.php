@extends('layouts.master')

@section('title', 'Penalizaciones - SIAP Admin')
@section('page-title', 'Gestión de Penalizaciones')

@section('breadcrumb')
    <li class="breadcrumb-item active">Penalizaciones</li>
@endsection

@section('content')
    <!-- Header -->
    <div class="mb-10">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-4xl font-extrabold text-slate-900 mb-2 font-outfit tracking-tight">Registro de Sanciones</h1>
                <p class="text-slate-500 font-medium">Monitoreo y seguimiento de deudas de horas por incumplimientos.</p>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-[2rem] shadow-premium border border-slate-100 p-8 mb-12 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-slate-50 rounded-full blur-3xl opacity-50 -mr-10 -mt-10"></div>
        
        <form action="{{ route('admin.penalties.index') }}" method="GET" class="grid grid-cols-1 lg:grid-cols-4 gap-6 relative z-10">
            <div class="lg:col-span-2">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 ml-1">Buscar aprendiz</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                        <i class="fas fa-search text-slate-300"></i>
                    </div>
                    <input type="text" name="search" placeholder="Nombre, correo o documento..." value="{{ request('search') }}"
                        class="form-input-tailwind w-full pl-12 pr-4 py-3 rounded-2xl bg-slate-50/50 border-slate-100 focus:bg-white transition-all">
                </div>
            </div>

            <div class="flex flex-col md:flex-row gap-6 lg:col-span-2 items-end">
                <div class="flex-1 w-full">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 ml-1">Estado</label>
                    <select name="status"
                        class="form-input-tailwind w-full pl-5 pr-10 py-3 rounded-2xl bg-slate-50/50 border-slate-100 appearance-none cursor-pointer">
                        <option value="">Todos los Estados</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                        <option value="in_recovery" {{ request('status') == 'in_recovery' ? 'selected' : '' }}>En Recuperación</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Cerrado / Pagado</option>
                    </select>
                </div>

                <div class="flex gap-3">
                    <button type="submit"
                        class="sena-gradient hover:opacity-90 text-white font-bold px-8 py-3.5 rounded-2xl transition-all shadow-sena shadow-md">
                        <i class="fas fa-filter mr-2 text-xs"></i>
                        Filtrar
                    </button>
                    <a href="{{ route('admin.penalties.index') }}"
                        class="bg-slate-50 hover:bg-slate-100 text-slate-400 p-3.5 rounded-2xl border border-slate-100 transition-all flex items-center justify-center aspect-square"
                        title="Limpiar filtros">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-[2.5rem] shadow-premium border border-slate-100 overflow-hidden group mb-12">
        <div class="p-0 overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Aprendiz</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Fecha</th>
                        <th class="px-8 py-5 text-center text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Programada</th>
                        <th class="px-8 py-5 text-center text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Asistida</th>
                        <th class="px-8 py-5 text-center text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Deuda</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Estado</th>
                        <th class="px-8 py-5 text-right text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($penalties as $penalty)
                        <tr class="group/item hover:bg-slate-50/80 transition-all duration-200">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 rounded-2xl sena-gradient flex items-center justify-center text-white font-bold text-lg shadow-sena shadow-md group-hover/item:scale-110 group-hover/item:rotate-3 transition-transform overflow-hidden">
                                        @if($penalty->apprentice?->profile_photo_path)
                                            <img src="{{ Storage::url($penalty->apprentice->profile_photo_path) }}" class="w-full h-full object-cover">
                                        @else
                                            {{ strtoupper(substr(($penalty->apprentice?->name ?? 'A'), 0, 1)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 mb-0.5">{{ $penalty->apprentice?->name ?? 'Aprendiz sin nombre' }}</p>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ $penalty->apprentice?->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-sm font-bold text-slate-800 mb-0.5">{{ $penalty->date->format('d M, Y') }}</p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest italic">Registro diario</p>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <span class="text-sm font-bold text-slate-500 font-outfit">@hm($penalty->scheduled_hours)</span>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <span class="text-sm font-bold text-slate-500 font-outfit">@hm($penalty->attended_hours)</span>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <div class="inline-flex px-3 py-1 bg-rose-50 text-rose-600 rounded-lg border border-rose-100 font-extrabold font-outfit text-sm">
                                    @hm($penalty->penalty_hours)
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                @if($penalty->status === 'pending')
                                    <span class="inline-flex items-center px-4 py-1.5 bg-amber-50 text-amber-600 text-[10px] font-bold uppercase tracking-widest rounded-full border border-amber-100">
                                        <i class="fas fa-exclamation-circle mr-1.5"></i>
                                        Pendiente
                                    </span>
                                @elseif($penalty->status === 'in_recovery')
                                    <span class="inline-flex items-center px-4 py-1.5 bg-blue-50 text-blue-600 text-[10px] font-bold uppercase tracking-widest rounded-full border border-blue-100 animate-pulse">
                                        <i class="fas fa-sync-alt mr-1.5"></i>
                                        En Proceso
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-4 py-1.5 bg-emerald-50 text-emerald-600 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-100">
                                        <i class="fas fa-check-circle mr-1.5"></i>
                                        Saldado
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex justify-end gap-3">
                                    <button class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 hover:bg-sena hover:text-white transition-all shadow-sm active:scale-95 flex items-center justify-center" title="Ver detalle">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-8 py-20 text-center">
                                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200">
                                    <i class="fas fa-file-invoice mr-1.5 text-4xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-slate-800 mb-2 font-outfit">Sin sanciones registradas</h3>
                                <p class="text-slate-500 font-medium">No se encontraron incumplimientos para los filtros aplicados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($penalties->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                {{ $penalties->links() }}
            </div>
        @endif
    </div>
@endsection
