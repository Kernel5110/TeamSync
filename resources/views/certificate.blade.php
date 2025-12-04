<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificado de Reconocimiento - {{ $equipo->nombre }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Great+Vibes&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
        @page {
            size: landscape;
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            font-family: 'Open Sans', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .certificate-container {
            width: 297mm;
            height: 210mm;
            background-color: #fff;
            position: relative;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            border: 20px solid #fff;
            outline: 5px solid #1f2937;
            outline-offset: -10px;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.05;
            width: 60%;
            pointer-events: none;
        }
        .header-text {
            font-family: 'Cinzel', serif;
            font-size: 3rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 5px;
        }
        .sub-header {
            font-family: 'Cinzel', serif;
            font-size: 1.5rem;
            color: #4b5563;
            margin-bottom: 40px;
            letter-spacing: 2px;
        }
        .presented-to {
            font-size: 1.2rem;
            color: #6b7280;
            margin-bottom: 20px;
            font-style: italic;
        }
        .recipient-name {
            font-family: 'Great Vibes', cursive;
            font-size: 4rem;
            color: #d97706; /* Gold/Amber color */
            margin-bottom: 20px;
            line-height: 1;
        }
        .description {
            font-size: 1.2rem;
            color: #374151;
            max-width: 80%;
            margin: 0 auto 40px;
            line-height: 1.6;
        }
        .event-name {
            font-weight: 700;
            color: #1f2937;
        }
        .rank-text {
            font-weight: 700;
            color: #d97706;
        }
        .signatures {
            display: flex;
            justify-content: space-around;
            width: 80%;
            margin-top: 40px;
        }
        .signature-block {
            text-align: center;
        }
        .signature-line {
            width: 250px;
            border-top: 2px solid #1f2937;
            margin-bottom: 10px;
        }
        .signature-name {
            font-weight: 600;
            color: #1f2937;
        }
        .signature-title {
            font-size: 0.9rem;
            color: #6b7280;
        }
        .date {
            position: absolute;
            bottom: 40px;
            font-size: 1rem;
            color: #6b7280;
        }
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #4f46e5;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        .print-btn:hover {
            background-color: #4338ca;
        }
        @media print {
            body {
                background-color: #fff;
            }
            .certificate-container {
                box-shadow: none;
                width: 100%;
                height: 100%;
                border: none; /* Let the outline handle the border look */
                outline: 5px solid #1f2937;
                outline-offset: -10px;
                page-break-after: always;
            }
            .print-btn {
                display: none;
            }
            @page {
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">Imprimir / Guardar como PDF</button>

    <div class="certificate-container">
        <!-- Optional Watermark SVG -->
        <svg class="watermark" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
        </svg>

        <div class="header-text">Certificado de Reconocimiento</div>
        <div class="sub-header">Premio a la Excelencia en Innovación</div>

        <div class="presented-to">Otorgado al equipo</div>
        <div class="recipient-name">{{ $equipo->nombre }}</div>

        <div class="description">
            Por obtener el <span class="rank-text">{{ $rank }}º Lugar</span> en el evento <span class="event-name">{{ $evento->nombre }}</span>.<br>
            Reconocemos su destacado desempeño, creatividad y contribución tecnológica con el proyecto "{{ $equipo->project_name ?? 'Proyecto Innovador' }}".
        </div>

        <div class="signatures">
            <div class="signature-block">
                <div class="signature-line"></div>
                <div class="signature-name">Comité Organizador</div>
                <div class="signature-title">TeamSync</div>
            </div>
            <div class="signature-block">
                <div class="signature-line"></div>
                <div class="signature-name">{{ now()->format('d \d\e F \d\e Y') }}</div>
                <div class="signature-title">Fecha</div>
            </div>
        </div>
    </div>

    <script>
        // Auto-print on load if desired, or let user click button
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
