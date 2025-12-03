@extends('layouts.app')

@section('title', 'Evaluación de Evento - TeamSync')



@section('content')
<div class="evaluation-container">
    <div class="evaluation-header">
        <h1>Evaluación: {{ $evento->nombre }}</h1>
        <p>Panel de Juez - Calificación de Proyectos</p>
    </div>

    <div class="evaluation-grid">
        <!-- Lista de Equipos -->
        <div class="teams-section">
            <h2 style="margin-bottom: 1.5rem; font-size: 1.5rem; font-weight: 600;">Equipos Participantes</h2>
            <div class="teams-list">
                @forelse($evento->equipos as $equipo)
                    <div class="team-card" onclick="selectTeam(this, '{{ $equipo->nombre }}', {{ $equipo->id }})">
                        <div class="team-name">{{ $equipo->nombre }}</div>
                        <div class="team-members">
                            {{ $equipo->participantes->map(fn($p) => $p->user->name ?? 'Participante')->join(', ') }}
                        </div>
                        <span class="status-badge status-pending">Pendiente</span>
                    </div>
                @empty
                    <p class="text-gray-500">No hay equipos registrados en este evento.</p>
                @endforelse
            </div>
        </div>

        <!-- Panel de Evaluación -->
        <div class="evaluation-panel">
            <h3 class="panel-title">Evaluar: <span id="selected-team-name">Seleccione un equipo</span></h3>
            
            <form id="evaluation-form" action="{{ route('event.evaluate.store', $evento->id) }}" method="POST">
                @csrf
                <input type="hidden" name="equipo_id" id="selected-team-id" required>
                
                <div class="criteria-group">
                    <label class="criteria-label">Innovación (0-10)</label>
                    <input type="range" name="score_innovation" min="0" max="10" value="5" class="range-slider" oninput="updateScore(this, 'score-1')">
                    <div class="score-display">Puntaje: <span id="score-1">5</span>/10</div>
                </div>

                <div class="criteria-group">
                    <label class="criteria-label">Impacto Social (0-10)</label>
                    <input type="range" name="score_social_impact" min="0" max="10" value="5" class="range-slider" oninput="updateScore(this, 'score-2')">
                    <div class="score-display">Puntaje: <span id="score-2">5</span>/10</div>
                </div>

                <div class="criteria-group">
                    <label class="criteria-label">Viabilidad Técnica (0-10)</label>
                    <input type="range" name="score_technical_viability" min="0" max="10" value="5" class="range-slider" oninput="updateScore(this, 'score-3')">
                    <div class="score-display">Puntaje: <span id="score-3">5</span>/10</div>
                </div>

                <div class="criteria-group">
                    <label class="criteria-label">Comentarios</label>
                    <textarea name="comments" class="comment-box" placeholder="Escribe tus observaciones aquí..."></textarea>
                </div>

                <button type="submit" class="btn-submit">Enviar Evaluación</button>
            </form>
        </div>
    </div>
</div>

<script>
    function selectTeam(card, name, id) {
        // Remove selected class from all cards
        document.querySelectorAll('.team-card').forEach(c => c.classList.remove('selected'));
        // Add selected class to clicked card
        card.classList.add('selected');
        // Update panel title
        document.getElementById('selected-team-name').textContent = name;
        // Set hidden input value
        document.getElementById('selected-team-id').value = id;
    }

    function updateScore(input, displayId) {
        document.getElementById(displayId).textContent = input.value;
    }
</script>
@endsection
