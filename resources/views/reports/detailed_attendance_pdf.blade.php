<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Detallado de Asistencia</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            background: #fff;
            padding: 20px;
        }

        /* ── Header ── */
        .pdf-header {
            border-bottom: 3px solid #007a33;
            padding-bottom: 12px;
            margin-bottom: 18px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .brand { font-size: 19px; font-weight: 900; color: #007a33; letter-spacing: -0.5px; }
        .brand span { color: #1e293b; }
        .brand-sub { font-size: 8.5px; color: #64748b; margin-top: 2px; }
        .meta { text-align: right; font-size: 8.5px; color: #64748b; line-height: 1.7; }
        .meta strong { color: #1e293b; }

        h1 { font-size: 14px; font-weight: 900; color: #1e293b; margin-bottom: 3px; }
        .subtitle { font-size: 9px; color: #64748b; margin-bottom: 18px; }

        /* ── Table ── */
        table { width: 100%; border-collapse: collapse; }

        thead tr { background: #007a33; }
        th {
            padding: 7px 9px;
            text-align: left;
            font-size: 8px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #fff;
        }

        td {
            padding: 6px 9px;
            font-size: 9px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
        }
        tr:nth-child(even) td { background: #f8fafc; }
        tr:hover td { background: #f1fdf5; }

        .name-cell { font-weight: 700; color: #0f172a; font-size: 9.5px; }
        .dim { color: #94a3b8; font-size: 8.5px; }

        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 99px;
            font-size: 7.5px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .badge-green  { background: #d1fae5; color: #065f46; }
        .badge-slate  { background: #e2e8f0; color: #334155; }
        .badge-indigo { background: #e0e7ff; color: #3730a3; }

        .time-cell { font-family: DejaVu Sans Mono, monospace; font-size: 9px; }

        /* ── Footer ── */
        .pdf-footer {
            margin-top: 22px;
            border-top: 1px solid #e2e8f0;
            padding-top: 9px;
            font-size: 8px;
            color: #94a3b8;
            display: flex;
            justify-content: space-between;
        }

        .no-data { color: #94a3b8; font-style: italic; padding: 16px; text-align: center; }

        /* ── Summary row ── */
        .summary-row td {
            background: #f0fdf4;
            font-weight: 900;
            font-size: 9.5px;
            border-top: 2px solid #007a33;
            color: #065f46;
        }
    </style>
</head>
<body>

    {{-- Header ─────────────────────────────────────── --}}
    <div class="pdf-header">
        <div>
            <div class="brand">SIEAP <span>— Sistema de Control de Asistencia</span></div>
            <div class="brand-sub">Servicio Nacional de Aprendizaje · SENA</div>
        </div>
        <div class="meta">
            <strong>Reporte Detallado de Asistencia</strong><br>
            Generado: {{ $generated_at->format('d/m/Y H:i') }}<br>
            Total de registros: {{ count($rows) }}
        </div>
    </div>

    <h1>Control de Asistencia por Aprendiz</h1>
    <p class="subtitle">Incluye fase, tecnólogo, ficha, fechas de entrada/salida, horas laboradas y meta de la fase para cada colaborador.</p>

    @if(count($rows))
    <table>
        <thead>
            <tr>
                <th>Aprendiz</th>
                <th>Fase</th>
                <th>Tecnólogo</th>
                <th>Ficha</th>
                <th>Fecha Entrada</th>
                <th>Fecha Salida</th>
                <th>Horas</th>
                <th>Meta Fase</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $r)
            <tr>
                <td class="name-cell">{{ $r['apprentice'] }}</td>
                <td><span class="badge badge-green">{{ $r['phase'] }}</span></td>
                <td>{{ $r['technologist'] }}</td>
                <td><span class="badge badge-slate">{{ $r['cohort'] }}</span></td>
                <td class="time-cell">{{ $r['entry_date'] }}</td>
                <td class="time-cell">{{ $r['exit_date'] }}</td>
                <td><span class="badge badge-indigo">{{ $r['total_hours'] }}</span></td>
                <td><strong>{{ $r['expected_hours'] }}</strong></td>
            </tr>
            @endforeach

            {{-- Summary row --}}
            <tr class="summary-row">
                <td colspan="6">TOTAL DE REGISTROS: {{ count($rows) }}</td>
                <td colspan="2">Meta promedio: —</td>
            </tr>
        </tbody>
    </table>
    @else
        <p class="no-data">No se encontraron registros con los filtros aplicados.</p>
    @endif

    {{-- Footer ──────────────────────────────────────── --}}
    <div class="pdf-footer">
        <span>SIEAP · Servicio Nacional de Aprendizaje</span>
        <span>Documento generado automáticamente — {{ $generated_at->format('d/m/Y H:i:s') }}</span>
    </div>

</body>
</html>

