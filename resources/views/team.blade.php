@extends('layouts.app')

@section('title', 'Equipo - TeamSync')

@push('styles')
    @vite(['resources/css/equipos.css'])
@endpush
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

@section('content')
    <div class="contenedor-equipo">
        @if(session('success'))
            <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                {{ session('error') }}
            </div>
        @endif

        <div class="header-equipo">
            <div>
                <h1>Equipo</h1>
                <p>Gestiona tu equipo de desarrollo y colaboradores</p>
            </div>
            <div class="acciones-equipo">
                <a href="#" class="btn-buscar">
                    <span class="material-icons">search</span> Buscar
                </a>
                @if(!$equipo)
                    <a href="#modal-crear-equipo" class="btn-nuevo">
                        <span class="material-icons">add</span> Nuevo
                    </a>
                @else
                    <a href="#modal-agregar-miembro" class="btn-nuevo">
                        <span class="material-icons">person_add</span> Agregar Miembro
                    </a>
                @endif
            </div>
        </div>

        @if($equipo)
            <div class="tarjeta-miembros">
                <div class="titulo-seccion">Miembros del Equipo: {{ $equipo->nombre }}</div>
                <table class="tabla-miembros">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Institución</th>
                            <th>Carrera</th>
                            <th>Rol</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($equipo->participantes as $participante)
                            <tr>
                                <td>{{ $participante->user->name }}</td>
                                <td>{{ $participante->institucion }}</td>
                                <td>{{ $participante->carrera->nombre }}</td>
                                <td>
                                    @php
                                        $rolClass = 'rol-analista';
                                        if (stripos($participante->rol, 'programador') !== false) $rolClass = 'rol-programador';
                                        if (stripos($participante->rol, 'diseñador') !== false) $rolClass = 'rol-disenador';
                                    @endphp
                                    <span class="badge-rol {{ $rolClass }}">
                                        @if($rolClass == 'rol-programador') <span class="material-icons" style="font-size: 14px;">code</span> @endif
                                        @if($rolClass == 'rol-disenador') <span class="material-icons" style="font-size: 14px;">palette</span> @endif
                                        {{ $participante->rol ?? 'Miembro' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="stats-grid">
                <div class="stat-card blue">
                    <div class="stat-number">{{ $equipo->participantes->count() }}</div>
                    <div class="stat-label">Total Miembros</div>
                </div>
                <div class="stat-card cyan">
                    <div class="stat-number">1</div>
                    <div class="stat-label">Eventos Activos</div>
                </div>
                <div class="stat-card green">
                    <div class="stat-number">1</div>
                    <div class="stat-label">Proyectos</div>
                </div>
                <div class="stat-card orange">
                    <div class="stat-number">85%</div>
                    <div class="stat-label">Progreso</div>
                </div>
            </div>
        @else
            <div class="tarjeta-miembros" style="text-align: center; padding: 3rem;">
                <span class="material-icons" style="font-size: 4rem; color: #d1d5db;">groups</span>
                <h2 style="margin-top: 1rem; color: #374151;">No tienes un equipo aún</h2>
                <p style="color: #6b7280; margin-bottom: 2rem;">Crea un equipo para participar en eventos o espera a ser invitado.</p>
                <a href="#modal-crear-equipo" class="btn-nuevo" style="display: inline-flex;">
                    Crear Equipo
                </a>
            </div>
        @endif
    </div>

    <!-- Modal Crear Equipo -->
    <div id="modal-crear-equipo" class="modal">
        <div class="modal-content">
            <a href="#" class="close-modal">&times;</a>
            <h2 style="margin-bottom: 1.5rem;">Crear Nuevo Equipo</h2>
            <form action="{{ route('team.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="nombre">Nombre del Equipo</label>
                    <input type="text" id="nombre" name="nombre" required placeholder="Ej. Alpha Team">
                </div>
                <div class="form-group">
                    <label for="evento_id">Evento</label>
                    <select id="evento_id" name="evento_id" required>
                        @foreach($eventos as $evento)
                            <option value="{{ $evento->id }}">{{ $evento->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-submit">Crear Equipo</button>
            </form>
        </div>
    </div>

    <!-- Modal Agregar Miembro -->
    <div id="modal-agregar-miembro" class="modal">
        <div class="modal-content">
            <a href="#" class="close-modal">&times;</a>
            <h2 style="margin-bottom: 1.5rem;">Agregar Miembro</h2>
            <form action="{{ route('team.addMember') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email">Correo Electrónico del Usuario</label>
                    <input type="email" id="email" name="email" required placeholder="usuario@ejemplo.com">
                </div>
                <button type="submit" class="btn-submit">Enviar Invitación</button>
            </form>
        </div>
    </div>
@endsection
