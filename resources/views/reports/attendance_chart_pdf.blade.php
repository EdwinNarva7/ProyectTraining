<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Asistencia — Gráfica</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            background: #fff;
        }

        /* ════════════════════════════════════
           PAGE BREAKS
           ════════════════════════════════════ */
        .page {
            padding: 24px 28px;
            page-break-after: always;
        }
        .page:last-child {
            page-break-after: avoid;
        }

        /* ════════════════════════════════════
           SHARED HEADER / FOOTER
           ════════════════════════════════════ */
        .pdf-header {
            border-bottom: 3px solid #007a33;
            padding-bottom: 12px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .brand       { font-size: 19px; font-weight: 900; color: #007a33; letter-spacing: -0.5px; }
        .brand span  { color: #1e293b; }
        .brand-sub   { font-size: 8px; color: #64748b; margin-top: 2px; }
        .meta        { text-align: right; font-size: 8px; color: #64748b; line-height: 1.8; }
        .meta strong { color: #1e293b; font-size: 10px; }

        .pdf-footer {
            margin-top: 28px;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            font-size: 7.5px;
            color: #94a3b8;
            display: flex;
            justify-content: space-between;
        }

        /* ════════════════════════════════════
           PAGE 1 — PORTADA / RESUMEN
           ════════════════════════════════════ */
        .cover-title {
            font-size: 22px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }
        .cover-subtitle {
            font-size: 10px;
            color: #64748b;
            margin-bottom: 28px;
        }

        /* phase info box */
        .info-box {
            background: #f0fdf4;
            border: 1px solid #6ee7b7;
            border-left: 4px solid #007a33;
            border-radius: 8px;
            padding: 14px 18px;
            margin-bottom: 28px;
        }
        .info-box-title { font-size: 8px; font-weight: 900; text-transform: uppercase;
                          letter-spacing: 0.15em; color: #007a33; margin-bottom: 6px; }
        .info-box-value { font-size: 12px; font-weight: 700; color: #0f172a; }
        .info-box-sub   { font-size: 8.5px; color: #64748b; margin-top: 3px; }

        /* chips grid */
        .chips-grid { display: flex; gap: 14px; margin-bottom: 28px; }
        .chip {
            flex: 1;
            padding: 16px 12px;
            border-radius: 10px;
            text-align: center;
        }
        .chip-green  { background: #d1fae5; border: 1px solid #6ee7b7; }
        .chip-red    { background: #fee2e2; border: 1px solid #fca5a5; }
        .chip-blue   { background: #dbeafe; border: 1px solid #93c5fd; }
        .chip-amber  { background: #fef3c7; border: 1px solid #fcd34d; }
        .chip-slate  { background: #f1f5f9; border: 1px solid #cbd5e1; }
        .chip-label  { font-size: 7px; font-weight: 900; text-transform: uppercase;
                       letter-spacing: 0.12em; color: #475569; margin-bottom: 5px; }
        .chip-value  { font-size: 22px; font-weight: 900; color: #0f172a; line-height: 1; }
        .chip-sub    { font-size: 7px; color: #64748b; margin-top: 4px; }

        /* period summary table */
        .period-table { width: 100%; border-collapse: collapse; margin-top: 4px; }
        .period-table th {
            background: #007a33;
            color: #fff;
            padding: 7px 12px;
            font-size: 8px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            text-align: left;
        }
        .period-table td {
            padding: 8px 12px;
            font-size: 9.5px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }
        .period-table tr:nth-child(even) td { background: #f8fafc; }

        /* ════════════════════════════════════
           PAGE 2 — GRÁFICAS DE BARRAS
           ════════════════════════════════════ */
        .section-title {
            font-size: 11px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 6px;
            padding-bottom: 7px;
            border-bottom: 2px solid #e2e8f0;
        }
        .section-title span { color: #007a33; }

        .chart-section { margin-bottom: 32px; }

        /* horizontal bars */
        .bar-row       { display: flex; align-items: center; margin-bottom: 7px; gap: 8px; }
        .bar-label     { width: 32px; font-size: 7.5px; font-weight: 700; color: #475569; text-align: right; flex-shrink: 0; }
        .bar-date      { width: 28px; font-size: 7px; color: #94a3b8; flex-shrink: 0; }
        .bar-track     { flex: 1; background: #f1f5f9; border-radius: 4px; height: 20px;
                         position: relative; overflow: hidden; }
        .bar-fill      { height: 100%; border-radius: 4px; display: flex; align-items: center; padding-left: 7px; }
        .bar-fill-g    { background: #007a33; }
        .bar-fill-r    { background: #ef4444; }
        .bar-pct-in    { font-size: 7.5px; font-weight: 900; color: #fff; white-space: nowrap; }
        .bar-pct-out   { font-size: 7.5px; font-weight: 900; color: #475569; margin-left: 6px; flex-shrink: 0; }
        .bar-count     { width: 80px; text-align: right; font-size: 7px; color: #64748b; flex-shrink: 0; }

        .legend        { display: flex; gap: 16px; margin-top: 12px; font-size: 7.5px; color: #475569; }
        .legend-dot    { display: inline-block; width: 12px; height: 10px; border-radius: 2px;
                         vertical-align: middle; margin-right: 4px; }

        /* ════════════════════════════════════
           PAGE 3 — TABLA DE DATOS
           ════════════════════════════════════ */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table thead tr { background: #007a33; }
        .data-table th {
            padding: 8px 10px;
            text-align: left;
            font-size: 8px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #fff;
        }
        .data-table td {
            padding: 7px 10px;
            font-size: 9px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }
        .data-table tr:nth-child(even) td { background: #f8fafc; }

        .badge { display: inline-block; padding: 2px 8px; border-radius: 99px; font-size: 8px; font-weight: 900; }
        .badge-g { background: #d1fae5; color: #065f46; }
        .badge-r { background: #fee2e2; color: #991b1b; }
        .badge-o { background: #fef3c7; color: #92400e; }

        .high { color: #065f46; font-weight: 900; }
        .mid  { color: #d97706; font-weight: 900; }
        .low  { color: #dc2626; font-weight: 900; }

        .total-row td {
            background: #f0fdf4 !important;
            border-top: 2px solid #007a33;
            font-weight: 900;
            font-size: 9.5px;
            color: #065f46;
        }
    </style>
</head>
<body>

@php
    $totalPresent = collect($days)->sum('present');
    $totalAbsent  = collect($days)->sum('absent');
    $avgRate      = count($days) > 0 ? round(collect($days)->avg('rate'), 1) : 0;
    $maxRate      = count($days) > 0 ? collect($days)->max('rate') : 0;
    $minRate      = count($days) > 0 ? collect($days)->min('rate') : 0;
    $totalDays    = count($days);
    $optimalDays  = collect($days)->where('rate', '>=', 80)->count();
    $criticalDays = collect($days)->where('rate', '<', 50)->count();
@endphp

{{-- ════════════════════════════════════════════
     PÁGINA 1 — PORTADA / RESUMEN EJECUTIVO
     ════════════════════════════════════════════ --}}
<div class="page">

    <div class="pdf-header">
        <div>
            <div class="brand">SIEAP <span>— Sistema de Control de Asistencia</span></div>
            <div class="brand-sub">Servicio Nacional de Aprendizaje · SENA</div>
        </div>
        <div class="meta">
            <strong>{{ $title }}</strong><br>
            Generado: {{ $generated_at->format('d/m/Y H:i') }}<br>
            Pág. 1 de 3 — Resumen Ejecutivo
        </div>
    </div>

    <div class="cover-title">Reporte Gráfico de Asistencia</div>
    <p class="cover-subtitle">
        Análisis de porcentajes reales de presencia y ausencia del período seleccionado.
    </p>

    {{-- Info del período --}}
    <div class="info-box">
        <div class="info-box-title">Contexto del Período Analizado</div>
        @if($phase)
            <div class="info-box-value">Fase: {{ $phase->name }}</div>
            <div class="info-box-sub">
                Duración:
                {{ $phase->start_date ? \Carbon\Carbon::parse($phase->start_date)->format('d/m/Y') : '--' }}
                →
                {{ $phase->end_date   ? \Carbon\Carbon::parse($phase->end_date)->format('d/m/Y')   : '--' }}
                &nbsp;·&nbsp; Estado: {{ $phase->is_active ? 'Activa' : 'Inactiva' }}
            </div>
        @else
            <div class="info-box-value">Período: Últimos 7 días</div>
            <div class="info-box-sub">
                {{ $days[0]['date'] ?? '--' }} → {{ $days[array_key_last($days)]['date'] ?? '--' }}
                &nbsp;·&nbsp; Corte al {{ $generated_at->format('d/m/Y H:i') }}
            </div>
        @endif
        <div class="info-box-sub" style="margin-top:4px;">
            Total de aprendices activos considerados: <strong>{{ $totalApprentices }}</strong>
        </div>
    </div>

    {{-- KPI Chips --}}
    <div class="chips-grid">
        <div class="chip chip-slate">
            <div class="chip-label">Aprendices</div>
            <div class="chip-value">{{ $totalApprentices }}</div>
            <div class="chip-sub">activos registrados</div>
        </div>
        <div class="chip chip-blue">
            <div class="chip-label">Promedio</div>
            <div class="chip-value">{{ $avgRate }}%</div>
            <div class="chip-sub">asistencia del período</div>
        </div>
        <div class="chip chip-green">
            <div class="chip-label">Máx. Día</div>
            <div class="chip-value">{{ $maxRate }}%</div>
            <div class="chip-sub">mejor jornada</div>
        </div>
        <div class="chip chip-red">
            <div class="chip-label">Mín. Día</div>
            <div class="chip-value">{{ $minRate }}%</div>
            <div class="chip-sub">peor jornada</div>
        </div>
        <div class="chip chip-amber">
            <div class="chip-label">Días Óptimos</div>
            <div class="chip-value">{{ $optimalDays }}/{{ $totalDays }}</div>
            <div class="chip-sub">≥ 80% asistencia</div>
        </div>
    </div>

    {{-- Tabla resumen --}}
    <table class="period-table">
        <thead>
            <tr>
                <th>Indicador</th>
                <th>Valor</th>
                <th>Detalle</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Total días analizados</strong></td>
                <td>{{ $totalDays }}</td>
                <td>Jornadas con registro en el período</td>
            </tr>
            <tr>
                <td><strong>Total presencias registradas</strong></td>
                <td>{{ $totalPresent }}</td>
                <td>Suma de registros de entrada únicos por día</td>
            </tr>
            <tr>
                <td><strong>Total ausencias acumuladas</strong></td>
                <td>{{ $totalAbsent }}</td>
                <td>Aprendices sin registro de entrada ese día</td>
            </tr>
            <tr>
                <td><strong>% Asistencia promedio</strong></td>
                <td class="high">{{ $avgRate }}%</td>
                <td>( Σ presentes / total_aprendices ) / total_días × 100</td>
            </tr>
            <tr>
                <td><strong>Días con asistencia óptima (≥ 80%)</strong></td>
                <td class="high">{{ $optimalDays }}</td>
                <td>{{ $totalDays > 0 ? round(($optimalDays / $totalDays) * 100, 1) : 0 }}% del período</td>
            </tr>
            <tr>
                <td><strong>Días críticos (&lt; 50%)</strong></td>
                <td class="{{ $criticalDays > 0 ? 'low' : 'high' }}">{{ $criticalDays }}</td>
                <td>{{ $totalDays > 0 ? round(($criticalDays / $totalDays) * 100, 1) : 0 }}% del período</td>
            </tr>
        </tbody>
    </table>

    <div class="pdf-footer">
        <span>SIEAP · Servicio Nacional de Aprendizaje</span>
        <span>Pág. 1 de 3 — Resumen Ejecutivo · {{ $generated_at->format('d/m/Y H:i:s') }}</span>
    </div>
</div>


{{-- ════════════════════════════════════════════
     PÁGINA 2 — GRÁFICAS DE BARRAS
     ════════════════════════════════════════════ --}}
<div class="page">

    <div class="pdf-header">
        <div>
            <div class="brand">SIEAP <span>— Sistema de Control de Asistencia</span></div>
            <div class="brand-sub">Servicio Nacional de Aprendizaje · SENA</div>
        </div>
        <div class="meta">
            <strong>{{ $title }}</strong><br>
            Generado: {{ $generated_at->format('d/m/Y H:i') }}<br>
            Pág. 2 de 3 — Gráficas de Barras
        </div>
    </div>

    {{-- Gráfica 1: % Asistencia (Presentes) --}}
    <div class="chart-section">
        <div class="section-title">Gráfica 1 · <span>Porcentaje de Asistencia</span> por Día (Presentes)</div>
        @foreach($days as $d)
            @php $fw = $d['rate']; @endphp
            <div class="bar-row">
                <div class="bar-label">{{ $d['label'] }}</div>
                <div class="bar-date">{{ $d['date'] }}</div>
                <div class="bar-track">
                    @if($fw > 0)
                    <div class="bar-fill bar-fill-g" style="width:{{ $fw }}%">
                        @if($fw >= 15)<span class="bar-pct-in">{{ $fw }}%</span>@endif
                    </div>
                    @endif
                </div>
                @if($fw < 15)<span class="bar-pct-out">{{ $fw }}%</span>@else<span style="width:32px"></span>@endif
                <div class="bar-count">{{ $d['present'] }} / {{ $totalApprentices }} presentes</div>
            </div>
        @endforeach
        <div class="legend">
            <span><span class="legend-dot" style="background:#007a33"></span>Presentes (%)</span>
            <span><span class="legend-dot" style="background:#f1f5f9; border:1px solid #e2e8f0;"></span>Espacio = ausentes</span>
        </div>
    </div>

    {{-- Gráfica 2: % Ausencia --}}
    <div class="chart-section">
        <div class="section-title">Gráfica 2 · <span style="color:#dc2626">Porcentaje de Ausencia</span> por Día</div>
        @foreach($days as $d)
            @php $fw = round(100 - $d['rate'], 1); @endphp
            <div class="bar-row">
                <div class="bar-label">{{ $d['label'] }}</div>
                <div class="bar-date">{{ $d['date'] }}</div>
                <div class="bar-track">
                    @if($fw > 0)
                    <div class="bar-fill bar-fill-r" style="width:{{ $fw }}%">
                        @if($fw >= 15)<span class="bar-pct-in">{{ $fw }}%</span>@endif
                    </div>
                    @endif
                </div>
                @if($fw < 15)<span class="bar-pct-out">{{ $fw }}%</span>@else<span style="width:32px"></span>@endif
                <div class="bar-count">{{ $d['absent'] }} / {{ $totalApprentices }} ausentes</div>
            </div>
        @endforeach
        <div class="legend">
            <span><span class="legend-dot" style="background:#ef4444"></span>Ausentes (%)</span>
            <span><span class="legend-dot" style="background:#f1f5f9; border:1px solid #e2e8f0;"></span>Espacio = presentes</span>
        </div>
    </div>

    <div class="pdf-footer">
        <span>SIEAP · Servicio Nacional de Aprendizaje</span>
        <span>Pág. 2 de 3 — Gráficas de Barras · {{ $generated_at->format('d/m/Y H:i:s') }}</span>
    </div>
</div>


{{-- ════════════════════════════════════════════
     PÁGINA 3 — TABLA DE DATOS DETALLADA
     ════════════════════════════════════════════ --}}
<div class="page">

    <div class="pdf-header">
        <div>
            <div class="brand">SIEAP <span>— Sistema de Control de Asistencia</span></div>
            <div class="brand-sub">Servicio Nacional de Aprendizaje · SENA</div>
        </div>
        <div class="meta">
            <strong>{{ $title }}</strong><br>
            Generado: {{ $generated_at->format('d/m/Y H:i') }}<br>
            Pág. 3 de 3 — Tabla de Datos Detallada
        </div>
    </div>

    <div class="section-title" style="margin-bottom:14px;">
        Gráfica 3 · <span>Tabla de Datos</span> — Registros Completos del Período
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Día</th>
                <th>Fecha</th>
                <th>Presentes</th>
                <th>Ausentes</th>
                <th>Total</th>
                <th>% Asistencia</th>
                <th>% Ausencia</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($days as $i => $d)
            @php
                $pctClass = $d['rate'] >= 80 ? 'high' : ($d['rate'] >= 50 ? 'mid' : 'low');
                $estado   = $d['rate'] >= 80 ? 'Óptimo' : ($d['rate'] >= 50 ? 'Regular' : 'Crítico');
                $badge    = $d['rate'] >= 80 ? 'badge-g' : ($d['rate'] >= 50 ? 'badge-o' : 'badge-r');
            @endphp
            <tr>
                <td style="color:#94a3b8; font-size:8px;">{{ $i + 1 }}</td>
                <td><strong>{{ $d['label'] }}</strong></td>
                <td>{{ $d['date'] }}</td>
                <td><span class="badge badge-g">{{ $d['present'] }}</span></td>
                <td><span class="badge badge-r">{{ $d['absent'] }}</span></td>
                <td>{{ $totalApprentices }}</td>
                <td class="{{ $pctClass }}">{{ $d['rate'] }}%</td>
                <td class="low">{{ round(100 - $d['rate'], 1) }}%</td>
                <td><span class="badge {{ $badge }}">{{ $estado }}</span></td>
            </tr>
            @endforeach

            {{-- Totals row --}}
            <tr class="total-row">
                <td colspan="3">PROMEDIO DEL PERÍODO</td>
                <td>{{ $totalDays > 0 ? round($totalPresent / $totalDays, 1) : 0 }}/día</td>
                <td>{{ $totalDays > 0 ? round($totalAbsent  / $totalDays, 1) : 0 }}/día</td>
                <td>{{ $totalApprentices }}</td>
                <td>{{ $avgRate }}%</td>
                <td style="color:#dc2626;">{{ round(100 - $avgRate, 1) }}%</td>
                <td>—</td>
            </tr>
        </tbody>
    </table>

    <div class="pdf-footer">
        <span>SIEAP · Servicio Nacional de Aprendizaje</span>
        <span>Pág. 3 de 3 — Tabla de Datos · {{ $generated_at->format('d/m/Y H:i:s') }}</span>
    </div>
</div>

</body>
</html>
