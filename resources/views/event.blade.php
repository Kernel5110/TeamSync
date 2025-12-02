@extends('layouts.app')

@section('title', 'Eventos - TeamSync')

@push('styles')
    @vite(['resources/css/eventos.css'])
@endpush

@section('content')
    <div class="contenedor-eventos">
        @if(session('success'))
            <div style="width: 100%; background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border: 1px solid #a7f3d0;">
                {{ session('success') }}
            </div>
        @endif

        @can('create events')
            <div style="width: 100%; display: flex; justify-content: flex-end; margin-bottom: 20px;">
                <button id="btn-crear-evento" class="btn-confirmar" style="width: auto; padding: 10px 20px;">
                    <span class="material-icons" style="vertical-align: middle; margin-right: 5px;"></span> Crear Evento
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
                    <span class="badge-proximo">Próximo</span>
                </div>

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
                        <span>{{ $evento->capacidad }}</span>
                    </div>
                </div>

                <div class="evento-acciones">
                    <a href="#" class="btn-registrar" data-id="{{ $evento->id }}" data-nombre="{{ $evento->nombre }}">Registrar Equipo</a>
                    <a href="#" class="btn-detalles" 
                       data-nombre="{{ $evento->nombre }}"
                       data-descripcion="{{ $evento->descripcion }}"
                       data-fecha-inicio="{{ $evento->fecha_inicio->format('d F Y') }}"
                       data-fecha-fin="{{ $evento->fecha_fin->format('d F Y') }}"
                       data-ubicacion="{{ $evento->ubicacion }}"
                       data-capacidad="{{ $evento->capacidad }}">
                        Ver Detalles
                    </a>
                    
                    @can('edit events')
                        <button class="btn-editar-evento" 
                                data-id="{{ $evento->id }}"
                                data-nombre="{{ $evento->nombre }}"
                                data-descripcion="{{ $evento->descripcion }}"
                                data-fecha-inicio="{{ $evento->fecha_inicio->format('Y-m-d') }}"
                                data-fecha-fin="{{ $evento->fecha_fin->format('Y-m-d') }}"
                                data-ubicacion="{{ $evento->ubicacion }}"
                                data-capacidad="{{ $evento->capacidad }}"
                                style="background: none; border: none; cursor: pointer; color: #4f46e5;">
                            <span class="material-icons">edit</span>
                        </button>
                    @endcan
                    
                    @can('delete events')
                        <form action="{{ route('event.delete', $evento->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de eliminar este evento?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: none; border: none; cursor: pointer; color: #ef4444;">
                                <span class="material-icons">delete</span>
                            </button>
                        </form>
                    @endcan
                </div>
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
        <div class="modal-content profile-modal-content">
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
                        <span class="material-icons"></span>
                        <input type="number" id="evento-capacidad" name="capacidad" required>
                    </div>
                </div>
                <button type="submit" class="btn-confirmar">Guardar Evento</button>
            </form>
        </div>
    </div>

    <script>
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
                    inputEventoCapacidad.value = this.getAttribute('data-capacidad');

                    modalEvento.style.display = 'flex';
                });
            });

            if(closeEvento) {
                closeEvento.addEventListener('click', function() {
                    modalEvento.style.display = 'none';
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
