<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificado de Reconocimiento - {{ $equipo->nombre }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Great+Vibes&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
        @page {
            size: letter landscape;
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            font-family: 'Open Sans', sans-serif;
            width: 100%;
            height: 100%;
        }
        .certificate-container {
            width: 279.4mm;
            height: 215.9mm;
            position: relative;
            background-color: #fff;
            @if($isPdf ?? false)
            background-image: url('{{ public_path('certificate_bg-1.png') }}');
            @else
            background-image: url('/certificate_bg-1.png');
            @endif
            background-size: 100% 100%; /* Force stretch */
            background-repeat: no-repeat;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
            text-align: center;
            color: #1f2937;
        }

        /* PDF specific adjustments */
        @if($isPdf ?? false)
        .certificate-container {
            width: 100%;
            height: 100%;
            box-shadow: none;
        }
        @endif

        .content-wrapper {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            /* Use table for vertical centering in DomPDF if flex fails, but absolute + top padding is safer */
            padding-top: 50px; 
            box-sizing: border-box;
        }

        .header-text {
            font-family: 'Cinzel', serif;
            font-size: 40px; /* Use px for PDF consistency */
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 5px;
            margin-top: 40px;
        }
        .sub-header {
            font-family: 'Cinzel', serif;
            font-size: 20px;
            color: #4b5563;
            margin-bottom: 30px;
            letter-spacing: 2px;
        }
        .presented-to {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 10px;
            font-style: italic;
        }
        .recipient-name {
            font-family: 'Great Vibes', cursive;
            font-size: 60px;
            color: #d97706;
            margin-bottom: 20px;
            line-height: 1;
            text-shadow: 1px 1px 2px rgba(255,255,255,0.8);
        }
        .description {
            font-size: 16px;
            color: #374151;
            width: 80%;
            margin: 0 auto 30px;
            line-height: 1.6;
            background-color: rgba(255, 255, 255, 0.6);
            padding: 15px;
            border-radius: 5px;
        }
        .event-name {
            font-weight: 700;
            color: #1f2937;
        }
        .rank-text {
            font-weight: 700;
            color: #d97706;
        }

        /* Signatures Table for PDF compatibility */
        .signatures-table {
            width: 80%;
            margin: 40px auto 0;
            border-collapse: collapse;
        }
        .signatures-table td {
            width: 50%;
            vertical-align: top;
            text-align: center;
            padding: 0 20px;
        }
        .signature-line {
            width: 100%;
            border-top: 2px solid #1f2937;
            margin-bottom: 10px;
            display: block;
        }
        .signature-name {
            font-weight: 600;
            color: #1f2937;
            font-size: 14px;
        }
        .signature-title {
            font-size: 12px;
            color: #6b7280;
        }

        .btn-container {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }
        .print-btn {
            background-color: #4f46e5;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            text-decoration: none;
            display: inline-block;
        }
        .print-btn:hover {
            background-color: #4338ca;
        }
        @media print {
            .btn-container { display: none; }
            .certificate-container { box-shadow: none; border: none; }
        }
    </style>
</head>
<body>
    @unless($isPdf ?? false)
    <div class="btn-container">
        <a href="{{ route('events.certificate.download', ['eventId' => $evento->id, 'teamId' => $equipo->id]) }}" class="print-btn">
            Descargar PDF
        </a>
        <button class="print-btn" onclick="window.print()">Imprimir / Guardar como PDF</button>
    </div>
    @endunless

    <div class="certificate-container">
        <div class="content-wrapper">
            <!-- Optional Watermark SVG -->
            <!-- <svg class="watermark" ...> (Removed for cleaner look) -->

            <div class="header-text">Certificado de Reconocimiento</div>
            <div class="sub-header">Premio a la Excelencia en Innovación</div>

            <div class="presented-to">Otorgado al equipo</div>
            <div class="recipient-name">{{ $equipo->nombre }}</div>

            <div class="description">
                Por obtener el <span class="rank-text">{{ $rankText }}º Lugar</span> en el evento <span class="event-name">{{ $evento->nombre }}</span>.<br>
                Reconocemos su destacado desempeño, creatividad y contribución tecnológica con el proyecto "{{ $equipo->project_name ?? 'Proyecto Innovador' }}".
            </div>

            <table class="signatures-table">
                <tr>
                    <td>
                        <div class="signature-line"></div>
                        <div class="signature-name">Comité Organizador</div>
                        <div class="signature-title">TeamSync</div>
                    </td>
                    <td>
                        <div class="signature-line"></div>
                        <div class="signature-name">{{ now()->format('d \d\e F \d\e Y') }}</div>
                        <div class="signature-title">Fecha</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <script>
        // Auto-print on load if desired, or let user click button
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
