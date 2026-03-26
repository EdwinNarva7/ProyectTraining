<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Control de Asistencia por Aprendiz — Fase</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 9px;
            color: #1e293b;
            background: #fff;
        }

        /* ── Pages ── */
        .page { padding: 20px 24px; page-break-after: always; }
        .page:last-child { page-break-after: avoid; }

        /* ── Header ── */
        .pdf-header {
            border-bottom: 3px solid #007a33;
            padding-bottom: 10px;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .brand      { font-size: 17px; font-weight: 900; color: #007a33; letter-spacing: -0.5px; }
        .brand span { color: #1e293b; }
        .brand-sub  { font-size: 7.5px; color: #64748b; margin-top: 2px; }
        .meta       { text-align: right; font-size: 7.5px; color: #64748b; line-height: 1.8; }
        .meta strong { color: #1e293b; font-size: 9.5px; }

        /* ── Footer ── */
        .pdf-footer {
            margin-top: 20px;
            border-top: 1px solid #e2e8f0;
            padding-top: 7px;
            font-size: 7px;
            color: #94a3b8;
            display: flex;
            justify-content: space-between;
        }

        /* ── Section title ── */
        .sec-title {
            font-size: 10px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 6px;
            margin-bottom: 14px;
        }
        .sec-title span { color: #007a33; }

        /* ════════════  PAGE 1 — RESUMEN DE FASE  ════════════ */
        .cover-title   { font-size: 20px; font-weight: 900; color: #0f172a; margin-bottom: 3px; }
        .cover-sub     { font-size: 9px; color: #64748b; margin-bottom: 22px; }

        .info-box {
            background: #f0fdf4;
            border: 1px solid #6ee7b7;
            border-left: 4px solid #007a33;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 22px;
        }
        .info-row   { display: flex; gap: 8px; margin-top: 4px; font-size: 9px; }
        .info-label { font-weight: 900; color: #475569; width: 140px; flex-shrink: 0; }
        .info-value { color: #0f172a; }

        /* KPI chips */
        .chips { display: flex; gap: 12px; margin-bottom: 22px; }
        .chip  { flex: 1; padding: 14px 10px; border-radius: 10px; text-align: center; }
        .chip-g   { background: #d1fae5; border: 1px solid #6ee7b7; }
        .chip-r   { background: #fee2e2; border: 1px solid #fca5a5; }
        .chip-b   { background: #dbeafe; border: 1px solid #93c5fd; }
        .chip-s   { background: #f1f5f9; border: 1px solid #cbd5e1; }
        .chip-a   { background: #fef3c7; border: 1px solid #fcd34d; }
        .chip-lbl { font-size: 7px; font-weight: 900; text-transform: uppercase;
                    letter-spacing: 0.12em; color: #475569; margin-bottom: 5px; }
        .chip-val { font-size: 20px; font-weight: 900; color: #0f172a; line-height: 1; }
        .chip-sub { font-size: 6.5px; color: #64748b; margin-top: 3px; }

        /* ranking table on page 1 */
        .rank-table { width: 100%; border-collapse: collapse; }
        .rank-table thead tr { background: #007a33; }
        .rank-table th {
            padding: 7px 9px;
            text-align: left;
            font-size: 7.5px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #fff;
        }
        .rank-table td {
            padding: 6px 9px;
            font-size: 8.5px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
        }
        .rank-table tr:nth-child(even) td { background: #f8fafc; }

        /* mini bar inside table */
        .mini-track {
            width: 100%;
            height: 10px;
            background: #f1f5f9;
            border-radius: 3px;
            overflow: hidden;
        }
        .mini-fill-g { height: 100%; background: #007a33; border-radius: 3px; }
        .mini-fill-r { height: 100%; background: #ef4444; border-radius: 3px; }

        .badge { display: inline-block; padding: 1px 7px; border-radius: 99px;
                 font-size: 7px; font-weight: 900; }
        .badge-g { background: #d1fae5; color: #065f46; }
        .badge-r { background: #fee2e2; color: #991b1b; }
        .badge-a { background: #fef3c7; color: #92400e; }

        .hi  { color: #065f46; font-weight: 900; }
        .mid { color: #d97706; font-weight: 900; }
        .lo  { color: #dc2626; font-weight: 900; }

        /* ════════════  PAGE 2+ — DETALLE POR APRENDIZ  ════════════ */
        .apprentice-card {
            margin-bottom: 18px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
        }
        .card-header {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 8px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card-name   { font-size: 10px; font-weight: 900; color: #0f172a; }
        .card-meta   { font-size: 7.5px; color: #64748b; }
        .card-stats  { display: flex; gap: 10px; }
        .stat-pill   { padding: 3px 10px; border-radius: 99px; font-size: 7.5px; font-weight: 900; }
        .stat-g      { background: #d1fae5; color: #065f46; }
        .stat-r      { background: #fee2e2; color: #991b1b; }
        .stat-b      { background: #dbeafe; color: #1d4ed8; }

        .card-body { padding: 10px 12px; }

        /* bar inside card */
        .att-bar-wrap { margin-bottom: 8px; }
        .att-bar-label { font-size: 7px; font-weight: 700; color: #475569;
                         margin-bottom: 3px; display: flex; justify-content: space-between; }
        .att-track  { width: 100%; height: 14px; background: #f1f5f9; border-radius: 3px; overflow: hidden; }
        .att-fill-g { height: 100%; background: #007a33; border-radius: 3px; }
        .att-fill-r { height: 100%; background: #ef4444; border-radius: 3px; }

        /* absent days list */
        .absent-title { font-size: 7.5px; font-weight: 900; color: #dc2626;
                        text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 4px; }
        .absent-pills { display: flex; flex-wrap: wrap; gap: 4px; }
        .day-pill     { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5;
                        border-radius: 4px; padding: 2px 6px; font-size: 7px; font-weight: 700; }
        .no-absent    { font-size: 7.5px; color: #22c55e; font-weight: 700; font-style: italic; }
    </style>
</head>
<body>

@php
    $totalApprentices = $reportRows->count();
    $avgAttended = $totalApprentices > 0 ? round($reportRows->avg('attended_pct'), 1) : 0;
    $perfect     = $reportRows->where('missed', 0)->count();
    $critical    = $reportRows->where('attended_pct', '<', 50)->count();
    $totalDays   = count($workdays);
@endphp


{{-- ══════════════════════════════════════════════════════════
     PÁGINA 1 — RESUMEN GENERAL DE LA FASE
     ══════════════════════════════════════════════════════════ --}}
<div class="page">

    <div class="pdf-header">
        <div>
            <div class="brand">SIEAP <span>— Sistema de Control de Asistencia</span></div>
            <div class="brand-sub">Servicio Nacional de Aprendizaje · SENA</div>
        </div>
        <div class="meta">
            <strong>Control de Asistencia por Aprendiz</strong><br>
            Generado: {{ $generated_at->format('d/m/Y H:i') }}<br>
            Pág. 1 — Resumen de Fase
        </div>
    </div>

    <div class="cover-title">Asistencia por Aprendiz — Fase: {{ $phase->name }}</div>
    <p class="cover-sub">Desglose individual de presencias, ausencias y días específicos de inasistencia.</p>

    {{-- Info box --}}
    <div class="info-box">
        <div class="info-row"><div class="info-label">Fase:</div><div class="info-value"><strong>{{ $phase->name }}</strong> &nbsp;·&nbsp; {{ $phase->is_active ? '✓ Activa' : 'Inactiva' }}</div></div>
        <div class="info-row">
            <div class="info-label">Período analizado:</div>
            <div class="info-value">
                {{ $startDate->format('d/m/Y') }} → {{ $endDate->format('d/m/Y') }}
                &nbsp;({{ $totalDays }} días laborables)
            </div>
        </div>
        <div class="info-row"><div class="info-label">Total aprendices:</div><div class="info-value">{{ $totalApprentices }}</div></div>
    </div>

    {{-- KPI chips --}}
    <div class="chips">
        <div class="chip chip-s">
            <div class="chip-lbl">Aprendices</div>
            <div class="chip-val">{{ $totalApprentices }}</div>
            <div class="chip-sub">en la fase</div>
        </div>
        <div class="chip chip-s">
            <div class="chip-lbl">Días laborables</div>
            <div class="chip-val">{{ $totalDays }}</div>
            <div class="chip-sub">del período</div>
        </div>
        <div class="chip chip-b">
            <div class="chip-lbl">% Asistencia Prom.</div>
            <div class="chip-val">{{ $avgAttended }}%</div>
            <div class="chip-sub">promedio del grupo</div>
        </div>
        <div class="chip chip-g">
            <div class="chip-lbl">Asistencia Perfecta</div>
            <div class="chip-val">{{ $perfect }}</div>
            <div class="chip-sub">sin ninguna falta</div>
        </div>
        <div class="chip chip-r">
            <div class="chip-lbl">Caso Crítico</div>
            <div class="chip-val">{{ $critical }}</div>
            <div class="chip-sub">menos del 50%</div>
        </div>
    </div>

    {{-- Ranking table --}}
    <div class="sec-title">Ranking de Asistencia — <span>Todos los Aprendices</span></div>
    <table class="rank-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Aprendiz</th>
                <th>Tecnólogo</th>
                <th>Ficha</th>
                <th>Días Asistidos</th>
                <th>Días Ausente</th>
                <th>% Asistencia</th>
                <th>% Ausencia</th>
                <th>Barra Asistencia</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportRows->sortByDesc('attended_pct')->values() as $i => $row)
            @php
                $cls   = $row['attended_pct'] >= 80 ? 'hi' : ($row['attended_pct'] >= 50 ? 'mid' : 'lo');
                $badge = $row['attended_pct'] >= 80 ? 'badge-g' : ($row['attended_pct'] >= 50 ? 'badge-a' : 'badge-r');
                $label = $row['attended_pct'] >= 80 ? 'Óptimo' : ($row['attended_pct'] >= 50 ? 'Regular' : 'Crítico');
            @endphp
            <tr>
                <td style="color:#94a3b8; font-size:7.5px;">{{ $i + 1 }}</td>
                <td><strong>{{ $row['apprentice']->full_name }}</strong></td>
                <td>{{ $row['technologist'] }}</td>
                <td>{{ $row['cohort'] }}</td>
                <td class="hi">{{ $row['attended'] }} / {{ $row['total'] }}</td>
                <td class="{{ $row['missed'] > 0 ? 'lo' : 'hi' }}">{{ $row['missed'] }}</td>
                <td class="{{ $cls }}">{{ $row['attended_pct'] }}%</td>
                <td class="lo">{{ $row['missed_pct'] }}%</td>
                <td style="width:90px;">
                    <div class="mini-track">
                        <div class="mini-fill-g" style="width:{{ $row['attended_pct'] }}%"></div>
                    </div>
                </td>
                <td><span class="badge {{ $badge }}">{{ $label }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="pdf-footer">
        <span>SIEAP · Servicio Nacional de Aprendizaje</span>
        <span>Pág. 1 — Resumen de Fase · {{ $generated_at->format('d/m/Y H:i:s') }}</span>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════
     PÁGINA 2+ — DETALLE POR APRENDIZ (máx 4 por página)
     ══════════════════════════════════════════════════════════ --}}
@php $chunks = $reportRows->sortBy(fn($r) => $r['apprentice']->full_name)->chunk(4); @endphp

@foreach($chunks as $pageIndex => $chunk)
<div class="page">

    <div class="pdf-header">
        <div>
            <div class="brand">SIEAP <span>— Sistema de Control de Asistencia</span></div>
            <div class="brand-sub">Servicio Nacional de Aprendizaje · SENA</div>
        </div>
        <div class="meta">
            <strong>Detalle Individual — Fase: {{ $phase->name }}</strong><br>
            Generado: {{ $generated_at->format('d/m/Y H:i') }}<br>
            Pág. {{ $pageIndex + 2 }} — Detalle Aprendices {{ $pageIndex * 4 + 1 }}–{{ min(($pageIndex + 1) * 4, $totalApprentices) }}
        </div>
    </div>

    <div class="sec-title">
        Detalle de Asistencia <span>por Aprendiz</span> &nbsp;·&nbsp;
        <span style="color:#64748b; font-weight:400; text-transform:none; font-size:9px;">
            {{ $startDate->format('d/m/Y') }} → {{ $endDate->format('d/m/Y') }}
        </span>
    </div>

    @foreach($chunk as $row)
    @php
        $cls   = $row['attended_pct'] >= 80 ? 'hi' : ($row['attended_pct'] >= 50 ? 'mid' : 'lo');
        $label = $row['attended_pct'] >= 80 ? 'Óptimo' : ($row['attended_pct'] >= 50 ? 'Regular' : 'Crítico');
        $badge = $row['attended_pct'] >= 80 ? 'badge-g' : ($row['attended_pct'] >= 50 ? 'badge-a' : 'badge-r');
    @endphp
    <div class="apprentice-card">
        <div class="card-header">
            <div>
                <div class="card-name">{{ $row['apprentice']->full_name }}</div>
                <div class="card-meta">
                    Tecnólogo: {{ $row['technologist'] }} &nbsp;·&nbsp;
                    Ficha: {{ $row['cohort'] }} &nbsp;·&nbsp;
                    {{ $row['total'] }} días laborables en el período
                </div>
            </div>
            <div class="card-stats">
                <span class="stat-pill stat-g">✓ {{ $row['attended'] }} días asistidos</span>
                <span class="stat-pill stat-r">✗ {{ $row['missed'] }} días ausente</span>
                <span class="stat-pill stat-b">{{ $row['attended_pct'] }}% asistencia</span>
                <span class="badge {{ $badge }}" style="padding: 4px 10px;">{{ $label }}</span>
            </div>
        </div>

        <div class="card-body">
            {{-- Barra asistencia --}}
            <div class="att-bar-wrap">
                <div class="att-bar-label">
                    <span>Asistencia: {{ $row['attended_pct'] }}%</span>
                    <span style="color:#dc2626;">Ausencia: {{ $row['missed_pct'] }}%</span>
                </div>
                <div class="att-track">
                    <div class="att-fill-g" style="width:{{ $row['attended_pct'] }}%"></div>
                </div>
            </div>

            {{-- Días ausentes --}}
            @if(count($row['absent_days']) > 0)
                <div class="absent-title">Días de Inasistencia ({{ count($row['absent_days']) }}):</div>
                <div class="absent-pills">
                    @foreach($row['absent_days'] as $day)
                        <span class="day-pill">
                            {{ \Carbon\Carbon::parse($day)->translatedFormat('D d/m') }}
                        </span>
                    @endforeach
                </div>
            @else
                <div class="no-absent">✓ Sin ausencias — Asistencia perfecta</div>
            @endif
        </div>
    </div>
    @endforeach

    <div class="pdf-footer">
        <span>SIEAP · Servicio Nacional de Aprendizaje</span>
        <span>Pág. {{ $pageIndex + 2 }} — Detalle Individual · {{ $generated_at->format('d/m/Y H:i:s') }}</span>
    </div>
</div>
@endforeach

</body>
</html>
