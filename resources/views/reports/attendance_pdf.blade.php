<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Asistencia</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; }
        .header { margin-bottom: 12px; }
        .title { font-size: 18px; font-weight: 700; }
        .subtitle { font-size: 12px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; }
        th { background: #f5f7fa; text-transform: uppercase; font-size: 10px; color: #555; }
        tr:nth-child(even) { background: #fafafa; }
        .small { font-size: 10px; color: #777; }
    </style>
    </head>
<body>
    <div class="header">
        <div class="title">Reporte Detallado de Asistencia</div>
        <div class="subtitle">Generado: {{ $generated_at->format('d/m/Y H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Aprendiz</th>
                <th>Ficha</th>
                <th>Fecha</th>
                <th>Primera Entrada</th>
                <th>Última Salida</th>
                <th>Entradas</th>
                <th>Salidas</th>
                <th>Horas</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
        @foreach($rows as $r)
            <tr>
                <td>{{ $r['apprentice'] }}</td>
                <td>{{ $r['cohort'] }}</td>
                <td>{{ $r['date'] }}</td>
                <td>{{ $r['first_entry'] }}</td>
                <td>{{ $r['last_exit'] }}</td>
                <td>{{ $r['entries'] }}</td>
                <td>{{ $r['exits'] }}</td>
                <td>{{ $r['hours_worked'] }}</td>
                <td>{{ $r['status'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="small">SIAP - Servicio Nacional de Aprendizaje</div>
</body>
</html>
