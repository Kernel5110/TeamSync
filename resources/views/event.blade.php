@extends('layouts.app')

@section('title', 'Eventos - TeamSync')



@section('content')
    <div class="contenedor-eventos">
        @if(session('success'))
            <div style="width: 100%; background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border: 1px solid #a7f3d0;">
                {{ session('success') }}
            </div>
        @endif



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
        </style>

        @can('create events')
            <div style="position: absolute; top: 100px; right: 20px; z-index: 100;">
                <button id="btn-crear-evento" class="btn-confirmar" style="width: auto; padding: 8px 15px; font-size: 0.9rem;">
                    <span class="material-icons" style="vertical-align: middle; margin-right: 5px; font-size: 1.1rem;">add</span> Crear Evento
                </button>
            </div>
        @endcan

        @foreach($eventos as $evento)
            <div class="tarjeta-evento">
                <div class="evento-header">
                    <div class="evento-titulo">
                        <div class="icono-evento {{ $loop->even ? 'icono-innovatec' : 'icono-hackatec' }}">
                            @if($loop->even)
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a10 10 0 1 0 10 10 10 10 0 0 0-10-10zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z"></path><path d="M12 6v6l4 2"></path></svg>
                            @else
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path><path d="M4 22h16"></path><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"></path></svg>
                            @endif
                        </div>
                        <h2>{{ $evento->nombre }}</h2>
                    </div>
                    @php
                        $status = $evento->status;
                        $badgeColor = match($status) {
                            'En Curso' => '#10b981', // Green
                            'Próximo' => '#3b82f6', // Blue
                            'Finalizado' => '#6b7280', // Gray
                            default => '#3b82f6'
                        };
                    @endphp
                    <span class="badge-proximo" style="background-color: {{ $badgeColor }};">{{ $status }}</span>
                </div>

                @if($evento->categoria)
                    <div style="margin-bottom: 10px;">
                        <span style="background-color: #e0e7ff; color: #4338ca; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">
                            {{ $evento->categoria }}
                        </span>
                    </div>
                @endif

                <p class="evento-descripcion">
                    {{ $evento->descripcion }}
                </p>

                <div class="evento-detalles">
                    <div class="detalle-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        <span>{{ $evento->fecha_inicio->format('d') }}-{{ $evento->fecha_fin->format('d F Y') }}</span>
                    </div>
                    <div class="detalle-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        <span>{{ $evento->ubicacion }}</span>
                    </div>
                    <div class="detalle-item">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
    <span>{{ $evento->equipos->count() }}/{{ $evento->capacidad }} </span>
</div>
                </div>

                <div class="evento-acciones">

                    <a href="#" class="btn-detalles"
                       data-nombre="{{ $evento->nombre }}"
                       data-descripcion="{{ $evento->descripcion }}"
                       data-fecha-inicio="{{ $evento->fecha_inicio->format('d F Y') }}"
                       data-fecha-fin="{{ $evento->fecha_fin->format('d F Y') }}"
                       data-ubicacion="{{ $evento->ubicacion }}"
                       data-capacidad="{{ $evento->capacidad }}">
                        Ver Detalles
                    </a>

                    <a href="{{ route('event.ranking', $evento->id) }}" class="btn-ranking" style="background-color: #f59e0b; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 500; font-size: 0.9rem; transition: background-color 0.2s; display: inline-flex; align-items: center; gap: 5px;">
                        <span class="material-icons" style="font-size: 18px;">emoji_events</span> Ranking
                    </a>

          {{-- 1. ABRE UN SOLO BLOQUE DE AUTENTICACIÓN. --}}
