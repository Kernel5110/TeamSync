@extends('layouts.app')

@section('content')


    <div class="container">
        @if(session('success'))
            <div style="background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border: 1px solid #a7f3d0;">
                {{ session('success') }}
            </div>
        @endif

        <div class="profile-header">
            <div class="profile-avatar">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>

            <div class="profile-info">
                <h1>{{ $user->name }}</h1>
                <p>&lt;> {{ $user->participante->rol ?? 'Participante' }}</p>
                <p>{{ $user->participante->institucion ?? 'Institución no especificada' }}</p>
                <div class="profile-badges">
                    <span class="profile-badge">Innovador</span>
                    <span class="profile-badge">Competidor</span>
                </div>
            </div>

            <div class="profile-actions">
                @role('admin')
                    <button id="btn-crear-juez" style="background-color: #8b5cf6; color: white;"><span class="material-icons">gavel</span> Crear Juez</button>
                @endrole
                <button id="btn-editar-perfil"><span class="material-icons">edit</span> Editar</button>
                <div class="settings-icon">
                    <span class="material-icons">settings</span>
                </div>
            </div>
        </div>

        <div class="profile-stats-nav">
            <a href="#" class="nav-item active" data-tab="resumen">Resumen</a>
            @unlessrole('juez')
                <a href="#" class="nav-item" data-tab="equipos">Equipos</a>
            @endunlessrole
            <a href="#" class="nav-item" data-tab="logros">Logros</a>
        </div>

        <!-- Tab Content: Resumen -->
        <div id="resumen" class="tab-content active">
            <div class="profile-cards">
                <div class="card active">
                    <div class="number">0</div>
                    <div class="label">Eventos Participados</div>
                </div>
                <div class="card">
                    <div class="number">{{ $user->participante && $user->participante->equipo ? 1 : 0 }}</div>
                    <div class="label">Equipos Formados</div>
                </div>
            </div>

            <div class="profile-details-grid">
                <div class="details-card">
                    <h3><span class="material-icons">person</span> Información Personal</h3>
                    <div class="details-list">
                        <p><span class="material-icons">email</span> {{ $user->email }}</p>
                        <p><span class="material-icons">date_range</span> Miembro desde {{ $user->created_at->format('M Y') }}</p>
                        <p><span class="material-icons">school</span> {{ $user->participante->institucion ?? 'No especificada' }}</p>
                        @if($user->participante && $user->participante->equipo)
                            <p><span class="material-icons">groups</span> Equipo: {{ $user->participante->equipo->nombre }}</p>
                        @endif
                    </div>
                </div>

                <div class="details-card">
                    <h3><span class="material-icons">bar_chart</span> Habilidades Técnicas</h3>
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
                @if($user->participante && $user->participante->equipo)
                    <div style="padding: 1rem; border: 1px solid #e5e7eb; border-radius: 8px; margin-top: 1rem;">
                        <h4>{{ $user->participante->equipo->nombre }}</h4>
                        <p>Evento: {{ $user->participante->equipo->evento->nombre ?? 'Evento desconocido' }}</p>
                        <a href="{{ route('team') }}" style="color: #4f46e5; text-decoration: none; font-weight: 600; margin-top: 0.5rem; display: inline-block;">Ver Equipo &rarr;</a>
                    </div>
                @else
                    <p style="margin-top: 1rem; color: #6b7280;">No perteneces a ningún equipo actualmente.</p>
                    <a href="{{ route('event') }}" style="color: #4f46e5; text-decoration: none; font-weight: 600; margin-top: 0.5rem; display: inline-block;">Buscar Eventos &rarr;</a>
                @endif
            </div>
        </div>

        <!-- Tab Content: Logros -->
        <div id="logros" class="tab-content" style="display: none;">
            <div class="details-card">
                <h3>Mis Logros</h3>
                <p style="margin-top: 1rem; color: #6b7280;">Aún no tienes logros registrados. ¡Participa en eventos para ganar insignias!</p>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div id="modal-editar-perfil" class="modal">
        <div class="modal-content profile-modal-content">
            <div class="modal-header">
                <h2>Editar Perfil</h2>
                <span class="close-modal" id="close-editar">&times;</span>
            </div>
            <form action="{{ route('perfil.update') }}" method="POST" class="profile-form">
                @csrf
                <div class="form-group">
                    <label for="name">Nombre Completo</label>
                    <div class="input-with-icon">
                        <span class="material-icons">person</span>
                        <input type="text" id="name" name="name" value="{{ $user->name }}" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <div class="input-with-icon">
                        <span class="material-icons">email</span>
                        <input type="email" id="email" name="email" value="{{ $user->email }}" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="institucion">Institución</label>
                    <div class="input-with-icon">
                        <span class="material-icons">school</span>
                        <input type="text" id="institucion" name="institucion" value="{{ $user->participante->institucion ?? '' }}">
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
            <form action="{{ route('admin.createJudge') }}" method="POST" class="profile-form">
                @csrf
                <div class="form-group">
                    <label for="juez-name">Nombre Completo</label>
                    <div class="input-with-icon">
                        <span class="material-icons">person</span>
                        <input type="text" id="juez-name" name="name" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="juez-email">Correo Electrónico</label>
                    <div class="input-with-icon">
                        <span class="material-icons">email</span>
                        <input type="email" id="juez-email" name="email" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="juez-password">Contraseña</label>
                    <div class="input-with-icon">
                        <span class="material-icons">lock</span>
                        <input type="password" id="juez-password" name="password" required>
                    </div>
                </div>
                <button type="submit" class="btn-confirmar">Crear Juez</button>
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
            });
        });
    </script>

@endsection
