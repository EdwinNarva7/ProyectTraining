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

            $hoursWorked = 0;
            if ($firstEntry && $lastExit) {
                $hoursWorked = $firstEntry->occurred_at->diffInMinutes($lastExit->occurred_at) / 60;
            }
            $status = 'Incompleto';
            if ($firstEntry && $lastExit) {
                $status = 'Completado';
            } elseif ($entries->count() > 0 && $exits->count() === 0) {
                $status = 'Sin salida';
            }

            $rows[] = [
                'apprentice' => $apprentice->full_name,
                'cohort' => $apprentice->apprenticeProfile->cohort ?? '',
                'date' => $date,
                'first_entry' => $firstEntry ? $firstEntry->occurred_at->format('H:i:s') : '',
                'last_exit' => $lastExit ? $lastExit->occurred_at->format('H:i:s') : '',
                'entries' => $entries->count(),
                'exits' => $exits->count(),
                'hours_worked' => number_format($hoursWorked, 2),
                'status' => $status,
            ];
        }

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('reports.attendance_pdf', ['rows' => $rows, 'generated_at' => now()]);
            $pdf->setPaper('letter', 'landscape');
            return $pdf->download('reporte_asistencia_' . now()->format('Y-m-d_His') . '.pdf');
        }

        $filename = 'reporte_asistencia_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $output = fopen('php://temp', 'r+');
        fputcsv($output, ['Aprendiz', 'Ficha', 'Fecha', 'Primera Entrada', 'Última Salida', 'Entradas', 'Salidas', 'Horas', 'Estado']);
        foreach ($rows as $r) {
            fputcsv($output, [
                $r['apprentice'], $r['cohort'], $r['date'], $r['first_entry'], $r['last_exit'],
                $r['entries'], $r['exits'], $r['hours_worked'], $r['status']
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
}
