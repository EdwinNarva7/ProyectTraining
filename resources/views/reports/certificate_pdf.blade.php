<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Certificado de Asistencia - {{ $certificate->apprentice->full_name }}</title>
    <style>
        @page {
            margin: 0;
            size: letter landscape;
        }

        body {
            font-family: 'Helvetica', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        .container {
            position: relative;
            width: 100%;
            height: 100%;
            border: 20px solid #39A900;
            box-sizing: border-box;
            padding: 50px;
        }

        .inner-border {
            border: 2px solid #2d8500;
            height: 100%;
            padding: 40px;
            text-align: center;
            box-sizing: border-box;
        }

        .header {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            width: 90px;
        }

        .title {
            font-size: 44px;
            color: #2d8500;
            font-weight: 800;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .subtitle {
            font-size: 18px;
            color: #666;
            margin-bottom: 16px;
        }

        .content {
            font-size: 18px;
            line-height: 1.6;
            color: #333;
            margin-bottom: 32px;
        }

        .apprentice-name {
            font-size: 32px;
            font-weight: 800;
            color: #111;
            margin: 14px 0;
        }

        .details {
            margin-top: 18px;
            font-size: 16px;
            color: #4b5563;
        }

        .footer {
            margin-top: 48px;
            position: relative;
        }

        .signatures {
            display: flex;
            justify-content: space-around;
            gap: 80px;
            margin-top: 24px;
        }

        .signature-block {
            text-align: center;
        }

        .signature-line {
            width: 280px;
            border-top: 2px solid #333;
            margin: 0 auto 6px auto;
        }

        .signature-text {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
        }

        .issuer {
            font-size: 13px;
            color: #6b7280;
            margin-top: 6px;
        }

        .certificate-id {
            position: absolute;
            bottom: 20px;
            right: 40px;
            font-size: 10px;
            color: #aaa;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.05;
            font-size: 120px;
            font-weight: bold;
            color: #39A900;
            z-index: -1;
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 9999px;
            font-size: 12px;
            color: #374151;
        }

        .meta {
            margin-top: 10px;
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="inner-border">
            <div class="watermark">SENA SIAP</div>

            <div class="header">
                <div>
                    <div class="title">Certificado Empresarial</div>
                    <div class="subtitle">Centro de Formación y Desarrollo Profesional • SIAP SENA</div>
                    <div class="badge">Documento oficial de cumplimiento de horas</div>
                </div>
                <div>
                    <!-- Espacio reservado para logotipo institucional -->
                    <div class="logo"></div>
                </div>
                <div class="subtitle">Sistema de Información de Asistencia y Participación (SIAP)</div>
            </div>

            <div class="content">
                Este documento certifica que el aprendiz
                <div class="apprentice-name">{{ $certificate->apprentice->full_name }}</div>
                ha completado satisfactoriamente un total de
                <br>
                <span
                    style="font-size: 24px; font-weight: bold; color: #39A900;">{{ number_format($certificate->hours_completed, 1) }}
                    Horas Académicas</span>
                <br>
                <div class="meta">
                    Expedición: {{ $certificate->issued_at->format('d/m/Y') }} • Centro: SENA - SIAP • Código Interno:
                    {{ str_pad($certificate->id, 6, '0', STR_PAD_LEFT) }}
                </div>
                registradas en la plataforma durante su proceso de formación.
            </div>

                Expedido el día {{ $certificate->issued_at->format('d') }} de
                {{ $certificate->issued_at->translatedFormat('F') }} de {{ $certificate->issued_at->format('Y') }}.
                Este certificado se emite para fines empresariales, institucionales y de validación académica del centro.
                {{ $certificate->issued_at->translatedFormat('F') }} de {{ $certificate->issued_at->format('Y') }}
            </div>

                <div class="signatures">
                    <div class="signature-block">
                        <div class="signature-line"></div>
                        <div class="signature-text">Director del Centro</div>
                        <div class="issuer">Centro de Formación Empresarial - SENA</div>
                    </div>
                    <div class="signature-block">
                        <div class="signature-line"></div>
                        <div class="signature-text">Coordinación Académica</div>
                        <div class="issuer">SIAP - Servicio Nacional de Aprendizaje (SENA)</div>
                    </div>
                </div>
                <div class="issuer">SIAP - Servicio Nacional de Aprendizaje (SENA)</div>
            </div>

            <div class="certificate-id">
                ID Certificado: {{ str_pad($certificate->id, 8, '0', STR_PAD_LEFT) }} | Código de Verificación:
                {{ strtoupper(substr(md5($certificate->id . 'SIAP'), 0, 10)) }}
            </div>
        </div>
    </div>
