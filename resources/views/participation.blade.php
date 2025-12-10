@extends('layouts.app')



@section('content')
<div class="contenedor-participacion">
    <div class="tarjeta-participacion">
        <div class="header-participacion">
            <h1>{{ $evento->name }}</h1>
            <p>Participación en el evento</p>
        </div>
        
        <div class="contenido-participacion">
            <div class="seccion-problema">
                <h2>Descripción del Problema</h2>
                <div class="descripcion-problema">
                    @if($evento->problem_statement)
                        {!! nl2br(e($evento->problem_statement)) !!}
                    @else
                        <p style="font-style: italic; color: #9ca3af;">La descripción del problema aún no ha sido publicada.</p>
                    @endif
                </div>
            </div>

            @if(isset($rank) && $rank <= 3)
                <div class="seccion-ganador" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 2px solid #fbbf24; border-radius: 12px; padding: 20px; margin-bottom: 30px; text-align: center;">
                    <x-icon name="emoji_events" style="font-size: 48px; color: #d97706; margin-bottom: 10px;" />
                    <h2 style="color: #92400e; margin-bottom: 10px;">¡Felicidades!</h2>
                    <p style="color: #b45309; font-size: 1.1rem; margin-bottom: 20px;">
                        Tu equipo ha obtenido el <strong>{{ $rank }}º Lugar</strong> en este evento.
                    </p>
                    <a href="{{ route('events.certificate', ['eventId' => $evento->id, 'teamId' => $equipo->id]) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 8px; background-color: #d97706; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: background-color 0.2s;">
                        <x-icon name="download" /> Descargar Certificado
                    </a>
                </div>
            @endif

            <div class="seccion-upload">
                <h2>Subir Solución</h2>
                
                @if(session('success'))
                    <div class="alert-success" role="alert">
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert-error" role="alert">
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                @if($isEvaluated)
                    <div style="background-color: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <x-icon name="lock" style="vertical-align: middle; margin-right: 5px;" />
                        Tu proyecto ya ha sido evaluado por los jueces. No se pueden realizar más cambios.
                    </div>
                @endif

                <form action="{{ route('events.participate.upload', $evento->id) }}" method="POST" class="form-upload" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="form-group">
                        <label for="project_name">Nombre del Proyecto</label>
                        <input type="text" name="project_name" id="project_name" class="input-text" required placeholder="Ej. Sistema de Gestión Inteligente" value="{{ $equipo->project_name ?? '' }}" {{ $isEvaluated ? 'disabled' : '' }}>
                    </div>

                    <div class="form-group">
                        <label for="technologies">Tecnologías a usar</label>
                        <input type="text" name="technologies" id="technologies" class="input-text" required placeholder="Ej. Laravel, Vue.js, MySQL" value="{{ $equipo->technologies ?? '' }}" {{ $isEvaluated ? 'disabled' : '' }}>
                    </div>

                    <div class="form-group">
                        <label for="github_repo">Repositorio de GitHub</label>
                        <input type="url" name="github_repo" id="github_repo" class="input-text" required placeholder="https://github.com/usuario/repo" value="{{ $equipo->github_repo ?? '' }}" {{ $isEvaluated ? 'disabled' : '' }}>
                    </div>

                    <div class="form-group">
                        <label for="github_pages">GitHub Pages (Opcional)</label>
                        <input type="url" name="github_pages" id="github_pages" class="input-text" placeholder="https://usuario.github.io/repo" value="{{ $equipo->github_pages ?? '' }}" {{ $isEvaluated ? 'disabled' : '' }}>
                    </div>

                    <div class="form-group">
                        <label for="project_description">Descripción y Avances</label>
                        <textarea name="project_description" id="project_description" class="input-textarea" rows="5" required placeholder="Describe tu proyecto y los avances realizados..." {{ $isEvaluated ? 'disabled' : '' }}>{{ $equipo->project_description ?? '' }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="evidence">Evidencia (PDF, Imagen - Máx 5MB)</label>
                        @if($equipo->evidence_path)
                            <div style="margin-bottom: 10px;">
                                <a href="{{ asset('storage/' . $equipo->evidence_path) }}" target="_blank" style="color: #4f46e5; text-decoration: underline;">Ver Evidencia Actual</a>
                            </div>
                        @endif
                        <input type="file" name="evidence" id="evidence" class="input-text" accept=".pdf,image/*" {{ $isEvaluated ? 'disabled' : '' }}>
                    </div>
                    
                    @if(!$isEvaluated)
                        <button type="submit" class="btn-subir">
                            Guardar Información
                        </button>
                    @endif
                </form>

                <div class="timeline-section" style="margin-top: 40px;">
                    <h2>Línea de Tiempo de Avances</h2>
                    <div class="timeline">
                        <!-- Example Timeline Items -->
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <h3>Inicio del Proyecto</h3>
                                <p>Definición de la idea y formación del equipo.</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <h3>Prototipado</h3>
                                <p>Diseño de mockups y arquitectura de base de datos.</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <h3>Desarrollo MVP</h3>
                                <p>Implementación de funcionalidades core.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
