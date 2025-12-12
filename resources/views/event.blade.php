@extends('layouts.app')

@section('title', 'Eventos - TeamSync')



@section('content')
    @if(session('success'))
        <div style="width: 100%; max-width: 1200px; margin: 0 auto 1rem auto; background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: 0.5rem; border: 1px solid #a7f3d0;">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="width: 100%; max-width: 1200px; margin: 0 auto 1rem auto; background-color: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 0.5rem; border: 1px solid #fecaca;">
            <ul style="list-style: disc; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="contenedor-eventos">



        <style>
            #modal-evento .modal-content,
            #modal-assign-judge .modal-content {
                background-color: #ffffff !important;
                opacity: 1 !important;
            }

            /* Ensure form inputs have solid background */
            .profile-form input,
            .profile-form textarea,
            .profile-form select {
                background-color: #f9fafb !important;
            }

            .profile-form input:focus,
            .profile-form textarea:focus,
            .profile-form select:focus {
                background-color: #ffffff !important;
            }

            /* Admin Actions Grid */
            .admin-actions-container {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
                gap: 15px;
                margin-top: 15px;
                padding-top: 15px;
                border-top: 1px dashed #e5e7eb;
            }

            .action-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 5px;
                text-align: center;
            }

            .action-label {
                font-size: 0.7rem;
                color: #6b7280;
                font-weight: 500;
            }
            
            .btn-admin-action {
                width: 40px !important;
                height: 40px !important;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: transform 0.2s;
            }

            .btn-admin-action:hover {
                transform: scale(1.1);
            }

            @media (max-width: 640px) {
                .admin-actions-container {
                     grid-template-columns: repeat(5, 1fr); /* 5 cols on mobile */
                     gap: 10px;
                }
                .action-label {
                    font-size: 0.65rem;
                }
                .btn-admin-action {
                    width: 36px !important;
                    height: 36px !important;
                }
            }
        </style>

        @can('create events')
            <div style="position: absolute; top: 100px; right: 20px; z-index: 100;">
                <button id="btn-crear-evento" class="btn-confirmar" style="width: auto; padding: 8px 15px; font-size: 0.9rem;">
                    <x-icon name="add" style="vertical-align: middle; margin-right: 5px; font-size: 1.1rem;" /> Crear Evento
                </button>
            </div>
        @endcan

        @foreach($eventos as $evento)
            <div class="tarjeta-evento">
                <div class="evento-header">
                    @if($evento->image_path)
                        <div style="width: 100%; height: 180px; overflow: hidden; border-radius: 8px 8px 0 0; margin-bottom: 10px;">
                            <img src="{{ asset('storage/' . $evento->image_path) }}" alt="{{ $evento->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    @else
                        <div class="evento-titulo">
                            <div class="icono-evento {{ $loop->even ? 'icono-innovatec' : 'icono-hackatec' }}">
                                @if($loop->even)
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a10 10 0 1 0 10 10 10 10 0 0 0-10-10zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z"></path><path d="M12 6v6l4 2"></path></svg>
                                @else
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path><path d="M4 22h16"></path><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"></path></svg>
                                @endif
                            </div>
                            <h2>{{ $evento->name }}</h2>
                        </div>
                    @endif

                    @if($evento->image_path)
                         {{-- If having image, title is not inside 'evento-titulo' so render it here or structure differently. 
                              Let's keep the structure clean. If image, showing title below image. --}}
                         <h2 style="font-size: 1.5rem; font-weight: 700; color: #1f2937; margin: 10px 0;">{{ $evento->name }}</h2>
                    @endif

                    @php
                        $status = $evento->status;
                        $badgeColor = match($status) {
                            'En Curso' => '#10b981', // Green
                            'Próximo' => '#3b82f6', // Blue
                            'Finalizado' => '#6b7280', // Gray
                            default => '#3b82f6'
                        };
                    @endphp
                    <span class="badge-proximo" style="background-color: {{ $badgeColor }}; position: absolute; top: 20px; right: 20px;">{{ $status }}</span>
                </div>

                @if($evento->categories->count() > 0)
                    <div style="margin-bottom: 10px; display: flex; flex-wrap: wrap; gap: 5px;">
                        @foreach($evento->categories as $cat)
                            <span style="background-color: #e0e7ff; color: #4338ca; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">
                                {{ $cat->name }}
                            </span>
                        @endforeach
                    </div>
                @endif

                <p class="evento-descripcion">
                    {{ $evento->description }}
                </p>

                <div class="evento-detalles">
                    <div class="detalle-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        <span>{{ $evento->starts_at->format('d/m/Y H:i') }} - {{ $evento->ends_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="detalle-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        <span>{{ $evento->location }}</span>
                    </div>
                    <div class="detalle-item">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
    <span>Equipos: {{ $evento->teams->count() }} / {{ (int)$evento->capacity }}</span>
</div>
                </div>

                <div class="evento-acciones">

                    <a href="#" class="btn-detalles"
                       data-name="{{ $evento->name }}"
                       data-description="{{ $evento->description }}"
                       data-starts-at="{{ $evento->starts_at->format('d F Y H:i') }}"
                       data-ends-at="{{ $evento->ends_at->format('d F Y') }}"
                       data-location="{{ $evento->location }}"
                       data-capacity="{{ $evento->capacity }}">
                        Ver Detalles
                    </a>

                    <a href="{{ route('events.ranking', $evento->id) }}" class="btn-ranking" style="background-color: #f59e0b; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 500; font-size: 0.9rem; transition: background-color 0.2s; display: inline-flex; align-items: center; gap: 5px;">
                        <x-icon name="emoji_events" style="font-size: 18px;" /> Ranking
                    </a>

          {{-- 1. ABRE UN SOLO BLOQUE DE AUTENTICACIÓN. --}}
@auth

    @php
        // Lógica del Juez (se ejecuta si el usuario está logueado)
        $is_judge_or_assigned = Auth::user()->hasRole('juez') || $evento->judges->contains(Auth::id());
    @endphp

    {{-- 2. VERIFICA SI EL USUARIO ES JUEZ O ESTÁ ASIGNADO --}}
    @if ($is_judge_or_assigned)

        {{-- BLOQUE JUEZ: Muestra el botón EVALUAR --}}
        {{-- BLOQUE JUEZ: Muestra el botón EVALUAR --}}
        @if($evento->status !== 'Finalizado')
            <a href="{{ route('events.evaluate.show', $evento->id) }}" class="btn-participar" style="background-color: #8b5cf6; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 500; font-size: 0.9rem; transition: background-color 0.2s;">
                Evaluar
            </a>
        @else
            <button disabled class="btn-participar" style="background-color: #9ca3af; color: white; padding: 8px 16px; border-radius: 6px; font-weight: 500; font-size: 0.9rem; cursor: not-allowed; border: none; opacity: 0.7;">
                Evaluar (Finalizado)
            </button>
        @endif

    {{-- 3. SI NO ES JUEZ, ENTRA AQUÍ (@else es el alternativo del @if anterior) --}}
    @else

        {{-- BLOQUE PARTICIPANTE/OTRO ROL: Lógica original para ver si puede participar --}}
        @unlessrole('admin')
            @php
                $userParticipante = auth()->user()->participant;
                $userTeamId = $userParticipante ? $userParticipante->team_id : null;

                $hasTeamInEvent = \App\Models\Team::where('event_id', $evento->id)
                    ->whereHas('participants', function($q) {
                        $q->where('user_id', auth()->id());
                    })->exists();

                // Time Validation
                $now = now('America/Mexico_City');
                $hasStarted = $now->greaterThanOrEqualTo($evento->starts_at);
            @endphp

            @if($hasTeamInEvent)
                @if($hasStarted)
                    <a href="{{ route('events.participate.show', $evento->id) }}" class="btn-ver-proyecto" style="background-color: #10b981; color: white; padding: 0.75rem; border-radius: 9999px; text-decoration: none; font-weight: 600; font-size: 1rem; transition: background-color 0.2s; display: inline-block; text-align: center;">
                        Ver Proyecto
                    </a>
                @else
                    <button disabled style="background-color: #9ca3af; color: white; padding: 0.75rem; border-radius: 9999px; border: none; font-weight: 600; font-size: 1rem; cursor: not-allowed;">
                        Inicia: {{ $evento->starts_at->format('H:i') }}
                    </button>
                @endif
            @else
                @if($evento->status !== 'Finalizado')
                    <button class="btn-registrar" data-id="{{ $evento->id }}" data-name="{{ $evento->name }}" style="background-color: #4f46e5; color: white; padding: 0.75rem; border-radius: 9999px; text-decoration: none; font-weight: 600; font-size: 1rem; transition: background-color 0.2s; border: none; cursor: pointer;">
                        Participar
                    </button>
                    @if(!$hasStarted)
                        <div style="text-align: center; font-size: 0.8rem; color: #6b7280; margin-top: 5px;">
                            Inicia: {{ $evento->starts_at->format('d/m H:i') }}
                        </div>
                    @endif
                @else
                    <button disabled style="background-color: #9ca3af; color: white; padding: 0.75rem; border-radius: 9999px; border: none; font-weight: 600; font-size: 1rem; cursor: not-allowed;">
                        Finalizado
                    </button>
                @endif
            @endif
        @endunlessrole

    @endif {{-- Cierra la verificación de Juez/Participante --}}

    {{-- 4. ELIMINÉ @endhasrole que estaba al final, ya que no tenía @hasrole de apertura --}}

@endauth {{-- Cierra el único bloque de autenticación --}}

</div>

                @role('admin')
                    <div class="admin-actions-container">
                        <div class="action-item">
                            <button class="btn-admin-action view-judges btn-view-judges" 
                                    data-id="{{ $evento->id }}" 
                                    data-judges="{{ $evento->judges->toJson() }}"
                                    title="Gestionar Jueces"
                                    style="background: linear-gradient(90deg, #8F00FF 0%, #97d7ff 100%); border: none;">
                                <x-icon name="gavel" style="color: white;" />
                            </button>
                            <span class="action-label">Jueces</span>
                        </div>

                        <div class="action-item">
                            <button class="btn-admin-action edit btn-editar-evento"
                                    data-id="{{ $evento->id }}"
                                    data-name="{{ $evento->name }}"
                                    data-description="{{ $evento->description }}"
                                    data-starts-at="{{ $evento->starts_at->format('Y-m-d') }}"
                                    data-start-time="{{ $evento->starts_at->format('H:i') }}"
                                    data-ends-at="{{ $evento->ends_at->format('Y-m-d') }}"
                                    data-location="{{ $evento->location }}"
                                    data-capacity="{{ $evento->capacity }}"
                                    data-categories="{{ $evento->categories->pluck('name')->toJson() }}"
                                    data-criteria="{{ $evento->criteria->toJson() }}"
                                    data-status-manual="{{ $evento->status_manual }}"
                                    title="Editar Evento">
                                <x-icon name="edit" />
                            </button>
                            <span class="action-label">Editar</span>
                        </div>

                        <div class="action-item">
                            <button class="btn-admin-action view-teams btn-ver-equipos"
                                    onclick="toggleTeams('teams-{{ $evento->id }}')"
                                    title="Ver Equipos">
                                <x-icon name="groups" />
                            </button>
                            <span class="action-label">Equipos</span>
                        </div>

                        <div class="action-item">
                            <form action="{{ route('events.destroy', $evento->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este evento?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-admin-action delete" title="Eliminar">
                                    <x-icon name="delete" />
                                </button>
                            </form>
                            <span class="action-label">Eliminar</span>
                        </div>

                        <div class="action-item">
                            <a href="{{ route('events.reports.pdf', $evento->id) }}" class="btn-admin-action" title="PDF" style="color: #dc2626; background: rgba(220, 38, 38, 0.1);">
                                <x-icon name="picture_as_pdf" style="width: 20px; height: 20px; color: #dc2626;" />
                            </a>
                            <span class="action-label">PDF</span>
                        </div>

                        <div class="action-item">
                            <a href="{{ route('events.reports.csv', $evento->id) }}" class="btn-admin-action" title="CSV" style="color: #16a34a; background: rgba(22, 163, 74, 0.1);">
                                <x-icon name="table_view" style="width: 20px; height: 20px; color: #16a34a;" />
                            </a>
                            <span class="action-label">Excel</span>
                        </div>

                        <div class="action-item">
                            <form action="{{ route('events.reports.email', $evento->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-admin-action" title="Enviar" style="color: #2563eb; background: rgba(37, 99, 235, 0.1); border: none; cursor: pointer;">
                                    <x-icon name="send" style="width: 20px; height: 20px; color: #2563eb;" />
                                </button>
                            </form>
                            <span class="action-label">Enviar</span>
                        </div>



                        @if($evento->status !== 'Finalizado')
                            <div class="action-item">
                                <form action="{{ route('events.status.update', $evento->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status_manual" value="Finalizado">
                                    <button type="submit" class="btn-admin-action" title="Finalizar" style="color: #ffffff; background: #6b7280; border: none; cursor: pointer;" onsubmit="return confirm('¿Estás seguro de finalizar este evento? Ya no se permitirán más registros.');">
                                        <x-icon name="flag" style="width: 20px; height: 20px; color: #ffffff;" />
                                    </button>
                                </form>
                                <span class="action-label">Fin</span>
                            </div>
                        @endif
                    </div>

                    <!-- Admin Team List Section -->
                    <div id="teams-{{ $evento->id }}" style="display: none; margin-top: 15px; border-top: 1px solid #e5e7eb; padding-top: 10px;">
                        <h4 style="font-size: 0.9rem; color: #4b5563; margin-bottom: 10px;">Equipos Registrados ({{ $evento->teams->count() }}/{{ $evento->capacity }})</h4>
                        @if($evento->teams->count() > 0)
                            <ul style="list-style: none; padding: 0; margin: 0; max-height: 150px; overflow-y: auto;">
                                @foreach($evento->teams as $team)
                                    <li style="display: flex; justify-content: space-between; align-items: center; padding: 5px 0; border-bottom: 1px dashed #e5e7eb; font-size: 0.85rem;">
                                        <div>
                                            <span style="font-weight: 600; color: #1f2937;">{{ $team->name }}</span>
                                            <span style="color: #6b7280; font-size: 0.75rem;">({{ $team->participants->count() }} miembros)</span>
                                        </div>
                                        <div style="display: flex; gap: 5px;">
                                            <!-- Edit Team (Redirect to Team Management) -->
                                            @if(auth()->check() && auth()->user()->participant && auth()->user()->participant->team_id == $team->id && auth()->user()->participant->rol == 'Líder')
                                            <a href="{{ route('teams.index') }}" style="color: #4f46e5; text-decoration: none;" title="Gestionar en Equipos">
                                                <x-icon name="open_in_new" style="font-size: 16px;" />
                                            </a>
                                            @endif
                                            <!-- Delete Team -->
                                            <form action="{{ route('teams.destroy', $team->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar equipo {{ $team->name }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="background: none; border: none; cursor: pointer; color: #ef4444; padding: 0;" title="Eliminar Equipo">
                                                    <x-icon name="delete" style="font-size: 16px;" />
                                                </button>
                                            </form>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p style="font-size: 0.85rem; color: #9ca3af; font-style: italic;">No hay equipos registrados.</p>
                        @endif
                    </div>
                @endrole
            </div>
        @endforeach
    </div>

    <div style="margin-top: 20px; display: flex; justify-content: center;">
        {{ $eventos->links() }}
    </div>

    <div class="contacto-seccion">
        <h2>¿No encuentras tu evento?</h2>
        <p>Contáctanos para agregar tu evento de innovación y tecnología</p>
        <a href="#" class="btn-contacto">Contactar Organizadores</a>
    </div>

    <!-- Modal de Registro de Equipo -->
    <div id="modal-registro" class="modal">
        <div class="modal-content">
            <span class="close-modal" id="close-registro">&times;</span>
            <h2>Registrar Equipo</h2>
            <p id="modal-evento-nombre"></p>

            <form action="{{ route('teams.store') }}" method="POST" id="form-registro-equipo">
                @csrf
                <input type="hidden" name="event_id" id="modal-evento-id">

                <div class="form-group">
                    <label for="seleccion-equipo">Seleccionar Equipo</label>
                    <select id="seleccion-equipo" name="seleccion_equipo" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #d1d5db; margin-bottom: 15px;">
                        @if(isset($equipo))
                            <option value="existing" data-name="{{ $equipo->name }}">{{ $equipo->name }} (Tu equipo actual)</option>
                        @endif
                        <option value="new" {{ !isset($equipo) ? 'selected' : '' }}>Crear nuevo equipo</option>
                    </select>
                </div>

                <div class="form-group" id="group-nombre-equipo" style="{{ isset($equipo) ? 'display: none;' : '' }}">
                    <label for="nombre-equipo">Nombre del Nuevo Equipo</label>
                    <input type="text" id="nombre-equipo" name="nombre" placeholder="Ej. Los Innovadores">
                </div>

                <button type="submit" class="btn-confirmar" id="btn-submit-equipo">
                    {{ isset($equipo) ? 'Ver mi equipo' : 'Crear Equipo' }}
                </button>
            </form>
        </div>
    </div>

    <!-- Modal de Detalles del Evento -->
    <div id="modal-detalles" class="modal">
        <div class="modal-content">
            <span class="close-modal" id="close-detalles">&times;</span>
            <h2 id="detalles-nombre"></h2>
            <p id="detalles-descripcion" style="margin-bottom: 20px; line-height: 1.6;"></p>

            <div class="detalle-item" style="margin-bottom: 10px;">
                <strong>Fecha:</strong> <span id="detalles-fecha"></span>
            </div>
            <div class="detalle-item" style="margin-bottom: 10px;">
                <strong>Ubicación:</strong> <span id="detalles-ubicacion"></span>
            </div>
            <div class="detalle-item" style="margin-bottom: 20px;">
                <strong>Capacidad:</strong> <span id="detalles-capacidad"></span>
            </div>
        </div>
    </div>

    <!-- Modal Crear/Editar Evento -->
    <div id="modal-evento" class="modal">
        <div class="modal-content profile-modal-content" style="background-color: white !important;">
            <div class="modal-header">
                <h2 id="modal-evento-titulo">Crear Evento</h2>
                <span class="close-modal" id="close-evento">&times;</span>
            </div>
            <form action="{{ route('events.store') }}" method="POST" id="form-evento" class="profile-form" enctype="multipart/form-data">
                @csrf
                <div id="method-spoofing"></div> <!-- For PUT method -->

                <div class="form-group">
                    <label for="evento-nombre">Nombre del Evento</label>
                    <div class="input-with-icon">
                        <x-icon name="label" />
                        <input type="text" id="evento-nombre" name="nombre" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="evento-image">Imagen de Portada (Opcional)</label>
                    <div class="input-with-icon">
                        <x-icon name="image" />
                        <input type="file" id="evento-image" name="image" accept="image/*" style="padding: 8px;">
                    </div>
                </div>
                <div class="form-group">
                    <label for="evento-descripcion">Descripción</label>
                    <textarea id="evento-descripcion" name="descripcion" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;"></textarea>
                </div>
                <div class="form-group">
                    <label for="evento-fecha-inicio">Fecha Inicio</label>
                    <div class="input-with-icon">
                        <x-icon name="date_range" />
                        <input type="date" id="evento-fecha-inicio" name="fecha_inicio" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="evento-start-time">Hora de Inicio (Oaxaca)</label>
                    <div class="input-with-icon">
                        <x-icon name="schedule" />
                        <input type="time" id="evento-start-time" name="start_time" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="evento-fecha-fin">Fecha Fin</label>
                    <div class="input-with-icon">
                        <x-icon name="date_range" />
                        <input type="date" id="evento-fecha-fin" name="fecha_fin" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="evento-ubicacion">Ubicación</label>
                    <div class="input-with-icon">
                        <x-icon name="place" />
                        <input type="text" id="evento-ubicacion" name="ubicacion" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="evento-capacidad">Capacidad</label>
                    <div class="input-with-icon">
                        <x-icon name="group" />
                        <input type="number" id="evento-capacidad" name="capacidad" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="evento-status-manual">Estado Manual (Opcional)</label>
                    <div class="input-with-icon">
                        <x-icon name="toggle_on" />
                        <select id="evento-status-manual" name="status_manual" style="width: 100%; padding: 12px 12px 12px 40px; border: 1px solid #e5e7eb; background-color: #f9fafb; border-radius: 8px; font-size: 15px; outline: none; color: #1f2937;">
                            <option value="">Automático (Basado en fechas)</option>
                            <option value="Próximo">Próximo</option>
                            <option value="En Curso">En Curso</option>
                            <option value="Finalizado">Finalizado</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Categorías</label>
                    <div style="max-height: 150px; overflow-y: auto; border: 1px solid #e5e7eb; padding: 10px; border-radius: 8px; background-color: #f9fafb;">
                        @php
                            $allCategories = \App\Models\Categoria::all();
                        @endphp
                        @if($allCategories->count() > 0)
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 8px;">
                                @foreach($allCategories as $cat)
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <input type="checkbox" id="cat-{{ $cat->id }}" name="categorias[]" value="{{ $cat->name }}" style="width: 14px; height: 14px; cursor: pointer;">
                                        <label for="cat-{{ $cat->id }}" style="font-size: 0.85rem; font-weight: normal; cursor: pointer; margin: 0; line-height: 1.2;">{{ $cat->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p style="color: #6b7280; font-size: 0.9rem; font-style: italic;">No hay categorías registradas. Agrega una abajo.</p>
                        @endif
                    </div>
                    <div class="input-with-icon" style="margin-top: 10px;">
                        <x-icon name="add_circle" />
                        <input type="text" name="categorias[]" placeholder="Escribe para agregar nueva categoría...">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Rúbrica de Evaluación (Criterios)</label>
                    <div id="criteria-container" style="border: 1px solid #e5e7eb; padding: 10px; border-radius: 8px; background-color: #f9fafb;">
                        <!-- Criteria rows will be added here -->
                    </div>
                    <button type="button" id="btn-add-criterion" style="margin-top: 10px; background-color: #10b981; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 0.85rem;">
                        + Agregar Criterio
                    </button>
                </div>

                <button type="submit" class="btn-confirmar">Guardar Evento</button>
            </form>
        </div>
    </div>

    <!-- Modal Gestionar Jueces -->
    <div id="modal-view-judges" class="modal">
        <div class="modal-content profile-modal-content" style="max-width: 600px !important;">
            <div class="modal-header">
                <h2>Gestionar Jueces</h2>
                <span class="close-modal" id="close-view-judges">&times;</span>
            </div>
            
            <!-- Asignar Nuevo Juez -->
            <div style="background-color: #f3f4f6; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <h3 style="font-size: 1rem; margin-bottom: 10px; color: #374151;">Asignar Nuevo Juez</h3>
                <form id="form-assign-judge-internal" method="POST" class="profile-form">
                    @csrf
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <div style="flex: 1; position: relative;">
                            <x-icon name="hammer" style="position: absolute; left: 10px; top: 10px; color: #6b7280;" />
                            <select id="judge-select-internal" name="user_id" style="width: 100%; padding: 10px 10px 10px 35px; border: 1px solid #d1d5db; background-color: white; border-radius: 6px; outline: none;" required>
                                <option value="">Seleccione un juez...</option>
                                @foreach(\App\Models\User::role('juez')->get() as $juez)
                                    <option value="{{ $juez->id }}">{{ $juez->name }} ({{ $juez->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" style="background-color: #10b981; color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; font-weight: 500;">
                            Asignar
                        </button>
                    </div>
                </form>
            </div>

            <h3 style="font-size: 1rem; margin-bottom: 10px; color: #374151; border-top: 1px solid #e5e7eb; padding-top: 15px;">Jueces Asignados</h3>
            <div id="judges-list-container" style="max-height: 300px; overflow-y: auto;">
                <!-- Judges will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        function toggleTeams(id) {
            const element = document.getElementById(id);
            if (element.style.display === 'none') {
                element.style.display = 'block';
            } else {
                element.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // ... (Existing Modal Logic) ...
            // Modal Registro Logic
            const modalRegistro = document.getElementById('modal-registro');
            const btnsRegistro = document.querySelectorAll('.btn-registrar');
            const closeRegistro = document.getElementById('close-registro');
            const eventoIdInput = document.getElementById('modal-evento-id');
            const eventoNombreDisplay = document.getElementById('modal-evento-nombre');

            const selectEquipo = document.getElementById('seleccion-equipo');
            const groupNombre = document.getElementById('group-nombre-equipo');
            const inputNombre = document.getElementById('nombre-equipo');
            const btnSubmit = document.getElementById('btn-submit-equipo');
            const form = document.getElementById('form-registro-equipo');

            btnsRegistro.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const eventoId = this.getAttribute('data-id');
                    const eventoNombre = this.getAttribute('data-name');

                    eventoIdInput.value = eventoId;
                    eventoNombreDisplay.textContent = 'Evento: ' + eventoNombre;

                    modalRegistro.style.display = 'flex';
                });
            });

            closeRegistro.onclick = function() {
                modalRegistro.style.display = 'none';
            }

            // Modal Detalles Logic
            const modalDetalles = document.getElementById('modal-detalles');
            const btnsDetalles = document.querySelectorAll('.btn-detalles');
            const closeDetalles = document.getElementById('close-detalles');

            const detNombre = document.getElementById('detalles-nombre');
            const detDesc = document.getElementById('detalles-descripcion');
            const detFecha = document.getElementById('detalles-fecha');
            const detUbicacion = document.getElementById('detalles-ubicacion');
            const detCapacidad = document.getElementById('detalles-capacidad');

            btnsDetalles.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    detNombre.textContent = this.getAttribute('data-name');
                    detDesc.textContent = this.getAttribute('data-description');
                    detFecha.textContent = this.getAttribute('data-starts-at') + ' - ' + this.getAttribute('data-ends-at');
                    detUbicacion.textContent = this.getAttribute('data-location');
                    detCapacidad.textContent = this.getAttribute('data-capacity') + ' personas';

                    modalDetalles.style.display = 'flex';
                });
            });

            closeDetalles.onclick = function() {
                modalDetalles.style.display = 'none';
            }

            // Modal Evento (Create/Edit) Logic
            const modalEvento = document.getElementById('modal-evento');
            const btnCrearEvento = document.getElementById('btn-crear-evento');
            const btnsEditarEvento = document.querySelectorAll('.btn-editar-evento');
            const closeEvento = document.getElementById('close-evento');
            const formEvento = document.getElementById('form-evento');
            const modalEventoTitulo = document.getElementById('modal-evento-titulo');
            const methodSpoofing = document.getElementById('method-spoofing');

            // Inputs
            const inputEventoNombre = document.getElementById('evento-nombre');
            const inputEventoDesc = document.getElementById('evento-descripcion');
            const inputEventoInicio = document.getElementById('evento-fecha-inicio');
            const inputEventoFin = document.getElementById('evento-fecha-fin');
            const inputEventoUbicacion = document.getElementById('evento-ubicacion');
            const inputEventoCapacidad = document.getElementById('evento-capacidad');
            const criteriaContainer = document.getElementById('criteria-container');
            const btnAddCriterion = document.getElementById('btn-add-criterion');

            function addCriterionRow(id = null, name = '', maxScore = 10, description = '') {
                const index = criteriaContainer.children.length;
                const div = document.createElement('div');
                div.style.display = 'flex';
                div.style.gap = '10px';
                div.style.marginBottom = '10px';
                div.style.alignItems = 'start';
                
                let html = `
                    <div style="flex: 2;">
                        <input type="text" name="criteria[${index}][name]" value="${name}" placeholder="Nombre Criterio" required style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                        <input type="text" name="criteria[${index}][description]" value="${description}" placeholder="Descripción (opcional)" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px; margin-top: 4px; font-size: 0.8rem;">
                    </div>
                    <div style="flex: 1;">
                        <input type="number" name="criteria[${index}][max_score]" value="${maxScore}" placeholder="Max" required style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                    </div>
                `;

                if (id) {
                    html += `<input type="hidden" name="criteria[${index}][id]" value="${id}">`;
                }

                html += `
                    <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; color: #ef4444; cursor: pointer; padding-top: 10px;">
                        <span class="material-icons">delete</span>
                    </button>
                `;

                div.innerHTML = html;
                criteriaContainer.appendChild(div);
            }

            if (btnAddCriterion) {
                btnAddCriterion.addEventListener('click', function() {
                    addCriterionRow();
                });
            }

            if(btnCrearEvento) {
                btnCrearEvento.addEventListener('click', function() {
                    modalEventoTitulo.textContent = 'Crear Evento';
                    formEvento.action = "{{ route('events.store') }}";
                    methodSpoofing.innerHTML = ''; // Clear PUT method
                    formEvento.reset();
                    criteriaContainer.innerHTML = '';
                    // Add default criteria
                    addCriterionRow(null, 'Innovación', 10, 'Originalidad y creatividad');
                    addCriterionRow(null, 'Impacto Social', 10, 'Beneficio a la comunidad');
                    addCriterionRow(null, 'Viabilidad Técnica', 10, 'Factibilidad de implementación');
                    modalEvento.style.display = 'flex';
                });
            }

            btnsEditarEvento.forEach(btn => {
                btn.addEventListener('click', function() {
                    modalEventoTitulo.textContent = 'Editar Evento';
                    const id = this.getAttribute('data-id');
                    formEvento.action = "/events/" + id;
                    methodSpoofing.innerHTML = '@method("PUT")';

                    inputEventoNombre.value = this.getAttribute('data-name');
                    inputEventoDesc.value = this.getAttribute('data-description');
                    inputEventoInicio.value = this.getAttribute('data-starts-at');
                    document.getElementById('evento-start-time').value = this.getAttribute('data-start-time');
                    inputEventoFin.value = this.getAttribute('data-ends-at');
                    inputEventoUbicacion.value = this.getAttribute('data-location');
                    inputEventoCapacidad.value = this.getAttribute('data-capacity');

                    // Status Manual
                    const statusManual = this.getAttribute('data-status-manual');
                    const selectStatus = document.getElementById('evento-status-manual');
                    selectStatus.value = statusManual || "";

                    // Categories
                    const categoriasJson = this.getAttribute('data-categorias');
                    
                    // Criteria
                    const criteriaJson = this.getAttribute('data-criteria');
                    criteriaContainer.innerHTML = '';
                    try {
                        const criteria = JSON.parse(criteriaJson);
                        if (Array.isArray(criteria)) {
                            criteria.forEach(c => {
                                addCriterionRow(c.id, c.name, c.max_score, c.description);
                            });
                        }
                    } catch(e) {
                        console.error("Error parsing criteria", e);
                    }

                    let categorias = [];
                    try {
                        categorias = JSON.parse(categoriasJson);
                    } catch(e) {
                        console.error("Error parsing categories", e);
                    }

                    // Uncheck all first
                    document.querySelectorAll('input[name="categorias[]"]').forEach(cb => {
                        if(cb.type === 'checkbox') cb.checked = false;
                        if(cb.type === 'text') cb.value = '';
                    });

                    // Check existing
                    if (Array.isArray(categorias)) {
                        categorias.forEach(catName => {
                            // Find checkbox with value == catName
                            const cb = document.querySelector(`input[name="categorias[]"][value="${catName}"]`);
                            if(cb) cb.checked = true;
                        });
                    }

                    modalEvento.style.display = 'flex';
                });
            });

            if(closeEvento) {
                closeEvento.addEventListener('click', function() {
                    modalEvento.style.display = 'none';
                });
            }

            // View Judges Modal Logic (Consolidated)
            const modalViewJudges = document.getElementById('modal-view-judges');
            const btnsViewJudges = document.querySelectorAll('.btn-view-judges');
            const closeViewJudges = document.getElementById('close-view-judges');
            const judgesListContainer = document.getElementById('judges-list-container');
            const formAssignJudgeInternal = document.getElementById('form-assign-judge-internal');

            btnsViewJudges.forEach(btn => {
                btn.addEventListener('click', function() {
                    const eventId = this.getAttribute('data-id');
                    const judgesJson = this.getAttribute('data-judges');
                    
                    // Set form action for assigning new judge
                    formAssignJudgeInternal.action = "/events/" + eventId + "/judges";

                    let judges = [];
                    try {
                        judges = JSON.parse(judgesJson);
                    } catch(e) {
                        console.error("Error parsing judges", e);
                    }

                    renderJudgesList(judges, eventId);
                    modalViewJudges.style.display = 'flex';
                });
            });

            function renderJudgesList(judges, eventId) {
                judgesListContainer.innerHTML = '';
                if (judges.length === 0) {
                    judgesListContainer.innerHTML = '<p style="text-align: center; color: #6b7280; font-style: italic; padding: 20px;">No hay jueces asignados a este evento.</p>';
                } else {
                    const ul = document.createElement('ul');
                    ul.style.listStyle = 'none';
                    ul.style.padding = '0';
                    ul.style.margin = '0';

                    judges.forEach(juez => {
                        const li = document.createElement('li');
                        li.style.display = 'flex';
                        li.style.justifyContent = 'space-between';
                        li.style.alignItems = 'center';
                        li.style.padding = '10px';
                        li.style.borderBottom = '1px solid #f3f4f6';

                        li.innerHTML = `
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 30px; height: 30px; background-color: #e0e7ff; color: #4338ca; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.8rem;">
                                    ${juez.name.charAt(0).toUpperCase()}
                                </div>
                                <div>
                                    <span style="font-weight: 600; display: block; color: #1f2937; font-size: 0.9rem;">${juez.name}</span>
                                    <span style="font-size: 0.75rem; color: #6b7280;">${juez.email}</span>
                                </div>
                            </div>
                            <form action="/events/${eventId}/judges/${juez.id}" method="POST" onsubmit="return confirm('¿Quitar a este juez del evento?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Quitar Juez" style="background: none; border: none; cursor: pointer; color: #ef4444; padding: 5px; opacity: 0.7; transition: opacity 0.2s;">
                                    <x-icon name="delete" />
                                </button>
                            </form>
                        `;
                        ul.appendChild(li);
                    });
                    judgesListContainer.appendChild(ul);
                }
            }

            if (closeViewJudges) {
                closeViewJudges.addEventListener('click', function() {
                    modalViewJudges.style.display = 'none';
                });
            }

            window.addEventListener('click', function(event) {
                if (event.target == modalViewJudges) {
                    modalViewJudges.style.display = 'none';
                }
            });

            // Global Window Click
            window.onclick = function(event) {
                if (event.target == modalRegistro) {
                    modalRegistro.style.display = 'none';
                }
                if (event.target == modalDetalles) {
                    modalDetalles.style.display = 'none';
                }
                if (event.target == modalEvento) {
                    modalEvento.style.display = 'none';
                }
                if (event.target == modalAssignJudge) {
                    modalAssignJudge.style.display = 'none';
                }
                if (event.target == modalAnnouncement) {
                    modalAnnouncement.style.display = 'none';
                }
            }



            // Handle dropdown change (Existing)
            if(selectEquipo) {
                selectEquipo.addEventListener('change', function() {
                    if (this.value === 'new') {
                        groupNombre.style.display = 'block';
                        inputNombre.required = true;
                        btnSubmit.textContent = 'Crear Equipo';
                        form.action = "{{ route('teams.store') }}";
                    } else {
                        groupNombre.style.display = 'none';
                        inputNombre.required = false;
                        btnSubmit.textContent = 'Ver mi equipo';
                    }
                });

                form.addEventListener('submit', function(e) {
                    if (selectEquipo.value === 'existing') {
                        e.preventDefault();
                        window.location.href = "{{ route('teams.index') }}";
                    }
                });
            }
        });

    </script>
@endsection
