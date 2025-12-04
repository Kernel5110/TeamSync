@extends('layouts.app')

@section('title', 'Evaluar Equipo - TeamSync')

@section('content')
<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 40px 20px;">
    <div class="header-section" style="margin-bottom: 40px; text-align: center;">
        <h1 style="font-size: 2.5rem; font-weight: 700; color: #1f2937; margin-bottom: 10px;">Evaluación de Proyecto</h1>
        <p style="color: #6b7280; font-size: 1.1rem;">Evento: {{ $evento->nombre }}</p>
    </div>

    <div class="grid-container" style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
        <!-- Left Column: Project Details -->
        <div class="project-details">
            <div class="card" style="background: white; padding: 30px; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); margin-bottom: 30px;">
                <h2 style="font-size: 1.5rem; font-weight: 600; color: #1f2937; margin-bottom: 20px; border-bottom: 2px solid #f3f4f6; padding-bottom: 10px;">
                    Información del Equipo
                </h2>
                <div style="margin-bottom: 20px;">
                    <h3 style="font-size: 1.1rem; font-weight: 600; color: #4b5563; margin-bottom: 5px;">Nombre del Equipo</h3>
                    <p style="font-size: 1.2rem; color: #111827; font-weight: 500;">{{ $equipo->nombre }}</p>
                </div>
                <div style="margin-bottom: 20px;">
                    <h3 style="font-size: 1.1rem; font-weight: 600; color: #4b5563; margin-bottom: 5px;">Integrantes</h3>
                    <ul style="list-style: none; padding: 0;">
                        @foreach($equipo->participantes as $participante)
                            <li style="margin-bottom: 5px; color: #374151;">
                                <span class="material-icons" style="font-size: 16px; vertical-align: middle; color: #9ca3af;">person</span>
                                {{ $participante->user->name }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="card" style="background: white; padding: 30px; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                <h2 style="font-size: 1.5rem; font-weight: 600; color: #1f2937; margin-bottom: 20px; border-bottom: 2px solid #f3f4f6; padding-bottom: 10px;">
                    Detalles del Proyecto
                </h2>
                
                <div style="margin-bottom: 25px;">
                    <h3 style="font-size: 1.1rem; font-weight: 600; color: #4b5563; margin-bottom: 5px;">Nombre del Proyecto</h3>
                    <p style="font-size: 1.1rem; color: #1f2937;">{{ $equipo->project_name ?? 'No especificado' }}</p>
                </div>

                <div style="margin-bottom: 25px;">
                    <h3 style="font-size: 1.1rem; font-weight: 600; color: #4b5563; margin-bottom: 5px;">Descripción</h3>
                    <p style="color: #4b5563; line-height: 1.6;">{{ $equipo->project_description ?? 'Sin descripción disponible.' }}</p>
                </div>

                <div style="margin-bottom: 25px;">
                    <h3 style="font-size: 1.1rem; font-weight: 600; color: #4b5563; margin-bottom: 5px;">Tecnologías</h3>
                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                        @if($equipo->technologies)
                            @foreach(explode(',', $equipo->technologies) as $tech)
                                <span style="background-color: #e0e7ff; color: #4338ca; padding: 4px 10px; border-radius: 20px; font-size: 0.9rem; font-weight: 500;">
                                    {{ trim($tech) }}
                                </span>
                            @endforeach
                        @else
                            <span style="color: #9ca3af;">No especificadas</span>
                        @endif
                    </div>
                </div>

                <div style="margin-bottom: 10px;">
                    <h3 style="font-size: 1.1rem; font-weight: 600; color: #4b5563; margin-bottom: 10px;">Enlaces</h3>
                    <div style="display: flex; gap: 15px;">
                        @if($equipo->github_repo)
                            <a href="{{ $equipo->github_repo }}" target="_blank" style="display: inline-flex; align-items: center; gap: 5px; color: #2563eb; text-decoration: none; font-weight: 500;">
                                <span class="material-icons">code</span> Repositorio
                            </a>
                        @endif
                        @if($equipo->github_pages)
                            <a href="{{ $equipo->github_pages }}" target="_blank" style="display: inline-flex; align-items: center; gap: 5px; color: #2563eb; text-decoration: none; font-weight: 500;">
                                <span class="material-icons">launch</span> Demo
                            </a>
                        @endif
                        @if(!$equipo->github_repo && !$equipo->github_pages)
                            <span style="color: #9ca3af;">No hay enlaces disponibles</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Evaluation Form -->
        <div class="evaluation-form-container">
            <div class="card" style="background: white; padding: 30px; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); position: sticky; top: 20px;">
                <h2 style="font-size: 1.5rem; font-weight: 600; color: #1f2937; margin-bottom: 20px; border-bottom: 2px solid #f3f4f6; padding-bottom: 10px;">
                    Formulario de Evaluación
                </h2>

                <form action="{{ route('event.evaluate.store', $evento->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="equipo_id" value="{{ $equipo->id }}">

                    <div class="criteria-group" style="margin-bottom: 25px;">
                        <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 10px;">Innovación (0-10)</label>
                        <input type="range" name="score_innovation" min="0" max="10" value="{{ $evaluation->score_innovation ?? 5 }}" style="width: 100%; margin-bottom: 10px;" oninput="document.getElementById('score-1').textContent = this.value">
                        <div style="text-align: right; font-weight: 600; color: #4f46e5;">Puntaje: <span id="score-1">{{ $evaluation->score_innovation ?? 5 }}</span>/10</div>
                    </div>

                    <div class="criteria-group" style="margin-bottom: 25px;">
                        <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 10px;">Impacto Social (0-10)</label>
                        <input type="range" name="score_social_impact" min="0" max="10" value="{{ $evaluation->score_social_impact ?? 5 }}" style="width: 100%; margin-bottom: 10px;" oninput="document.getElementById('score-2').textContent = this.value">
                        <div style="text-align: right; font-weight: 600; color: #4f46e5;">Puntaje: <span id="score-2">{{ $evaluation->score_social_impact ?? 5 }}</span>/10</div>
                    </div>

                    <div class="criteria-group" style="margin-bottom: 25px;">
                        <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 10px;">Viabilidad Técnica (0-10)</label>
                        <input type="range" name="score_technical_viability" min="0" max="10" value="{{ $evaluation->score_technical_viability ?? 5 }}" style="width: 100%; margin-bottom: 10px;" oninput="document.getElementById('score-3').textContent = this.value">
                        <div style="text-align: right; font-weight: 600; color: #4f46e5;">Puntaje: <span id="score-3">{{ $evaluation->score_technical_viability ?? 5 }}</span>/10</div>
                    </div>

                    <div class="criteria-group" style="margin-bottom: 30px;">
                        <label style="display: block; font-weight: 600; color: #374151; margin-bottom: 10px;">Comentarios</label>
                        <textarea name="comments" rows="4" style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-family: inherit;" placeholder="Escribe tus observaciones aquí...">{{ $evaluation->comments ?? '' }}</textarea>
                    </div>

                    <div style="display: flex; gap: 15px;">
                        <a href="{{ route('event.evaluate', $evento->id) }}" style="flex: 1; padding: 12px; text-align: center; border: 1px solid #d1d5db; border-radius: 8px; color: #374151; text-decoration: none; font-weight: 600;">Cancelar</a>
                        <button type="submit" style="flex: 2; padding: 12px; background: linear-gradient(to right, #6a11cb 0%, #2575fc 100%); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                            {{ isset($evaluation) ? 'Actualizar Evaluación' : 'Enviar Evaluación' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