@auth

    @php
        // Lógica del Juez (se ejecuta si el usuario está logueado)
        $is_judge_or_assigned = Auth::user()->hasRole('juez') || $evento->jueces->contains(Auth::id());
    @endphp

    {{-- 2. VERIFICA SI EL USUARIO ES JUEZ O ESTÁ ASIGNADO --}}
    @if ($is_judge_or_assigned)

        {{-- BLOQUE JUEZ: Muestra el botón EVALUAR --}}
        <a href="{{ route('event.evaluate', $evento->id) }}" class="btn-participar" style="background-color: #8b5cf6; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 500; font-size: 0.9rem; transition: background-color 0.2s;">
            Evaluar
        </a>

    {{-- 3. SI NO ES JUEZ, ENTRA AQUÍ (@else es el alternativo del @if anterior) --}}
    @else

        {{-- BLOQUE PARTICIPANTE/OTRO ROL: Lógica original para ver si puede participar --}}
        @unlessrole('admin')
            @php
                $userParticipante = auth()->user()->participante;
                $userTeamId = $userParticipante ? $userParticipante->equipo_id : null;

                $hasTeamInEvent = \App\Models\Equipo::where('evento_id', $evento->id)
                    ->whereHas('participantes', function($q) {
                        $q->where('usuario_id', auth()->id());
                    })->exists();
            @endphp

            @if($hasTeamInEvent)
                <a href="{{ route('participation.show', $evento->id) }}" class="btn-ver-proyecto" style="background-color: #10b981; color: white; padding: 0.75rem; border-radius: 9999px; text-decoration: none; font-weight: 600; font-size: 1rem; transition: background-color 0.2s; display: inline-block; text-align: center;">
                    Ver Proyecto
                </a>
            @else
                @if($evento->status !== 'Finalizado')
                    <button class="btn-registrar" data-id="{{ $evento->id }}" data-nombre="{{ $evento->nombre }}" style="background-color: #4f46e5; color: white; padding: 0.75rem; border-radius: 9999px; text-decoration: none; font-weight: 600; font-size: 1rem; transition: background-color 0.2s; border: none; cursor: pointer;">
                        Participar
                    </button>
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
                    <div class="admin-actions">
                        <button class="btn-admin-action judge btn-assign-judge" data-id="{{ $evento->id }}" title="Asignar Juez">
                            <span class="material-icons">gavel</span>
                        </button>

                        <button class="btn-admin-action edit btn-editar-evento"
                                data-id="{{ $evento->id }}"
                                data-nombre="{{ $evento->nombre }}"
                                data-descripcion="{{ $evento->descripcion }}"
                                data-fecha-inicio="{{ $evento->fecha_inicio->format('Y-m-d') }}"
                                data-fecha-fin="{{ $evento->fecha_fin->format('Y-m-d') }}"
                                data-ubicacion="{{ $evento->ubicacion }}"
                                data-capacidad="{{ $evento->capacidad }}"
                                data-categoria="{{ $evento->categoria }}"
                                title="Editar Evento">
                            <span class="material-icons">edit</span>
                        </button>

                        <button class="btn-admin-action view-teams btn-ver-equipos"
                                onclick="toggleTeams('teams-{{ $evento->id }}')"
                                title="Ver Equipos Registrados">
                            <span class="material-icons">groups</span>
                        </button>

                        <form action="{{ route('event.delete', $evento->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de eliminar este evento?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-admin-action delete" title="Eliminar Evento">
                                <span class="material-icons">delete</span>
                            </button>
                        </form>
                    </div>

                    <!-- Admin Team List Section -->
                    <div id="teams-{{ $evento->id }}" style="display: none; margin-top: 15px; border-top: 1px solid #e5e7eb; padding-top: 10px;">
                        <h4 style="font-size: 0.9rem; color: #4b5563; margin-bottom: 10px;">Equipos Registrados ({{ $evento->equipos->count() }}/{{ $evento->capacidad }})</h4>
                        @if($evento->equipos->count() > 0)
                            <ul style="list-style: none; padding: 0; margin: 0; max-height: 150px; overflow-y: auto;">
                                @foreach($evento->equipos as $team)
                                    <li style="display: flex; justify-content: space-between; align-items: center; padding: 5px 0; border-bottom: 1px dashed #e5e7eb; font-size: 0.85rem;">
                                        <div>
                                            <span style="font-weight: 600; color: #1f2937;">{{ $team->nombre }}</span>
                                            <span style="color: #6b7280; font-size: 0.75rem;">({{ $team->participantes->count() }} miembros)</span>
                                        </div>
                                        <div style="display: flex; gap: 5px;">
                                            <!-- Edit Team (Redirect to Team Management) -->
                                            <a href="{{ route('team') }}" style="color: #4f46e5; text-decoration: none;" title="Gestionar en Equipos">
                                                <span class="material-icons" style="font-size: 16px;">open_in_new</span>
                                            </a>
                                            <!-- Delete Team -->
                                            <form action="{{ route('team.destroy', $team->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar equipo {{ $team->nombre }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="background: none; border: none; cursor: pointer; color: #ef4444; padding: 0;" title="Eliminar Equipo">
                                                    <span class="material-icons" style="font-size: 16px;">delete</span>
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

            <form action="{{ route('team.store') }}" method="POST" id="form-registro-equipo">
                @csrf
                <input type="hidden" name="evento_id" id="modal-evento-id">

                <div class="form-group">
                    <label for="seleccion-equipo">Seleccionar Equipo</label>
                    <select id="seleccion-equipo" name="seleccion_equipo" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #d1d5db; margin-bottom: 15px;">
                        @if(isset($equipo))
                            <option value="existing" data-nombre="{{ $equipo->nombre }}">{{ $equipo->nombre }} (Tu equipo actual)</option>
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
            <form action="{{ route('event.store') }}" method="POST" id="form-evento" class="profile-form">
                @csrf
                <div id="method-spoofing"></div> <!-- For PUT method -->

                <div class="form-group">
                    <label for="evento-nombre">Nombre del Evento</label>
                    <div class="input-with-icon">
                        <span class="material-icons"></span>
                        <input type="text" id="evento-nombre" name="nombre" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="evento-descripcion">Descripción</label>
                    <textarea id="evento-descripcion" name="descripcion" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd;"></textarea>
                </div>
                <div class="form-group">
                    <label for="evento-fecha-inicio">Fecha Inicio</label>
                    <div class="input-with-icon">
                        <span class="material-icons"></span>
                        <input type="date" id="evento-fecha-inicio" name="fecha_inicio" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="evento-fecha-fin">Fecha Fin</label>
                    <div class="input-with-icon">
                        <span class="material-icons"></span>
                        <input type="date" id="evento-fecha-fin" name="fecha_fin" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="evento-ubicacion">Ubicación</label>
                    <div class="input-with-icon">
                        <span class="material-icons"></span>
                        <input type="text" id="evento-ubicacion" name="ubicacion" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="evento-capacidad">Capacidad</label>
                    <div class="input-with-icon">
                        <span class="material-icons">group</span>
                        <input type="number" id="evento-capacidad" name="capacidad" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="evento-categoria">Categoría</label>
                    <div class="input-with-icon">
                        <span class="material-icons">category</span>
                        <select id="evento-categoria-select" name="categoria_select" style="width: 100%; padding: 12px 12px 12px 40px; border: 1px solid #e5e7eb; background-color: #f9fafb; border-radius: 8px; font-size: 15px; outline: none; color: #1f2937;">
                            <option value="">Seleccionar Categoría</option>
                            <option value="Fintech">Fintech</option>
                            <option value="Healthtech">Healthtech</option>
                            <option value="Edtech">Edtech</option>
                            <option value="Agrotech">Agrotech</option>
                            <option value="Cybersecurity">Cybersecurity</option>
                            <option value="AI & Machine Learning">AI & Machine Learning</option>
                            <option value="Blockchain">Blockchain</option>
                            <option value="IoT">IoT</option>
                            <option value="Otro">Otro (Crear nueva)</option>
                        </select>
                    </div>
                    <div class="input-with-icon" id="container-nueva-categoria" style="display: none; margin-top: 10px;">
                        <span class="material-icons">add_circle</span>
                        <input type="text" id="evento-categoria-input" name="categoria_input" placeholder="Escribe la nueva categoría" disabled>
                    </div>
                    <!-- Hidden input to store the final value sent to backend -->
                    <input type="hidden" id="evento-categoria-final" name="categoria">
                </div>
                <button type="submit" class="btn-confirmar">Guardar Evento</button>
            </form>
        </div>
    </div>

    <!-- Modal Asignar Juez -->
    <div id="modal-assign-judge" class="modal">
        <div class="modal-content profile-modal-content" style="max-width: 500px !important;">
            <div class="modal-header">
                <h2>Asignar Juez</h2>
                <span class="close-modal" id="close-assign-judge">&times;</span>
            </div>
            <form id="form-assign-judge" method="POST" class="profile-form">
                @csrf
                <div class="form-group">
                    <label for="judge-select">Seleccionar Juez</label>
                    <div class="input-with-icon">
                        <span class="material-icons">hammer</span>
                       <select id="judge-select" name="user_id" style="/* ... estilos ... */" required>
    <option value="">Seleccione un juez...</option>
    @foreach(\App\Models\User::whereDoesntHave('roles', function($query) {
        $query->where('name', 'admin');
    })->get() as $user)
        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
    @endforeach
