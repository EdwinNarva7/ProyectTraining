<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function export(Request $request)
    {
        $module = $request->input('module', 'attendance');
        $format = $request->input('format', 'csv'); // csv|pdf

        if ($module === 'attendance') {
            return $this->exportAttendanceDetailed($request, $format);
        }

        if ($module === 'logs') {
            return $this->exportLogs($request, $format);
        }

        return Response::json(['success' => false, 'message' => 'Módulo de reporte no soportado'], 400);
    }

    private function exportAttendanceDetailed(Request $request, string $format)
    {
        $query = AttendanceLog::with(['apprentice.apprenticeProfile', 'createdBy']);

        if ($request->filled('date_from')) {
            $query->whereDate('occurred_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('occurred_at', '<=', $request->date_to);
        } else {
            $query->whereDate('occurred_at', '>=', Carbon::now()->subMonth());
        }
        if ($request->filled('apprentice_id')) {
            $query->where('apprentice_id', $request->apprentice_id);
        }
        if ($request->filled('cohort')) {
            $query->whereHas('apprentice.apprenticeProfile', function ($q) use ($request) {
                $q->where('cohort', 'LIKE', '%' . $request->cohort . '%');
            });
        }

        $logs = $query->orderBy('occurred_at', 'desc')->get();
        $grouped = $logs->groupBy(function ($log) {
            return $log->apprentice_id . '-' . $log->occurred_at->format('Y-m-d');
        });

        $rows = [];
        foreach ($grouped as $dayLogs) {
            $apprentice = $dayLogs->first()->apprentice;
            $date = $dayLogs->first()->occurred_at->format('Y-m-d');
            $entries = $dayLogs->where('event_type', 'entrada')->sortBy('occurred_at');
            $exits = $dayLogs->where('event_type', 'salida')->sortByDesc('occurred_at');
            $firstEntry = $entries->first();
            $lastExit = $exits->first();

            $workedMinutes = 0;
            if ($firstEntry && $lastExit) {
                $workedMinutes = $firstEntry->occurred_at->diffInMinutes($lastExit->occurred_at);
            }
            $status = 'Incompleto';
            if ($firstEntry && $lastExit) {
                $status = 'Completado';
            } elseif ($entries->count() > 0 && $exits->count() === 0) {
                $status = 'Sin salida';
            }

            $phase = $apprentice->apprenticeProfile?->phase;
            $technologist = $apprentice->apprenticeProfile?->technologist;
            
            $totalMinutes = $apprentice->getTotalWorkedMinutes($phase?->id);
            $expectedMinutes = $apprentice->getExpectedWorkedMinutes();

            $rows[] = [
                'apprentice' => $apprentice->full_name,
                'phase' => $phase ? $phase->name : 'N/A',
                'technologist' => $technologist ? $technologist->name : 'N/A',
                'cohort' => $apprentice->apprenticeProfile->cohort ?? 'N/A',
                'entry_date' => $firstEntry ? $firstEntry->occurred_at->format('Y-m-d H:i') : '--',
                'exit_date' => $lastExit ? $lastExit->occurred_at->format('Y-m-d H:i') : '--',
                'hours_worked' => $apprentice->formatMinutesToHours($workedMinutes),
                'total_hours' => $apprentice->formatMinutesToHours($totalMinutes),
                'expected_hours' => $apprentice->formatMinutesToHours($expectedMinutes),
                'status' => $status,
            ];
        }

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.detailed_attendance_pdf', ['rows' => $rows, 'generated_at' => now()]);
            $pdf->setPaper('letter', 'landscape');
            return $pdf->download('reporte_asistencia_' . now()->format('Y-m-d_His') . '.pdf');
        }

        $filename = 'reporte_asistencia_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $output = fopen('php://temp', 'r+');
        fputcsv($output, ['Aprendiz', 'Fase', 'Tecnologo', 'Ficha', 'Fecha Entrada', 'Fecha Salida', 'Horas', 'Meta Fase']);
        foreach ($rows as $r) {
            fputcsv($output, [
                $r['apprentice'], $r['phase'], $r['technologist'], $r['cohort'], 
                $r['entry_date'], $r['exit_date'], $r['total_hours'], $r['expected_hours']
            ]);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return Response::make($csv, 200, $headers);
    }

    private function exportLogs(Request $request, string $format)
    {
        $query = AttendanceLog::with(['apprentice', 'createdBy']);
        if ($request->filled('apprentice_id')) {
            $query->where('apprentice_id', $request->apprentice_id);
        }
        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('occurred_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('occurred_at', '<=', $request->date_to);
        }
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }
        $logs = $query->orderBy('occurred_at', 'desc')->get();

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.attendance_pdf', [
                'rows' => $logs->map(function ($l) {
                    return [
                        'apprentice' => $l->apprentice->full_name ?? '',
                        'cohort' => $l->apprentice->apprenticeProfile->cohort ?? '',
                        'date' => $l->occurred_at->format('Y-m-d'),
                        'first_entry' => $l->event_type === 'entrada' ? $l->occurred_at->format('H:i:s') : '',
                        'last_exit' => $l->event_type === 'salida' ? $l->occurred_at->format('H:i:s') : '',
                        'entries' => $l->event_type === 'entrada' ? 1 : 0,
                        'exits' => $l->event_type === 'salida' ? 1 : 0,
                        'hours_worked' => '',
                        'status' => strtoupper($l->event_type),
                    ];
                }),
                'generated_at' => now()
            ]);
            $pdf->setPaper('letter', 'landscape');
            return $pdf->download('logs_asistencia_' . now()->format('Y-m-d_His') . '.pdf');
        }

        $filename = 'logs_asistencia_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $output = fopen('php://temp', 'r+');
        fputcsv($output, ['Aprendiz', 'Ficha', 'Fecha', 'Hora', 'Evento', 'Origen', 'Nota']);
        foreach ($logs as $l) {
            fputcsv($output, [
                $l->apprentice->full_name ?? '',
                $l->apprentice->apprenticeProfile->cohort ?? '',
                $l->occurred_at->format('Y-m-d'),
                $l->occurred_at->format('H:i:s'),
                strtoupper($l->event_type),
                $l->source,
                $l->note ?: ''
            ]);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return Response::make($csv, 200, $headers);
    }

    public function exportSessionsByPhase()
    {
        // Obtener todas las fases con sus tecnólogos, horarios y aprendices
        $phases = \App\Models\Phase::with([
            'technologists.schedules',
            'technologists.apprentices',
        ])->get();

        $phaseData = [];

        foreach ($phases as $phase) {
            $techRows = [];
            $phaseTotalExpectedMinutes = 0;

            foreach ($phase->technologists as $tech) {
                // Horas a cumplir por aprendiz (usando el mismo cálculo que User::getExpectedWorkedMinutes)
                $schedules = $tech->schedules->where('status', 'activo');
                $weeklyMinutes = [];
                foreach ($schedules as $schedule) {
                    $start = \Carbon\Carbon::parse($schedule->start_time);
                    $end   = \Carbon\Carbon::parse($schedule->end_time);
                    $duration = $start->diffInMinutes($end);
                    $weeklyMinutes[$schedule->weekday] = ($weeklyMinutes[$schedule->weekday] ?? 0) + $duration;
                }

                $startDate = $tech->start_date ?? $phase->start_date ?? null;
                $endDate   = $tech->end_date   ?? $phase->end_date   ?? null;

                $expectedMinsPerApprentice = 0;
                if ($startDate && $endDate) {
                    $cur = \Carbon\Carbon::parse($startDate);
                    $end = \Carbon\Carbon::parse($endDate);
                    while ($cur->lte($end)) {
                        $wd = $cur->dayOfWeekIso;
                        $expectedMinsPerApprentice += $weeklyMinutes[$wd] ?? 0;
                        $cur->addDay();
                    }
                }

                $apprenticeCount = $tech->apprentices->count();
                $techTotalMinutes = $expectedMinsPerApprentice * $apprenticeCount;
                $phaseTotalExpectedMinutes += $techTotalMinutes;

                $techRows[] = [
                    'name'                => $tech->name,
                    'apprentice_count'    => $apprenticeCount,
                    'expected_per_apprentice' => $expectedMinsPerApprentice,
                    'total_minutes'       => $techTotalMinutes,
                ];
            }

            $phaseData[] = [
                'phase'              => $phase,
                'techs'              => $techRows,
                'total_minutes'      => $phaseTotalExpectedMinutes,
            ];
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.sessions_phase_pdf', [
            'phaseData'    => $phaseData,
            'generated_at' => now(),
        ]);
        $pdf->setPaper('letter', 'portrait');

        return $pdf->download('reporte_fases_' . now()->format('Y-m-d_His') . '.pdf');
    }

    /**
     * PDF de Gráfica de Asistencia (Semanal o por Fase)
     */
    public function exportAttendanceChartPdf(Request $request)
    {
        $type      = $request->input('type', 'weekly');   // 'weekly' | 'phase'
        $phaseId   = $request->input('phase_id');

        $totalApprentices = \App\Models\User::whereHas('role', function ($q) {
            $q->where('name', 'Aprendiz');
        })->where('status', 'activo')->count();

        $days = [];

        if ($type === 'phase' && $phaseId) {
            // Rango completo de la fase
            $phase = \App\Models\Phase::find($phaseId);
            if (!$phase || !$phase->start_date || !$phase->end_date) {
                return back()->withErrors(['phase_id' => 'Fase inválida o sin fechas definidas.']);
            }
            $start = \Carbon\Carbon::parse($phase->start_date);
            $end   = \Carbon\Carbon::parse($phase->end_date)->min(\Carbon\Carbon::today());
            $title = 'Asistencia — Fase: ' . $phase->name;
        } else {
            // Últimos 7 días
            $start = \Carbon\Carbon::today()->subDays(6);
            $end   = \Carbon\Carbon::today();
            $phase = null;
            $title = 'Asistencia — Últimos 7 días';
        }

        $cur = $start->copy();
        while ($cur->lte($end)) {
            $present = \App\Models\AttendanceLog::where('event_type', 'entrada')
                ->whereDate('occurred_at', $cur)
                ->distinct('apprentice_id')
                ->count('apprentice_id');

            $rate   = $totalApprentices > 0 ? round(($present / $totalApprentices) * 100, 1) : 0;
            $absent = max(0, $totalApprentices - $present);

            $days[] = [
                'date'    => $cur->format('d/m'),
                'label'   => $cur->translatedFormat('D'),
                'present' => $present,
                'absent'  => $absent,
                'rate'    => $rate,
            ];

            $cur->addDay();
        }

        $phases = \App\Models\Phase::orderBy('is_active', 'desc')->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.attendance_chart_pdf', [
            'days'             => $days,
            'title'            => $title,
            'totalApprentices' => $totalApprentices,
            'type'             => $type,
            'phase'            => $phase,
            'generated_at'     => now(),
        ]);
        $pdf->setPaper('letter', 'landscape');

        return $pdf->download('reporte_grafica_asistencia_' . now()->format('Y-m-d_His') . '.pdf');
    }

    /**
     * PDF individual de asistencia por aprendiz en una fase
     */
    public function exportApprenticeAttendancePdf(Request $request)
    {
        $request->validate(['phase_id' => 'required|exists:phases,id']);

        $phase = \App\Models\Phase::findOrFail($request->phase_id);

        // Rango de fechas de la fase (hasta hoy como máximo)
        $startDate = $phase->start_date
            ? \Carbon\Carbon::parse($phase->start_date)
            : \Carbon\Carbon::now()->subMonth();
        $endDate = $phase->end_date
            ? \Carbon\Carbon::parse($phase->end_date)->min(\Carbon\Carbon::today())
            : \Carbon\Carbon::today();

        // Días laborables del período (lunes a viernes)
        $workdays = [];
        $cur = $startDate->copy();
        while ($cur->lte($endDate)) {
            if ($cur->isWeekday()) {
                $workdays[] = $cur->format('Y-m-d');
            }
            $cur->addDay();
        }

        // Todos los aprendices activos de esta fase
        $apprentices = \App\Models\User::whereHas('role', fn($q) => $q->where('name', 'Aprendiz'))
            ->where('status', 'activo')
            ->whereHas('apprenticeProfile', fn($q) => $q->where('phase_id', $phase->id))
            ->with('apprenticeProfile.technologist')
            ->orderBy('full_name')
            ->get();

        // Logs de asistencia del período agrupados por aprendiz
        $allLogs = \App\Models\AttendanceLog::where('event_type', 'entrada')
            ->whereIn('apprentice_id', $apprentices->pluck('id'))
            ->whereBetween('occurred_at', [$startDate->startOfDay(), $endDate->copy()->endOfDay()])
            ->get()
            ->groupBy(fn($l) => $l->apprentice_id . '_' . $l->occurred_at->format('Y-m-d'));

        $reportRows = $apprentices->map(function ($apprentice) use ($workdays, $allLogs) {
            $presentDays = [];
            $absentDays  = [];

            foreach ($workdays as $day) {
                $key = $apprentice->id . '_' . $day;
                if (isset($allLogs[$key]) && $allLogs[$key]->count() > 0) {
                    $presentDays[] = $day;
                } else {
                    $absentDays[] = $day;
                }
            }

            $total        = count($workdays);
            $attended     = count($presentDays);
            $missed       = count($absentDays);
            $attendedPct  = $total > 0 ? round(($attended / $total) * 100, 1) : 0;
            $missedPct    = $total > 0 ? round(($missed  / $total) * 100, 1) : 0;

            return [
                'apprentice'   => $apprentice,
                'technologist' => $apprentice->apprenticeProfile?->technologist?->name ?? 'N/A',
                'cohort'       => $apprentice->apprenticeProfile?->cohort ?? 'N/A',
                'total'        => $total,
                'attended'     => $attended,
                'missed'       => $missed,
                'attended_pct' => $attendedPct,
                'missed_pct'   => $missedPct,
                'absent_days'  => $absentDays,
            ];
        });

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.apprentice_attendance_pdf', [
            'phase'       => $phase,
            'workdays'    => $workdays,
            'reportRows'  => $reportRows,
            'startDate'   => $startDate,
            'endDate'     => $endDate,
            'generated_at'=> now(),
        ]);
        $pdf->setPaper('letter', 'landscape');

        return $pdf->download('reporte_aprendices_fase_' . $phase->id . '_' . now()->format('Y-m-d_His') . '.pdf');
    }
}
