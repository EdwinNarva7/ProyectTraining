@extends('layouts.master')

@section('title', 'Asistencia - SIAP Admin')
@section('page-title', 'Dashboard de Asistencia')

@section('breadcrumb')
    <li class="breadcrumb-item active">Asistencia</li>
@endsection

@section('content')
    <!-- Header Section -->
    <div class="mb-10">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h1 class="text-4xl font-extrabold text-slate-900 mb-2 font-outfit tracking-tight">Gestión de <span class="text-sena">Asistencia</span></h1>
                    <p class="text-slate-500 font-medium font-outfit text-lg">Panel de control avanzado para el seguimiento institucional en tiempo real.</p>
                </div>
                <div class="flex gap-4">
                    <a href="{{ route('admin.attendance.logs') }}"
                        class="btn-primary-unified flex items-center gap-2 px-6 py-4 shadow-sena group">
                        <i class="fas fa-list-ul group-hover:rotate-12 transition-transform"></i>
                        Ver Historial Completo
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <!-- Total Apprentices -->
            <div class="group relative bg-white rounded-[2.5rem] p-8 shadow-premium border border-slate-100 hover:border-blue-200 transition-all duration-500 overflow-hidden hover:-translate-y-2">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-700 opacity-50"></div>
                <div class="relative z-10 flex flex-col h-full">
                    <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 mb-6 group-hover:rotate-12 transition-all shadow-sm">
                        <i class="fas fa-user-graduate text-3xl"></i>
                    </div>
                    <div class="flex items-baseline gap-2 mb-1">
                        <span class="text-4xl font-extrabold text-slate-800 font-outfit tracking-tighter">{{ $totalApprentices ?? 0 }}</span>
                    </div>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-[0.2em]">Aprendices Activos</p>
                </div>
            </div>

            <!-- Present Today -->
            <div class="group relative bg-white rounded-[2.5rem] p-8 shadow-premium border border-slate-100 hover:border-emerald-200 transition-all duration-500 overflow-hidden hover:-translate-y-2">
                <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-700 opacity-50"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600 mb-6 group-hover:rotate-12 transition-all shadow-sm">
                        <i class="fas fa-user-check text-3xl"></i>
                    </div>
                    <div class="flex items-baseline gap-2 mb-1">
                        <span class="text-4xl font-extrabold text-slate-800 font-outfit tracking-tighter">{{ $presentToday ?? 0 }}</span>
                    </div>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-[0.2em]">Presentes Hoy</p>
                </div>
            </div>

            <!-- Lates Today -->
            <div class="group relative bg-white rounded-[2.5rem] p-8 shadow-premium border border-slate-100 hover:border-amber-200 transition-all duration-500 overflow-hidden hover:-translate-y-2">
                <div class="absolute top-0 right-0 w-32 h-32 bg-amber-50 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-700 opacity-50"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center text-amber-600 mb-6 group-hover:rotate-12 transition-all shadow-sm">
                        <i class="fas fa-clock text-3xl"></i>
                    </div>
                    <div class="flex items-baseline gap-2 mb-1">
                        <span class="text-4xl font-extrabold text-slate-800 font-outfit tracking-tighter">{{ $lateToday ?? 0 }}</span>
                    </div>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-[0.2em]">Tardanzas</p>
                </div>
            </div>

            <!-- Hours Accumulated -->
            <div class="group relative bg-white rounded-[2.5rem] p-8 shadow-premium border border-slate-100 hover:border-indigo-200 transition-all duration-500 overflow-hidden hover:-translate-y-2">
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-700 opacity-50"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600 mb-6 group-hover:rotate-12 transition-all shadow-sm">
                        <i class="fas fa-history text-3xl"></i>
                    </div>
                    <div class="flex items-baseline gap-1 mb-1">
                        <span class="text-4xl font-extrabold text-slate-800 font-outfit tracking-tighter">{{ number_format($totalHours ?? 0, 1) }}h</span>
                    </div>
                    <p class="text-slate-500 text-xs font-bold uppercase tracking-[0.2em]">Horas Acumuladas</p>
                </div>
            </div>
        </div>

        <!-- Chart and Current Status -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
            <!-- Weekly Attendance Chart -->
            <div class="lg:col-span-2 bg-white rounded-[2.5rem] shadow-premium border border-slate-100 overflow-hidden group">
                <div class="p-8 border-b border-slate-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 shadow-inner group-hover:scale-110 transition-transform">
                                <i class="fas fa-chart-line text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-800 font-outfit">Asistencia Semanal</h2>
                                <p class="text-sm text-slate-500 font-medium">Progreso porcentual de los últimos 7 días</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-8 bg-gradient-to-b from-white to-slate-50/50">
                    <canvas id="attendanceChart" style="min-height: 350px; height: 350px;"></canvas>
                </div>
            </div>

            <!-- Current Status -->
            <div class="bg-white rounded-[2.5rem] shadow-premium border border-slate-100 overflow-hidden group">
                <div class="p-8 border-b border-slate-50 text-center">
                    <div class="w-16 h-16 bg-purple-50 rounded-2xl flex items-center justify-center text-purple-600 shadow-inner mx-auto mb-4">
                        <i class="fas fa-bullseye text-2xl animate-pulse"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800 font-outfit tracking-tight">Estado Actual</h2>
                    <p class="text-sm text-slate-500 font-medium">Monitoreo de participación instantánea</p>
                </div>
                <div class="p-8 space-y-6">
                    <!-- Present Status -->
                    <div class="bg-gradient-to-br from-emerald-50 to-white rounded-3xl p-8 border border-emerald-100 group/stat hover:shadow-lg transition-all">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 bg-sena rounded-2xl flex items-center justify-center text-white shadow-sena group-hover/stat:rotate-12 transition-transform">
                                    <i class="fas fa-check text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 uppercase font-black tracking-widest leading-none mb-1">Presentes</p>
                                    <p class="text-4xl font-extrabold text-slate-800 font-outfit">{{ $presentToday ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden shadow-inner p-0.5">
                            <div class="bg-sena h-full rounded-full transition-all duration-1000 shadow-sm"
                                style="width: {{ $attendanceRate ?? 0 }}%"></div>
                        </div>
                        <div class="flex justify-between items-center mt-4">
                            <p class="text-xs text-sena font-black italic tracking-widest uppercase">{{ $attendanceRate ?? 0 }}% cumplimiento</p>
                        </div>
                    </div>

                    <!-- Status Grid -->
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Late Status -->
                        <div class="bg-amber-50 rounded-3xl p-6 border border-amber-100 text-center group/sub shadow-sm hover:shadow-md transition-all">
                            <div class="w-12 h-12 bg-amber-500 rounded-2xl flex items-center justify-center text-white mx-auto mb-4 shadow-lg shadow-amber-200 group-hover/sub:scale-110 transition-transform">
                                <i class="fas fa-clock"></i>
                            </div>
                            <p class="text-[10px] text-slate-400 font-black uppercase tracking-wider mb-2">Tardes</p>
                            <p class="text-2xl font-extrabold text-slate-800 font-outfit">{{ $lateToday ?? 0 }}</p>
                        </div>

                        <!-- Absent Status -->
                        <div class="bg-rose-50 rounded-3xl p-6 border border-rose-100 text-center group/sub shadow-sm hover:shadow-md transition-all">
                            <div class="w-12 h-12 bg-rose-500 rounded-2xl flex items-center justify-center text-white mx-auto mb-4 shadow-lg shadow-rose-200 group-hover/sub:scale-110 transition-transform">
                                <i class="fas fa-times"></i>
                            </div>
                            <p class="text-[10px] text-slate-400 font-black uppercase tracking-wider mb-2">Ausentes</p>
                            <p class="text-2xl font-extrabold text-slate-800 font-outfit">{{ $absentToday ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            initAttendanceChart();
            fetchWeeklyDataAndRender();
            setInterval(fetchWeeklyDataAndRender, 60000);
        });

        let attendanceChart;
        function initAttendanceChart() {
            const ctx = document.getElementById('attendanceChart');
            if (!ctx) return;

            const gradient1 = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
            gradient1.addColorStop(0, 'rgba(12, 190, 100, 0.4)');
            gradient1.addColorStop(1, 'rgba(12, 190, 100, 0.0)');

            const gradient2 = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
            gradient2.addColorStop(0, 'rgba(239, 68, 68, 0.4)');
            gradient2.addColorStop(1, 'rgba(239, 68, 68, 0.0)');

            attendanceChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Presentes %',
                        data: [],
                        borderColor: '#0CBE64',
                        backgroundColor: gradient1,
                        borderWidth: 4,
                        fill: true,
                        tension: 0.45,
                        pointRadius: 6,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#0CBE64',
                        pointBorderWidth: 3,
                        pointHoverRadius: 8,
                        pointHoverBackgroundColor: '#0CBE64',
                        pointHoverBorderColor: '#fff',
                    }, {
                        label: 'Ausentes %',
                        data: [],
                        borderColor: '#EF4444',
                        backgroundColor: gradient2,
                        borderWidth: 4,
                        fill: true,
                        tension: 0.45,
                        pointRadius: 6,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#EF4444',
                        pointBorderWidth: 3,
                        pointHoverRadius: 8,
                        pointHoverBackgroundColor: '#EF4444',
                        pointHoverBorderColor: '#fff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 25,
                                font: {
                                    size: 13,
                                    family: "'Outfit', sans-serif",
                                    weight: '700'
                                },
                                color: '#64748b'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.95)',
                            padding: 16,
                            titleFont: { family: "'Outfit', sans-serif", size: 14, weight: '700' },
                            bodyFont: { family: "'Outfit', sans-serif", size: 13 },
                            cornerRadius: 16,
                            displayColors: true,
                            boxPadding: 8,
                            callbacks: {
                                label: function (context) {
                                    return context.dataset.label + ': ' + context.parsed.y + '%';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.03)',
                                drawBorder: false
                            },
                            ticks: {
                                font: { family: "'Outfit', sans-serif", size: 12, weight: '600' },
                                color: '#94a3b8',
                                padding: 10,
                                callback: function (value) { return value + '%'; }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { family: "'Outfit', sans-serif", size: 12, weight: '600' },
                                color: '#94a3b8',
                                padding: 10
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        }

        function fetchWeeklyDataAndRender() {
            fetch('{{ route("admin.attendance.weekly-data") }}', {
                headers: { 'Accept': 'application/json' }
            })
                .then(resp => resp.json())
                .then(data => {
                    if (!data.success || !attendanceChart) return;
                    attendanceChart.data.labels = data.labels;
                    attendanceChart.data.datasets[0].data = data.rates;
                    // Calculate inverse rate for absent if not provided directly
                    attendanceChart.data.datasets[1].data = data.rates.map(r => 100 - r);
                    attendanceChart.update();
                })
                .catch(() => { /* silent fail */ });
        }
    </script>
@endpush