</select>
                    </div>
                </div>
                <button type="submit" class="btn-confirmar">Asignar Juez</button>
            </form>
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
                    const eventoNombre = this.getAttribute('data-nombre');

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
                    detNombre.textContent = this.getAttribute('data-nombre');
                    detDesc.textContent = this.getAttribute('data-descripcion');
                    detFecha.textContent = this.getAttribute('data-fecha-inicio') + ' - ' + this.getAttribute('data-fecha-fin');
                    detUbicacion.textContent = this.getAttribute('data-ubicacion');
                    detCapacidad.textContent = this.getAttribute('data-capacidad') + ' personas';

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

            if(btnCrearEvento) {
                btnCrearEvento.addEventListener('click', function() {
                    modalEventoTitulo.textContent = 'Crear Evento';
                    formEvento.action = "{{ route('event.store') }}";
                    methodSpoofing.innerHTML = ''; // Clear PUT method
                    formEvento.reset();
                    modalEvento.style.display = 'flex';
                });
            }

            btnsEditarEvento.forEach(btn => {
                btn.addEventListener('click', function() {
                    modalEventoTitulo.textContent = 'Editar Evento';
                    const id = this.getAttribute('data-id');
                    formEvento.action = "/event/" + id;
                    methodSpoofing.innerHTML = '@method("PUT")';

                    inputEventoNombre.value = this.getAttribute('data-nombre');
                    inputEventoDesc.value = this.getAttribute('data-descripcion');
                    inputEventoInicio.value = this.getAttribute('data-fecha-inicio');
                    inputEventoFin.value = this.getAttribute('data-fecha-fin');
                    inputEventoUbicacion.value = this.getAttribute('data-ubicacion');
                    inputEventoUbicacion.value = this.getAttribute('data-ubicacion');
                    inputEventoCapacidad.value = this.getAttribute('data-capacidad');

                    const categoria = this.getAttribute('data-categoria');
                    const selectCategoria = document.getElementById('evento-categoria-select');
                    const inputCategoria = document.getElementById('evento-categoria-input');
                    const containerNueva = document.getElementById('container-nueva-categoria');
                    const finalCategoria = document.getElementById('evento-categoria-final');

                    // Check if category is in the list
                    let found = false;
                    for (let i = 0; i < selectCategoria.options.length; i++) {
                        if (selectCategoria.options[i].value === categoria) {
                            selectCategoria.value = categoria;
                            found = true;
                            break;
                        }
                    }

                    if (!found && categoria) {
                        selectCategoria.value = 'Otro';
                        containerNueva.style.display = 'block';
                        inputCategoria.disabled = false;
                        inputCategoria.value = categoria;
                    } else {
                        selectCategoria.value = categoria || "";
                        containerNueva.style.display = 'none';
                        inputCategoria.disabled = true;
                        inputCategoria.value = '';
                    }

                    // Update hidden input
                    finalCategoria.value = categoria || "";

                    modalEvento.style.display = 'flex';
                });
            });

            // Category logic for Create/Edit
            const selectCategoria = document.getElementById('evento-categoria-select');
            const inputCategoria = document.getElementById('evento-categoria-input');
            const containerNueva = document.getElementById('container-nueva-categoria');
            const finalCategoria = document.getElementById('evento-categoria-final');

            if(selectCategoria) {
                selectCategoria.addEventListener('change', function() {
                    if(this.value === 'Otro') {
                        containerNueva.style.display = 'block';
                        inputCategoria.disabled = false;
                        inputCategoria.focus();
                        finalCategoria.value = inputCategoria.value;
                    } else {
                        containerNueva.style.display = 'none';
                        inputCategoria.disabled = true;
                        finalCategoria.value = this.value;
                    }
                });

                inputCategoria.addEventListener('input', function() {
                    finalCategoria.value = this.value;
                });

                // Also handle form submit to ensure value is set
                const formEvento = document.getElementById('form-evento');
                formEvento.addEventListener('submit', function() {
                    if(selectCategoria.value === 'Otro') {
                        finalCategoria.value = inputCategoria.value;
                    } else {
                        finalCategoria.value = selectCategoria.value;
                    }
                });
            }

            if(closeEvento) {
                closeEvento.addEventListener('click', function() {
                    modalEvento.style.display = 'none';
                });
            }

            // Assign Judge Modal Logic
            const modalAssignJudge = document.getElementById('modal-assign-judge');
            const btnsAssignJudge = document.querySelectorAll('.btn-assign-judge');
            const closeAssignJudge = document.getElementById('close-assign-judge');
            const formAssignJudge = document.getElementById('form-assign-judge');

            btnsAssignJudge.forEach(btn => {
                btn.addEventListener('click', function() {
                    const eventId = this.getAttribute('data-id');
                    formAssignJudge.action = "/evento/" + eventId + "/assign-judge";
                    modalAssignJudge.style.display = 'flex';
                });
            });

            if(closeAssignJudge) {
                closeAssignJudge.addEventListener('click', function() {
                    modalAssignJudge.style.display = 'none';
                });
            }

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
            }

            // Handle dropdown change (Existing)
            if(selectEquipo) {
                selectEquipo.addEventListener('change', function() {
                    if (this.value === 'new') {
                        groupNombre.style.display = 'block';
                        inputNombre.required = true;
                        btnSubmit.textContent = 'Crear Equipo';
                        form.action = "{{ route('team.store') }}";
                    } else {
                        groupNombre.style.display = 'none';
                        inputNombre.required = false;
                        btnSubmit.textContent = 'Ver mi equipo';
                    }
                });

                form.addEventListener('submit', function(e) {
                    if (selectEquipo.value === 'existing') {
                        e.preventDefault();
                        window.location.href = "{{ route('team') }}";
                    }
                });
            }
        });

    </script>
@endsection
