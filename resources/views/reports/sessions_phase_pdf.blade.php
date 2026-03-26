<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Horas por Fase</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            background: #fff;
            padding: 24px;
        }

        /* ── Header ── */
        .pdf-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #007a33;
            padding-bottom: 14px;
            margin-bottom: 20px;
        }
        .pdf-header .brand { font-size: 20px; font-weight: 900; color: #007a33; letter-spacing: -0.5px; }
        .pdf-header .brand span { color: #1e293b; }
        .pdf-header .meta { text-align: right; color: #64748b; font-size: 9px; line-height: 1.6; }
        .pdf-header .meta strong { color: #1e293b; }

        h1 {
            font-size: 16px;
            font-weight: 900;
            color: #1e293b;
            margin-bottom: 4px;
        }
        .subtitle {
            font-size: 10px;
            color: #64748b;
            margin-bottom: 24px;
        }

        /* ── Phase block ── */
        .phase-block { margin-bottom: 28px; }

        .phase-title {
            background: #007a33;
            color: #fff;
            font-weight: 900;
            font-size: 12px;
            padding: 7px 14px;
            border-radius: 4px 4px 0 0;
            letter-spacing: 0.3px;
        }
        .phase-title span {
            font-weight: 400;
            font-size: 9px;
            opacity: 0.85;
            margin-left: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead tr {
            background: #f1f5f9;
        }
        th {
            padding: 7px 12px;
            text-align: left;
            font-size: 8.5px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: #475569;
            border-bottom: 1px solid #e2e8f0;
        }
        td {
            padding: 8px 12px;
            font-size: 10px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
        }
        tr:nth-child(even) td { background: #f8fafc; }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 99px;
            font-size: 9px;
            font-weight: 700;
        }
        .badge-green { background: #dcfce7; color: #15803d; }
        .badge-blue  { background: #dbeafe; color: #1d4ed8; }

        /* ── Phase total row ── */
        .total-row td {
            background: #fff7ed;
            font-weight: 900;
            font-size: 10.5px;
            border-top: 2px solid #f97316;
            color: #c2410c;
        }
        .total-row td:first-child { color: #c2410c; letter-spacing: 0.05em; }

        /* ── Footer ── */
        .pdf-footer {
            margin-top: 30px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            font-size: 8.5px;
            color: #94a3b8;
            display: flex;
            justify-content: space-between;
        }

        .no-data { color: #94a3b8; font-style: italic; padding: 10px 14px; font-size: 10px; }
    </style>
</head>
<body>

    <div class="pdf-header">
        <div>
            <div class="brand">SIEAP <span>— Sistema de Control de Asistencia</span></div>
            <div style="font-size:9px; color:#64748b; margin-top:3px;">Servicio Nacional de Aprendizaje · SENA</div>
        </div>
        <div class="meta">
            <strong>Reporte de Horas por Fase</strong><br>
            Generado: {{ $generated_at->format('d/m/Y H:i') }}<br>
            Total de fases: {{ count($phaseData) }}
        </div>
    </div>

    <h1>Horas a Cumplir por Tecnólogo y Fase</h1>
    <p class="subtitle">
        Muestra las horas esperadas por tecnólogo, las horas que debe cumplir cada aprendiz del grupo,
        y el total consolidado de horas por fase.
    </p>

    @forelse($phaseData as $pd)
        @php
            $phase = $pd['phase'];
            $techs = $pd['techs'];
            $totalMins = $pd['total_minutes'];
            $totalH = intdiv($totalMins, 60);
            $totalM = $totalMins % 60;
        @endphp

        <div class="phase-block">
            <div class="phase-title">
                {{ strtoupper($phase->name) }}
                <span>
                    {{ $phase->start_date ? \Carbon\Carbon::parse($phase->start_date)->format('d/m/Y') : '--' }}
                    →
                    {{ $phase->end_date   ? \Carbon\Carbon::parse($phase->end_date)->format('d/m/Y')   : '--' }}
                    &nbsp;·&nbsp; {{ $phase->is_active ? 'ACTIVA' : 'INACTIVA' }}
                </span>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Tecnólogo / Programa</th>
                        <th>Aprendices</th>
                        <th>Horas a Cumplir / Aprendiz</th>
                        <th>Total Horas del Grupo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($techs as $t)
                        @php
                            $perH = intdiv($t['expected_per_apprentice'], 60);
                            $perM = $t['expected_per_apprentice'] % 60;
                            $grpH = intdiv($t['total_minutes'], 60);
                            $grpM = $t['total_minutes'] % 60;
                        @endphp
                        <tr>
                            <td><strong>{{ $t['name'] }}</strong></td>
                            <td><span class="badge badge-blue">{{ $t['apprentice_count'] }}</span></td>
                            <td>{{ $perH }}h {{ $perM }}m</td>
                            <td>{{ $grpH }}h {{ $grpM }}m</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="no-data">Sin tecnólogos registrados en esta fase.</td></tr>
                    @endforelse

                    {{-- Total Phase Row --}}
                    @if(count($techs))
                    <tr class="total-row">
                        <td colspan="3">TOTAL HORAS DE LA FASE</td>
                        <td>{{ $totalH }}h {{ $totalM }}m</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    @empty
        <p class="no-data">No hay fases registradas en el sistema.</p>
    @endforelse

    <div class="pdf-footer">
        <span>SIEAP · Servicio Nacional de Aprendizaje</span>
        <span>Documento generado automáticamente — {{ $generated_at->format('d/m/Y H:i:s') }}</span>
    </div>

</body>
</html>
