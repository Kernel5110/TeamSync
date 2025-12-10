@extends('layouts.app')

@section('title', 'Evaluación de Evento - TeamSync')



@section('content')
<div class="evaluation-container">
    <div class="evaluation-header">
        <h1>Evaluación: {{ $evento->name }}</h1>
        <p>Panel de Juez - Calificación de Proyectos</p>
    </div>

    @if(session('error'))
        <div style="width: 100%; background-color: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border: 1px solid #fecaca;">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div style="width: 100%; background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border: 1px solid #a7f3d0;">
            {{ session('success') }}
        </div>
    @endif

    <div class="evaluation-grid">
        <!-- Lista de Equipos -->
        <div class="teams-section">
            <h2 style="margin-bottom: 1.5rem; font-size: 1.5rem; font-weight: 600;">Equipos Participantes</h2>
            <div class="teams-list">
                @forelse($evento->teams as $equipo)
                    <a href="{{ route('events.evaluate.team', ['eventId' => $evento->id, 'teamId' => $equipo->id]) }}" style="text-decoration: none; color: inherit;">
                        <div class="team-card" style="cursor: pointer; transition: transform 0.2s; border: 1px solid #e5e7eb; border-radius: 8px; padding: 15px; margin-bottom: 10px; background: white;">
                            <div class="team-name" style="font-weight: 600; font-size: 1.1rem; color: #1f2937; margin-bottom: 5px;">{{ $equipo->name }}</div>
                            <div class="team-members" style="font-size: 0.9rem; color: #6b7280; margin-bottom: 10px;">
                                {{ $equipo->participants->map(fn($p) => $p->user->name ?? 'Participante')->join(', ') }}
                            </div>
                            @if(in_array($equipo->id, $evaluatedTeams))
                                <span class="status-badge status-completed" style="background-color: #d1fae5; color: #065f46; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">Evaluado</span>
                            @else
                                <span class="status-badge status-pending" style="background-color: #f3f4f6; color: #4b5563; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem;">Evaluar</span>
                            @endif
                        </div>
                    </a>
                @empty
                    <p class="text-gray-500">No hay equipos registrados en este evento.</p>
                @endforelse
            </div>
        </div>

        <!-- Panel de Evaluación -->
        <div class="evaluation-panel">
            <div style="text-align: center; padding: 40px; color: #6b7280;">
                <x-icon name="touch_app" style="font-size: 48px; margin-bottom: 20px; color: #9ca3af;" />
                <h3 style="font-size: 1.2rem; font-weight: 600; margin-bottom: 10px;">Selecciona un equipo</h3>
                <p>Haz clic en un equipo de la lista para ver su proyecto y realizar la evaluación.</p>
            </div>
        </div>
    </div>
</div>


@endsection
