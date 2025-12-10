@extends('layouts.app')

@section('content')


    <div class="container">
        @if(session('success'))
            <div style="background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border: 1px solid #a7f3d0;">
                {{ session('success') }}
            </div>
        @endif

        <div class="profile-header">
            <div class="profile-avatar" style="{{ $user->profile_photo_path ? 'background-image: url(' . asset('storage/' . $user->profile_photo_path) . '); background-size: cover; background-position: center; color: transparent;' : '' }}">
                {{ $user->profile_photo_path ? '' : strtoupper(substr($user->name, 0, 2)) }}
            </div>

            <div class="profile-info">
                <h1>{{ $user->name }}</h1>
                <p>&lt;> {{ $user->participant->rol ?? 'Participante' }}</p>
                <p>{{ $user->participant->institution ?? 'Institución no especificada' }}</p>
                <div class="profile-badges">
                    <span class="profile-badge">Innovador</span>
                    <span class="profile-badge">Competidor</span>
                </div>
            </div>

            <div class="profile-actions">
                @role('admin')
                    <button id="btn-crear-juez" style="background-color: #8b5cf6; color: white;"><x-icon name="gavel" /> Crear Juez</button>
                @endrole
                <button id="btn-editar-perfil"><x-icon name="edit" /> Editar</button>
                <div class="settings-icon">
                    <x-icon name="settings" />
                </div>
            </div>
        </div>

        <div class="profile-stats-nav">
            <a href="#" class="nav-item active" data-tab="resumen">Resumen</a>
            @unlessrole('admin')
                @unlessrole('juez')
                    <a href="#" class="nav-item" data-tab="equipos">Equipos</a>
                @endunlessrole
                <a href="#" class="nav-item" data-tab="logros">Logros</a>
            @endunlessrole
            @role('admin')
                <a href="#" class="nav-item" data-tab="usuarios">Usuarios</a>
                <a href="#" class="nav-item" data-tab="datos">Datos</a>
            @endrole
        </div>

        <!-- Tab Content: Resumen -->
        <div id="resumen" class="tab-content active">
            <div class="profile-cards">
                <div class="card active">
                    <div class="number">0</div>
                    <div class="label">Eventos Participados</div>
                </div>
                <div class="card">
                    <div class="number">{{ $user->participant && $user->participant->team ? 1 : 0 }}</div>
                    <div class="label">Equipos Formados</div>
                </div>
            </div>

            <div class="profile-details-grid">
                <div class="details-card">
                    <h3><x-icon name="person" /> Información Personal</h3>
                    <p><strong>Nombre:</strong> {{ $user->name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Rol:</strong> {{ implode(', ', $user->getRoleNames()->toArray()) }}</p>
                    <p><x-icon name="date_range" /> Miembro desde {{ $user->created_at->format('M Y') }}</p>
                    <p><x-icon name="school" /> {{ $user->participant->institution ?? 'No especificada' }}</p>
                    @if($user->participant && $user->participant->team)
                        <p><x-icon name="groups" /> Equipo: {{ $user->participant->team->name }}</p>
                    @endif
                    @if($user->expertise)
                        <p><x-icon name="star" /> <strong>Experiencia:</strong> {{ $user->expertise }}</p>
                    @endif

                    @role('admin')
                        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                            <a href="{{ route('start') }}" class="btn-confirmar" style="display: inline-flex; align-items: center; gap: 8px; width: auto;">
                                <x-icon name="bar_chart" style="width: 20px; height: 20px;" />
                                Ir al Dashboard Administrativo
                            </a>
                        </div>
                    @endrole
                </div>

                <div class="details-card">
                    <h3><x-icon name="bar_chart" /> Habilidades Técnicas</h3>
                    <p>Tu progreso en diferentes tecnologías</p>
                    <!-- Static for now, can be dynamic later -->
                    <div class="progress-bar-container">
                        <div class="skill-name"><span>React</span><span>85%</span></div>
                        <div class="progress-bar"><div class="progress-bar-fill" style="width: 85%;"></div></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content: Equipos -->
        <div id="equipos" class="tab-content" style="display: none;">
            <div class="details-card">
                <h3>Mis Equipos</h3>
                @if($user->participant && $user->participant->team)
                    <div style="padding: 1rem; border: 1px solid #e5e7eb; border-radius: 8px; margin-top: 1rem;">
                        <h4>{{ $user->participant->team->name }}</h4>
                        <p>Evento: {{ $user->participant->team->event->name ?? 'Evento desconocido' }}</p>
                        <a href="{{ route('teams.index') }}" style="color: #4f46e5; text-decoration: none; font-weight: 600; margin-top: 0.5rem; display: inline-block;">Ver Equipo &rarr;</a>
                    </div>
                @else
                    <p style="margin-top: 1rem; color: #6b7280;">No perteneces a ningún equipo actualmente.</p>
                    <a href="{{ route('events.index') }}" style="color: #4f46e5; text-decoration: none; font-weight: 600; margin-top: 0.5rem; display: inline-block;">Buscar Eventos &rarr;</a>
                @endif
            </div>
        </div>

        <!-- Tab Content: Logros -->
        </div>

        <!-- Tab Content: Usuarios (Admin Only) -->
        @role('admin')
        <div id="usuarios" class="tab-content" style="display: none;">
            <div class="details-card">
                <h3>Gestión de Usuarios</h3>
                <p style="margin-bottom: 20px; color: #6b7280;">Administra todos los usuarios registrados en la plataforma.</p>
                
                @if(isset($users))
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                            <thead>
                                <tr style="background-color: #f9fafb; text-align: left;">
                                    <th style="padding: 12px; border-bottom: 2px solid #e5e7eb; color: #4b5563;">Nombre</th>
                                    <th style="padding: 12px; border-bottom: 2px solid #e5e7eb; color: #4b5563;">Email</th>
                                    <th style="padding: 12px; border-bottom: 2px solid #e5e7eb; color: #4b5563;">Rol</th>
                                    <th style="padding: 12px; border-bottom: 2px solid #e5e7eb; color: #4b5563;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $u)
                                    <tr>
                                        <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; font-weight: 600; color: #1f2937;">{{ $u->name }}</td>
                                        <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; color: #4b5563;">{{ $u->email }}</td>
                                        <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; color: #4b5563;">
                                            @foreach($u->roles as $role)
                                                <span style="background-color: #e0e7ff; color: #4338ca; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">
                                                    {{ ucfirst($role->name) }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td style="padding: 12px; border-bottom: 1px solid #e5e7eb;">
                                            <button class="btn-editar-usuario-admin"
                                                    data-id="{{ $u->id }}"
                                                    data-name="{{ $u->name }}"
                                                    data-email="{{ $u->email }}"
                                                    data-role="{{ $u->roles->first() ? $u->roles->first()->name : 'participante' }}"
                                                    style="background-color: #3b82f6; color: white; padding: 6px 12px; border: none; border-radius: 6px; font-size: 0.8rem; cursor: pointer; margin-right: 5px;">
                                                <x-icon name="edit" style="font-size: 16px;" />
                                            </button>
                                            @if($u->id !== auth()->id())
                                                <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar usuario {{ $u->name }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" style="background-color: #ef4444; color: white; padding: 6px 12px; border: none; border-radius: 6px; font-size: 0.8rem; cursor: pointer;">
                                                        <x-icon name="delete" style="font-size: 16px;" />
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
        @endrole

        <!-- Tab Content: Datos (Admin Only) -->
        @role('admin')
        <div id="datos" class="tab-content" style="display: none;">
            <div class="details-card">
                <h3>Gestión de Datos</h3>
                <p style="margin-bottom: 20px; color: #6b7280;">Administra las Instituciones y Carreras del sistema.</p>

                <div style="margin-bottom: 20px;">
                    <select id="data-selector" style="padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; background-color: #f9fafb; font-size: 1rem; cursor: pointer;">
                        <option value="instituciones">Instituciones</option>
                        <option value="carreras">Carreras</option>
                    </select>
                    <button id="btn-crear-dato" class="btn-confirmar" style="margin-left: 10px; width: auto; padding: 8px 15px;">
                        <x-icon name="add" style="vertical-align: middle;" /> Agregar Nuevo
                    </button>
                </div>

                <!-- Tabla Universidades -->
                <div id="tabla-universidades" class="data-table-container">
                    @if(isset($instituciones))
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                                <thead>
                                    <tr style="background-color: #f9fafb; text-align: left;">
                                        <th style="padding: 12px; border-bottom: 2px solid #e5e7eb; color: #4b5563;">ID</th>
                                        <th style="padding: 12px; border-bottom: 2px solid #e5e7eb; color: #4b5563;">Nombre</th>
                                        <th style="padding: 12px; border-bottom: 2px solid #e5e7eb; color: #4b5563;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($instituciones as $inst)
                                        <tr>
                                            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; color: #6b7280;">{{ $inst->id }}</td>
                                            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; font-weight: 600; color: #1f2937;">{{ $inst->name }}</td>
                                            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb;">
                                                <button class="btn-editar-institucion" 
                                                        data-id="{{ $inst->id }}" 
                                                        data-name="{{ $inst->name }}"
                                                        style="background-color: #3b82f6; color: white; padding: 6px 12px; border: none; border-radius: 6px; font-size: 0.8rem; cursor: pointer; margin-right: 5px;">
                                                    <x-icon name="edit" style="font-size: 16px;" />
                                                </button>
                                                <form action="{{ route('admin.instituciones.destroy', $inst->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar institución {{ $inst->name }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" style="background-color: #ef4444; color: white; padding: 6px 12px; border: none; border-radius: 6px; font-size: 0.8rem; cursor: pointer;">
                                                        <x-icon name="delete" style="font-size: 16px;" />
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- Tabla Carreras -->
                <div id="tabla-carreras" class="data-table-container" style="display: none;">
                    @if(isset($carreras))
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                                <thead>
                                    <tr style="background-color: #f9fafb; text-align: left;">
                                        <th style="padding: 12px; border-bottom: 2px solid #e5e7eb; color: #4b5563;">ID</th>
                                        <th style="padding: 12px; border-bottom: 2px solid #e5e7eb; color: #4b5563;">Nombre</th>
                                        <th style="padding: 12px; border-bottom: 2px solid #e5e7eb; color: #4b5563;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($carreras as $carrera)
                                        <tr>
                                            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; color: #6b7280;">{{ $carrera->id }}</td>
                                            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; font-weight: 600; color: #1f2937;">{{ $carrera->name }}</td>
                                            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb;">
                                                <button class="btn-editar-carrera" 
                                                        data-id="{{ $carrera->id }}" 
                                                        data-name="{{ $carrera->name }}"
                                                        style="background-color: #3b82f6; color: white; padding: 6px 12px; border: none; border-radius: 6px; font-size: 0.8rem; cursor: pointer; margin-right: 5px;">
                                                    <x-icon name="edit" style="font-size: 16px;" />
                                                </button>
                                                <form action="{{ route('admin.carreras.destroy', $carrera->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar carrera {{ $carrera->name }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" style="background-color: #ef4444; color: white; padding: 6px 12px; border: none; border-radius: 6px; font-size: 0.8rem; cursor: pointer;">
                                                        <x-icon name="delete" style="font-size: 16px;" />
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endrole
    </div>

    <!-- Edit Profile Modal -->
    <div id="modal-editar-perfil" class="modal">
        <div class="modal-content profile-modal-content">
            <div class="modal-header">
                <h2>Editar Perfil</h2>
                <span class="close-modal" id="close-editar">&times;</span>
            </div>
            <form action="{{ route('profile.update') }}" method="POST" class="profile-form" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="profile_photo">Foto de Perfil</label>
                    <div class="input-with-icon">
                        <x-icon name="image" />
                        <input type="file" id="profile_photo" name="profile_photo" accept="image/*">
                    </div>
                </div>
                <div class="form-group">
                    <label for="name">Nombre Completo</label>
                    <div class="input-with-icon">
                        <x-icon name="person" />
                        <input type="text" id="name" name="name" value="{{ $user->name }}" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <div class="input-with-icon">
                        <x-icon name="email" />
                        <input type="email" id="email" name="email" value="{{ $user->email }}" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="institucion">Institución</label>
                    <div class="input-with-icon">
                        <x-icon name="school" />
                        <input type="text" id="institucion" name="institucion" value="{{ $user->participant->institution ?? '' }}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="expertise">Áreas de Experiencia (Separadas por comas)</label>
                    <div class="input-with-icon">
                        <x-icon name="star" />
                        <input type="text" id="expertise" name="expertise" value="{{ $user->expertise }}" placeholder="Ej: IA, Finanzas, UX/UI">
                    </div>
                </div>
                <button type="submit" class="btn-confirmar">Guardar Cambios</button>
            </form>
        </div>
    </div>

    <!-- Create Judge Modal -->
    <div id="modal-crear-juez" class="modal">
        <div class="modal-content profile-modal-content">
            <div class="modal-header">
                <h2>Crear Juez</h2>
                <span class="close-modal" id="close-crear-juez">&times;</span>
            </div>
            <form action="{{ route('admin.judges.create') }}" method="POST" class="profile-form">
                @csrf
                <div class="form-group">
                    <label for="juez-name">Nombre Completo</label>
                    <div class="input-with-icon">
                        <x-icon name="person" />
                        <input type="text" id="juez-name" name="name" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="juez-email">Correo Electrónico</label>
                    <div class="input-with-icon">
                        <x-icon name="email" />
                        <input type="email" id="juez-email" name="email" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="juez-password">Contraseña</label>
                    <div class="input-with-icon">
                        <x-icon name="lock" />
                        <input type="password" id="juez-password" name="password" required>
                    </div>
                </div>
                <button type="submit" class="btn-confirmar">Crear Juez</button>
            </form>
        </div>
    </div>

    </div>

    <!-- Modal Editar Usuario (Admin) -->
    <div id="modal-editar-usuario-admin" class="modal">
        <div class="modal-content profile-modal-content">
            <div class="modal-header">
                <h2>Editar Usuario</h2>
                <span class="close-modal" id="close-editar-usuario-admin">&times;</span>
            </div>
            <form action="" method="POST" id="form-editar-usuario-admin" class="profile-form">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="edit-user-name">Nombre Completo</label>
                    <div class="input-with-icon">
                        <x-icon name="person" />
                        <input type="text" id="edit-user-name" name="name" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit-user-email">Correo Electrónico</label>
                    <div class="input-with-icon">
                        <x-icon name="email" />
                        <input type="email" id="edit-user-email" name="email" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit-user-role">Rol</label>
                    <div class="input-with-icon">
                        <x-icon name="badge" />
                        <select id="edit-user-role" name="role" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;">
                            <option value="admin">Admin</option>
                            <option value="juez">Juez</option>
                            <option value="participante">Participante</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn-confirmar">Actualizar Usuario</button>
            </form>
        </div>
    </div>

    <!-- Modal Crear/Editar Institucion -->
    <div id="modal-institucion" class="modal">
        <div class="modal-content profile-modal-content">
            <div class="modal-header">
                <h2 id="modal-institucion-title">Nueva Institución</h2>
                <span class="close-modal" id="close-institucion">&times;</span>
            </div>
            <form action="{{ route('admin.instituciones.store') }}" method="POST" id="form-institucion" class="profile-form">
                @csrf
                <div id="method-institucion"></div>
                <div class="form-group">
                    <label for="institucion-nombre">Nombre de la Institución</label>
                    <div class="input-with-icon">
                        <x-icon name="school" />
                        <input type="text" id="institucion-nombre" name="nombre" required>
                    </div>
                </div>
                <button type="submit" class="btn-confirmar">Guardar</button>
            </form>
        </div>
    </div>

    <!-- Modal Crear/Editar Carrera -->
    <div id="modal-carrera" class="modal">
        <div class="modal-content profile-modal-content">
            <div class="modal-header">
                <h2 id="modal-carrera-title">Nueva Carrera</h2>
                <span class="close-modal" id="close-carrera">&times;</span>
            </div>
            <form action="{{ route('admin.carreras.store') }}" method="POST" id="form-carrera" class="profile-form">
                @csrf
                <div id="method-carrera"></div>
                <div class="form-group">
                    <label for="carrera-nombre">Nombre de la Carrera</label>
                    <div class="input-with-icon">
                        <x-icon name="badge" />
                        <input type="text" id="carrera-nombre" name="nombre" required>
                    </div>
                </div>
                <button type="submit" class="btn-confirmar">Guardar</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab Navigation
            const navItems = document.querySelectorAll('.nav-item');
            const tabContents = document.querySelectorAll('.tab-content');

            navItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Remove active class from all items and hide all contents
                    navItems.forEach(nav => nav.classList.remove('active'));
                    tabContents.forEach(content => {
                        content.style.display = 'none';
                        content.classList.remove('active');
                    });

                    // Add active class to clicked item and show corresponding content
                    this.classList.add('active');
                    const tabId = this.getAttribute('data-tab');
                    const activeContent = document.getElementById(tabId);
                    if(activeContent) {
                        activeContent.style.display = 'block';
                        activeContent.classList.add('active');
                    }
                });
            });

            // Edit Modal Logic
            const modalEditar = document.getElementById('modal-editar-perfil');
            const btnEditar = document.getElementById('btn-editar-perfil');
            const closeEditar = document.getElementById('close-editar');

            if(btnEditar) {
                btnEditar.addEventListener('click', function() {
                    modalEditar.style.display = 'flex';
                });
            }

            if(closeEditar) {
                closeEditar.addEventListener('click', function() {
                    modalEditar.style.display = 'none';
                });
            }

            // Create Judge Modal Logic
            const modalJuez = document.getElementById('modal-crear-juez');
            const btnJuez = document.getElementById('btn-crear-juez');
            const closeJuez = document.getElementById('close-crear-juez');

            if(btnJuez) {
                btnJuez.addEventListener('click', function() {
                    modalJuez.style.display = 'flex';
                });
            }

            if(closeJuez) {
                closeJuez.addEventListener('click', function() {
                    modalJuez.style.display = 'none';
                });
            }

            window.addEventListener('click', function(event) {
                if (event.target == modalEditar) {
                    modalEditar.style.display = 'none';
                }
                if (event.target == modalJuez) {
                    modalJuez.style.display = 'none';
                }
                const modalEditUser = document.getElementById('modal-editar-usuario-admin');
                if (event.target == modalEditUser) {
                    modalEditUser.style.display = 'none';
                }
            });

            // Admin Edit User Logic
            const modalEditUser = document.getElementById('modal-editar-usuario-admin');
            const btnsEditUser = document.querySelectorAll('.btn-editar-usuario-admin');
            const closeEditUser = document.getElementById('close-editar-usuario-admin');
            const formEditUser = document.getElementById('form-editar-usuario-admin');
            const inputEditName = document.getElementById('edit-user-name');
            const inputEditEmail = document.getElementById('edit-user-email');
            const selectEditRole = document.getElementById('edit-user-role');

            if (btnsEditUser.length > 0) {
                btnsEditUser.forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const name = this.getAttribute('data-name');
                        const email = this.getAttribute('data-email');
                        const role = this.getAttribute('data-role');

                        formEditUser.action = "/admin/users/" + id;
                        inputEditName.value = name;
                        inputEditEmail.value = email;
                        selectEditRole.value = role;

                        modalEditUser.style.display = 'flex';
                    });
                });
            }

            if (closeEditUser) {
                closeEditUser.addEventListener('click', function() {
                    modalEditUser.style.display = 'none';
                });
            }

            // Data Management Logic (Admin)
            const dataSelector = document.getElementById('data-selector');
            const tableUniversidades = document.getElementById('tabla-universidades');
            const tableCarreras = document.getElementById('tabla-carreras');
            const btnCrearDato = document.getElementById('btn-crear-dato');
            
            // Modals
            const modalInstitucion = document.getElementById('modal-institucion');
            const modalCarrera = document.getElementById('modal-carrera');
            const closeInstitucion = document.getElementById('close-institucion');
            const closeCarrera = document.getElementById('close-carrera');
            
            // Forms
            const formInstitucion = document.getElementById('form-institucion');
            const formCarrera = document.getElementById('form-carrera');
            const titleInstitucion = document.getElementById('modal-institucion-title');
            const titleCarrera = document.getElementById('modal-carrera-title');
            const inputInstitucion = document.getElementById('institucion-nombre');
            const inputCarrera = document.getElementById('carrera-nombre');
            const methodInstitucion = document.getElementById('method-institucion');
            const methodCarrera = document.getElementById('method-carrera');

            if (dataSelector) {
                dataSelector.addEventListener('change', function() {
                    if (this.value === 'instituciones') {
                        tableUniversidades.style.display = 'block';
                        tableCarreras.style.display = 'none';
                    } else {
                        tableUniversidades.style.display = 'none';
                        tableCarreras.style.display = 'block';
                    }
                });

                btnCrearDato.addEventListener('click', function() {
                    if (dataSelector.value === 'instituciones') {
                        formInstitucion.action = "{{ route('admin.instituciones.store') }}";
                        methodInstitucion.innerHTML = ''; // Remove PUT method
                        titleInstitucion.textContent = 'Nueva Institución';
                        inputInstitucion.value = '';
                        modalInstitucion.style.display = 'flex';
                    } else {
                        formCarrera.action = "{{ route('admin.carreras.store') }}";
                        methodCarrera.innerHTML = ''; // Remove PUT method
                        titleCarrera.textContent = 'Nueva Carrera';
                        inputCarrera.value = '';
                        modalCarrera.style.display = 'flex';
                    }
                });

                // Edit Buttons
                document.querySelectorAll('.btn-editar-institucion').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const nombre = this.getAttribute('data-name');
                        
                        formInstitucion.action = "/admin/instituciones/" + id;
                        methodInstitucion.innerHTML = '@method("PUT")';
                        titleInstitucion.textContent = 'Editar Institución';
                        inputInstitucion.value = nombre;
                        modalInstitucion.style.display = 'flex';
                    });
                });

                document.querySelectorAll('.btn-editar-carrera').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const nombre = this.getAttribute('data-name');
                        
                        formCarrera.action = "/admin/carreras/" + id;
                        methodCarrera.innerHTML = '@method("PUT")';
                        titleCarrera.textContent = 'Editar Carrera';
                        inputCarrera.value = nombre;
                        modalCarrera.style.display = 'flex';
                    });
                });

                // Close Modals
                if (closeInstitucion) {
                    closeInstitucion.addEventListener('click', () => modalInstitucion.style.display = 'none');
                }
                if (closeCarrera) {
                    closeCarrera.addEventListener('click', () => modalCarrera.style.display = 'none');
                }
                
                window.addEventListener('click', function(event) {
                    if (event.target == modalInstitucion) modalInstitucion.style.display = 'none';
                    if (event.target == modalCarrera) modalCarrera.style.display = 'none';
                });
            }
        });
    </script>

@endsection
