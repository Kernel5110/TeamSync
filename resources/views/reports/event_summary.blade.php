<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Evento</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 20px;
        }
        h1 {
            color: #4f46e5;
            margin: 0;
            font-size: 24px;
        }
        .meta {
            margin-top: 10px;
            font-size: 14px;
            color: #666;
        }
        .section {
            margin-bottom: 30px;
        }
        h2 {
            font-size: 18px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 15px;
            color: #1f2937;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background-color: #f9fafb;
            font-weight: bold;
            color: #374151;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .stat-item {
            display: table-cell;
            text-align: center;
            padding: 15px;
            background-color: #f3f4f6;
            border-radius: 8px;
            width: 33%;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #4f46e5;
            display: block;
        }
        .stat-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $evento->nombre }}</h1>
        <div class="meta">
            {{ $evento->fecha_inicio->format('d/m/Y') }} - {{ $evento->fecha_fin->format('d/m/Y') }} | {{ $evento->ubicacion }}
        </div>
        <div class="meta">
            Categoría: {{ $evento->categoria ?? 'General' }}
        </div>
    </div>

    <div class="section">
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-number">{{ $evento->teams->count() }}</span>
                <span class="stat-label">Equipos</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $evento->teams->sum(function($team) { return $team->participants->count(); }) }}</span>
                <span class="stat-label">Participantes</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">{{ $evento->status }}</span>
                <span class="stat-label">Estado</span>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Ranking de Equipos</h2>
        @if($ranking->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 10%;">Pos</th>
                        <th style="width: 30%;">Equipo</th>
                        <th style="width: 20%;">Proyecto</th>
                        <th style="width: 20%;">Tecnologías</th>
                        <th style="width: 20%;">Puntaje</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ranking as $index => $team)
                        <tr>
                            <td>
                                @if($index < 3)
                                    <span style="font-weight: bold; color: #d97706;">#{{ $index + 1 }}</span>
                                @else
                                    #{{ $index + 1 }}
                                @endif
                            </td>
                            <td>{{ $team->nombre }}</td>
                            <td>{{ $team->project_name ?? 'N/A' }}</td>
                            <td>{{ $team->technologies ?? 'N/A' }}</td>
                            <td><strong>{{ number_format($team->total_score, 2) }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="font-style: italic; color: #666;">No hay evaluaciones registradas aún.</p>
        @endif
    </div>

    <div class="section">
        <h2>Detalle de Equipos</h2>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Miembros</th>
                    <th>Repositorio</th>
                    <th>Progreso</th>
                </tr>
            </thead>
            <tbody>
                @foreach($evento->teams as $team)
                    <tr>
                        <td>{{ $team->nombre }}</td>
                        <td>
                            @foreach($team->participants as $p)
                                {{ $p->user->name }}<br>
                            @endforeach
                        </td>
                        <td>
                            @if($team->github_repo)
                                <a href="{{ $team->github_repo }}" target="_blank">Ver Repo</a>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $team->progress }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
