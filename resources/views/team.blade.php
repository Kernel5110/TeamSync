@extends('layouts.app')

@section('title', 'Equipo - TeamSync')


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
                <h1>Equipos</h1>
                <p>Gestion de equipos de desarrollo y colaboradores</p>
            </div>
            <div class="acciones-equipo" style="display: flex; gap: 10px; align-items: center;">
                <div class="search-container" style="position: relative;">
                    <x-icon name="search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #6b7280;" />
                    <input type="text" id="search-team-input" placeholder="Buscar equipo..." style="padding: 8px 10px 8px 35px; border: 1px solid #d1d5db; border-radius: 20px; font-size: 0.9rem; width: 200px; transition: width 0.3s;">
                </div>
                @if(!$equipo)
                    <a href="#modal-crear-equipo" class="btn-nuevo">
                        <x-icon name="add" /> Nuevo
                    </a>
                @else
                    <a href="#modal-agregar-miembro" class="btn-nuevo">
                        <x-icon name="person_add" /> Agregar Miembro
                    </a>
                @endif
            </div>
        </div>

        @if($equipo)
            <div class="tarjeta-miembros">
                <div class="titulo-seccion" style="display: flex; align-items: center; gap: 15px;">
                    @if($equipo->logo_path)
                        <img src="{{ asset('storage/' . $equipo->logo_path) }}" alt="Logo {{ $equipo->nombre }}" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid #e5e7eb;">
                    @else
                        <div style="width: 50px; height: 50px; border-radius: 50%; background-color: #e0e7ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem;">
                            {{ strtoupper(substr($equipo->nombre, 0, 2)) }}
                        </div>
                    @endif
                    Miembros del Equipo: {{ $equipo->nombre }}
                    @if(auth()->user()->participant && auth()->user()->participant->rol == 'Líder' && auth()->user()->participant->equipo_id == $equipo->id)
                        <a href="#modal-editar-mi-equipo" id="btn-editar-mi-equipo" style="color: #6b7280; text-decoration: none;" title="Editar Equipo">
                            <x-icon name="edit" />
                        </a>
                    @endif
                </div>
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
                                        @if($rolClass == 'rol-programador') <x-icon name="code" style="font-size: 14px;" /> @endif
                                        @if($rolClass == 'rol-disenador') <x-icon name="palette" style="font-size: 14px;" /> @endif
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
                    <div class="stat-number">{{ $equipo->progress }}%</div>
                    <div class="stat-label">Progreso</div>
                </div>
            </div>
        @else
            @unlessrole('admin')
                @if($myPendingRequest)
                     <div class="tarjeta-miembros" style="text-align: center; padding: 3rem;">
                        <x-icon name="pending" style="font-size: 4rem; color: #f59e0b;" />
                        <h2 style="margin-top: 1rem; color: #374151;">Solicitud Enviada</h2>
                        <p style="color: #6b7280; margin-bottom: 2rem;">Has solicitado unirte al equipo <strong>{{ $myPendingRequest->equipo->nombre }}</strong>. Espera a que el líder acepte tu solicitud.</p>
                    </div>
                @else
                    <div class="tarjeta-miembros" style="text-align: center; padding: 3rem;">
                        <x-icon name="groups" style="font-size: 4rem; color: #d1d5db;" />
                        <h2 style="margin-top: 1rem; color: #374151;">No tienes un equipo aún</h2>
                        <p style="color: #6b7280; margin-bottom: 2rem;">Crea un equipo para participar en eventos o solicita unirte a uno existente.</p>
                        <a href="#modal-crear-equipo" class="btn-nuevo" style="display: inline-flex;">
                            Crear Equipo
                        </a>
                    </div>
                @endif
            @endunlessrole
        @endif

        {{-- Messages Section for Leaders --}}
        @if(isset($pendingRequests) && count($pendingRequests) > 0)
            <div class="tarjeta-miembros" style="margin-top: 2rem; border-left: 4px solid #f59e0b;">
                <div class="titulo-seccion" style="display: flex; align-items: center; gap: 10px;">
                    <x-icon name="mail" style="color: #f59e0b;" />
                    Mensajes / Solicitudes Pendientes
                </div>
                <table class="tabla-miembros">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Institución</th>
                            <th>Carrera</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingRequests as $request)
                            <tr>
                                <td>{{ $request->user->name }}</td>
                                <td>{{ $request->user->participante->institucion ?? 'N/A' }}</td>
                                <td>{{ $request->user->participante->carrera->nombre ?? 'N/A' }}</td>
                                <td>
                                    <div style="display: flex; gap: 10px;">
                                        <form action="{{ route('requests.accept', $request->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn-nuevo" style="background: #10b981; padding: 5px 10px; font-size: 0.8rem;">
                                                Aceptar
                                            </button>
                                        </form>
                                        <form action="{{ route('requests.reject', $request->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn-nuevo" style="background: #ef4444; padding: 5px 10px; font-size: 0.8rem;">
                                                Rechazar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Other Teams Section --}}
        @if(isset($otherTeams) && $otherTeams->count() > 0)
            <div class="contenedor-equipo" style="margin-top: 40px;">
                <div class="titulo-seccion">Otros Equipos Disponibles</div>
                <div class="grid-equipos" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                    @foreach($otherTeams as $team)
                        <div class="card-equipo" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                                <div>
                                    <h3 style="margin: 0; font-size: 1.2rem; color: #111827;">{{ $team->nombre }}</h3>
                                    <span style="font-size: 0.9rem; color: #6b7280;">{{ $team->evento->nombre ?? 'Sin Evento' }}</span>
                                </div>
                                <span class="badge-rol" style="background: #e0e7ff; color: #4338ca;">
                                    {{ $team->participantes->count() }} Miembros
                                </span>
                            </div>
                            
                            <div style="margin-bottom: 15px;">
                                <p style="margin: 0; font-size: 0.9rem; color: #4b5563;">Líder: 
                                    @php
                                        $lider = $team->participantes->where('rol', 'Líder')->first();
                                    @endphp
                                    {{ $lider ? $lider->user->name : 'N/A' }}
                                </p>
                            </div>

                            @if(!$equipo && !$myPendingRequest && !Auth::user()->hasRole('admin'))
                                <form action="{{ route('teams.join', $team->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-nuevo" style="width: 100%; justify-content: center; background: #4f46e5;">
                                        Solicitar Unirse
                                    </button>
                                </form>
                            @elseif($myPendingRequest && $myPendingRequest->equipo_id == $team->id)
                                <button disabled class="btn-nuevo" style="width: 100%; justify-content: center; background: #9ca3af; cursor: not-allowed;">
                                    Solicitud Enviada
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div style="margin-top: 20px; display: flex; justify-content: center;">
                    {{ $otherTeams->appends(['all_teams_page' => $allTeams ? $allTeams->currentPage() : 1])->links('pagination::simple-tailwind') }}
                </div>
            </div>
        @endif

    @role('admin')
        <div class="contenedor-equipo" style="margin-top: 40px;">
            <div class="team-header">
            <h1>Administracion de equipos y miembros</h1>
        </div>
            <p>Equipos registrados en el sistema:</p>


            @if(isset($allTeams) && $allTeams->count() > 0)
                <div class="tarjeta-miembros">
                    <table class="tabla-miembros">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre del Equipo</th>
                                <th>Evento</th>
                                <th>Participantes</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($allTeams as $t)
                                <tr>
                                    <td>{{ $t->id }}</td>
                                    <td>{{ $t->nombre }}</td>
                                    <td>{{ $t->evento->nombre ?? 'N/A' }}</td>
                                    <td>
                                        <ul style="margin: 0; padding-left: 20px; font-size: 0.9rem;">
                                            @foreach($t->participantes as $p)
                                                <li>{{ $p->user->name }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>
                                        <button class="btn-editar-equipo"
                                                data-id="{{ $t->id }}"
                                                data-nombre="{{ $t->nombre }}"
                                                data-evento-id="{{ $t->evento_id }}"
                                                data-project-name="{{ $t->project_name }}"
                                                data-project-description="{{ $t->project_description }}"
                                                data-technologies="{{ $t->technologies }}"
                                                data-github-repo="{{ $t->github_repo }}"
                                                data-github-pages="{{ $t->github_pages }}"
                                                data-members="{{ $t->participantes->map(function($p) { return ['id' => $p->user->id, 'name' => $p->user->name, 'rol' => $p->rol]; })->toJson() }}"
                                                style="background: none; border: none; cursor: pointer; color: #4f46e5; margin-right: 10px;">
                                            <x-icon name="edit" />
                                        </button>
                                        <form action="{{ route('teams.destroy', $t->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Estás seguro de eliminar este equipo?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background: none; border: none; cursor: pointer; color: #ef4444;">
                                                <x-icon name="delete" />
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div style="margin-top: 20px; display: flex; justify-content: center;">
                        {{ $allTeams->appends(['other_teams_page' => $otherTeams ? $otherTeams->currentPage() : 1])->links('pagination::simple-tailwind') }}
                    </div>
                </div>
            @else
                <p>No hay equipos registrados en el sistema.</p>
            @endif
        </div>
    @endrole

    <!-- Modal Editar Equipo (Admin) -->
    <!-- Modal Editar Equipo (Admin) -->
    <div id="modal-editar-equipo" class="modal" style="z-index: 10000;">
        <div class="modal-content">
            <span class="close-modal" id="close-editar-equipo">&times;</span>
            <h2 style="margin-bottom: 1.5rem;">Editar Equipo</h2>
            <form action="" method="POST" id="form-editar-equipo" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="edit-nombre">Nombre del Equipo</label>
                    <input type="text" id="edit-nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="edit-logo">Logo del Equipo (Opcional)</label>
                    <input type="file" id="edit-logo" name="logo" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="edit-evento_id">Evento</label>
                    <select id="edit-evento_id" name="evento_id" required>
                        @foreach($eventos as $evento)
                            <option value="{{ $evento->id }}">{{ $evento->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="border-top: 1px solid #e5e7eb; margin: 1.5rem 0; padding-top: 1rem;">
                    <h3 style="font-size: 1.1rem; color: #374151; margin-bottom: 1rem;">Información del Proyecto</h3>
                    
                    <div class="form-group">
                        <label for="edit-project_name">Nombre del Proyecto</label>
                        <input type="text" id="edit-project_name" name="project_name" placeholder="Nombre de su proyecto">
                    </div>

                    <div class="form-group">
                        <label for="edit-project_description">Descripción</label>
                        <textarea id="edit-project_description" name="project_description" rows="3" placeholder="Breve descripción"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="edit-technologies">Tecnologías</label>
                        <input type="text" id="edit-technologies" name="technologies" placeholder="Ej: Laravel, Vue">
                    </div>

                    <div class="form-group">
                        <label for="edit-github_repo">Repositorio Git</label>
                        <input type="url" id="edit-github_repo" name="github_repo" placeholder="https://github.com/usuario/repo">
                    </div>

                    <div class="form-group">
                        <label for="edit-github_pages">GitHub Pages / Demo URL</label>
                        <input type="url" id="edit-github_pages" name="github_pages" placeholder="https://usuario.github.io/repo">
                    </div>
                </div>
                <div class="form-group">
                    <label>Miembros del Equipo</label>
                    <div id="edit-members-list" style="border: 1px solid #d1d5db; border-radius: 8px; padding: 10px; max-height: 150px; overflow-y: auto;">
                        <!-- Members populated by JS -->
                    </div>
                </div>
                <button type="submit" class="btn-submit">Actualizar Equipo</button>
            </form>

            <!-- Hidden form for removing members -->
            <form id="form-remove-member" method="POST" style="display: none;">
                @csrf
                <input type="hidden" name="user_id" id="remove-user-id">
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalEditar = document.getElementById('modal-editar-equipo');
            const btnsEditar = document.querySelectorAll('.btn-editar-equipo');
            const closeEditar = document.getElementById('close-editar-equipo');
            const formEditar = document.getElementById('form-editar-equipo');
            const inputNombre = document.getElementById('edit-nombre');
            const selectEvento = document.getElementById('edit-evento_id');
            const inputProjectName = document.getElementById('edit-project_name');
            const inputProjectDesc = document.getElementById('edit-project_description');
            const inputTechnologies = document.getElementById('edit-technologies');
            const inputGithubRepo = document.getElementById('edit-github_repo');
            const inputGithubPages = document.getElementById('edit-github_pages');

            btnsEditar.forEach((btn) => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    try {
                        const id = this.getAttribute('data-id');
                        const nombre = this.getAttribute('data-nombre');
                        const eventoId = this.getAttribute('data-evento-id');
                        
                        let members = [];
                        const membersAttr = this.getAttribute('data-members');
                        if (membersAttr) {
                            try {
                                members = JSON.parse(membersAttr);
                            } catch(e) {
                                // Silent fail or handle gracefully
                            }
                        }

                        if (!modalEditar) return;

                        formEditar.action = "/teams/" + id;
                        inputNombre.value = nombre;
                        selectEvento.value = eventoId;
                        inputProjectName.value = this.getAttribute('data-project-name') || '';
                        inputProjectDesc.value = this.getAttribute('data-project-description') || '';
                        inputTechnologies.value = this.getAttribute('data-technologies') || '';
                        inputGithubRepo.value = this.getAttribute('data-github-repo') || '';
                        inputGithubPages.value = this.getAttribute('data-github-pages') || '';

                        // Populate members list
                        const membersList = document.getElementById('edit-members-list');
                        membersList.innerHTML = '';
                        if (members.length > 0) {
                            members.forEach(member => {
                                const div = document.createElement('div');
                                div.style.display = 'flex';
                                div.style.justifyContent = 'space-between';
                                div.style.alignItems = 'center';
                                div.style.marginBottom = '5px';
                                div.style.padding = '5px';
                                div.style.borderBottom = '1px solid #f3f4f6';

                                const nameSpan = document.createElement('span');
                                nameSpan.textContent = member.name + (member.rol === 'Líder' ? ' (Líder)' : '');

                                const removeBtn = document.createElement('button');
                                removeBtn.type = 'button';
                                removeBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>';
                                removeBtn.style.background = 'none';
                                removeBtn.style.border = 'none';
                                removeBtn.style.color = '#ef4444';
                                removeBtn.style.cursor = 'pointer';
                                removeBtn.title = 'Eliminar miembro';
                                removeBtn.onclick = function() {
                                    if(confirm('¿Eliminar a ' + member.name + ' del equipo?')) {
                                        const removeForm = document.getElementById('form-remove-member');
                                        document.getElementById('remove-user-id').value = member.id;
                                        removeForm.action = "/teams/" + id + "/members/remove";
                                        removeForm.submit();
                                    }
                                };

                                div.appendChild(nameSpan);
                                div.appendChild(removeBtn);
                                membersList.appendChild(div);
                            });
                        } else {
                            membersList.innerHTML = '<p style="color: #9ca3af; text-align: center;">Sin miembros</p>';
                        }

                        // Force display
                        modalEditar.style.display = 'flex';

                    } catch (error) {
                        alert('Error al abrir el modal de edición.');
                    }
                });
            });

            if(closeEditar) {
                closeEditar.onclick = function() {
                    modalEditar.style.display = 'none';
                }
            }

            window.onclick = function(event) {
                if (event.target == modalEditar) {
                    modalEditar.style.display = 'none';
                }
            }
        });
    </script>

    <!-- Modal Crear Equipo -->
    <div id="modal-crear-equipo" class="modal">
        <div class="modal-content">
            <a href="#" class="close-modal">&times;</a>
            <h2 style="margin-bottom: 1.5rem;">Crear Nuevo Equipo</h2>
            <form action="{{ route('teams.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="nombre">Nombre del Equipo</label>
                    <input type="text" id="nombre" name="nombre" required placeholder="Ej. Alpha Team">
                </div>
                <div class="form-group">
                    <label for="logo">Logo del Equipo (Opcional)</label>
                    <input type="file" id="logo" name="logo" accept="image/*">
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

    <!-- Modal Editar Mi Equipo (Leader) -->
    @if($equipo)
    <div id="modal-editar-mi-equipo" class="modal">
        <div class="modal-content">
            <span class="close-modal" id="close-editar-mi-equipo">&times;</span>
            <h2 style="margin-bottom: 1.5rem;">Editar Mi Equipo</h2>
            <form action="{{ route('teams.update', $equipo->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="evento_id" value="{{ $equipo->evento_id }}">
                <div class="form-group">
                    <label for="mi-equipo-nombre">Nombre del Equipo</label>
                    <input type="text" id="mi-equipo-nombre" name="nombre" value="{{ $equipo->nombre }}" required>
                </div>
                
                <div style="border-top: 1px solid #e5e7eb; margin: 1.5rem 0; padding-top: 1rem;">
                    <h3 style="font-size: 1.1rem; color: #374151; margin-bottom: 1rem;">Información del Proyecto</h3>
                    
                    <div class="form-group">
                        <label for="project_name">Nombre del Proyecto</label>
                        <input type="text" id="project_name" name="project_name" value="{{ $equipo->project_name }}" placeholder="Nombre de su proyecto">
                    </div>

                    <div class="form-group">
                        <label for="project_description">Descripción</label>
                        <textarea id="project_description" name="project_description" rows="3" placeholder="Breve descripción de lo que hace su proyecto">{{ $equipo->project_description }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="technologies">Tecnologías (separadas por coma)</label>
                        <input type="text" id="technologies" name="technologies" value="{{ $equipo->technologies }}" placeholder="Ej: Laravel, Vue, MySQL">
                    </div>

                    <div class="form-group">
                        <label for="github_repo">Repositorio Git</label>
                        <input type="url" id="github_repo" name="github_repo" value="{{ $equipo->github_repo }}" placeholder="https://github.com/usuario/repo">
                    </div>

                    <div class="form-group">
                        <label for="github_pages">GitHub Pages / Demo URL</label>
                        <input type="url" id="github_pages" name="github_pages" value="{{ $equipo->github_pages }}" placeholder="https://usuario.github.io/repo">
                    </div>
                </div>

                <div class="form-group">
                    <label for="mi-equipo-logo">Logo del Equipo (Opcional)</label>
                    <input type="file" id="mi-equipo-logo" name="logo" accept="image/*">
                </div>
                <button type="submit" class="btn-submit">Guardar Cambios</button>
            </form>
        </div>
    </div>
    @endif

    <!-- Modal Agregar Miembro -->
    <div id="modal-agregar-miembro" class="modal">
        <div class="modal-content">
            <a href="#" class="close-modal">&times;</a>
            <h2 style="margin-bottom: 1.5rem;">Agregar Miembro</h2>
            <form action="{{ route('teams.members.add') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email">Correo Electrónico del Usuario</label>
                    <input type="email" id="email" name="email" required placeholder="usuario@ejemplo.com">
                </div>
                <button type="submit" class="btn-submit">Enviar Invitación</button>
            </form>
        </div>
    </div>
    <!-- Modal Buscar Equipo -->
    <div id="modal-buscar-equipo" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <span class="close-modal" id="close-buscar-equipo">&times;</span>
            <h2 style="margin-bottom: 1.5rem;">Resultados de Búsqueda</h2>
            <div id="search-results-container">
                <!-- Results will be loaded here -->
                <p style="text-align: center; color: #6b7280;">Escribe en el buscador para encontrar equipos.</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Search Logic
            const searchInput = document.getElementById('search-team-input');
            const modalBuscar = document.getElementById('modal-buscar-equipo');
            const closeBuscar = document.getElementById('close-buscar-equipo');
            const resultsContainer = document.getElementById('search-results-container');
            let timeout = null;

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value;

                    clearTimeout(timeout);

                    if (query.length > 0) {
                        modalBuscar.style.display = 'flex';
                        resultsContainer.innerHTML = '<p style="text-align: center; padding: 20px;">Buscando...</p>';

                        timeout = setTimeout(() => {
                            fetch(`{{ route('teams.search') }}?query=${encodeURIComponent(query)}`, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                resultsContainer.innerHTML = data.html;
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                resultsContainer.innerHTML = '<p style="text-align: center; color: red;">Error al buscar equipos.</p>';
                            });
                        }, 500); // Debounce
                    } else {
                        modalBuscar.style.display = 'none';
                    }
                });
            }

            if (closeBuscar) {
                closeBuscar.onclick = function() {
                    modalBuscar.style.display = 'none';
                }
            }

            // Close modal when clicking outside
            window.onclick = function(event) {
                if (event.target == modalBuscar) {
                    modalBuscar.style.display = 'none';
                }
                // Existing modals
                const modalEditar = document.getElementById('modal-editar-equipo');
                if (event.target == modalEditar) {
                    modalEditar.style.display = 'none';
                }
                const modalCrear = document.getElementById('modal-crear-equipo');
                if (event.target == modalCrear) {
                    modalCrear.style.display = 'none';
                }
                const modalAgregar = document.getElementById('modal-agregar-miembro');
                if (event.target == modalAgregar) {
                    modalAgregar.style.display = 'none';
                }
                const modalEditarMiEquipo = document.getElementById('modal-editar-mi-equipo');
                if (event.target == modalEditarMiEquipo) {
                    modalEditarMiEquipo.style.display = 'none';
                }
            }

            // Leader Edit Team Logic
            const btnEditarMiEquipo = document.getElementById('btn-editar-mi-equipo');
            const modalEditarMiEquipo = document.getElementById('modal-editar-mi-equipo');
            const closeEditarMiEquipo = document.getElementById('close-editar-mi-equipo');

            if (btnEditarMiEquipo) {
                btnEditarMiEquipo.addEventListener('click', function(e) {
                    e.preventDefault();
                    modalEditarMiEquipo.style.display = 'flex';
                });
            }

            if (closeEditarMiEquipo) {
                closeEditarMiEquipo.addEventListener('click', function() {
                    modalEditarMiEquipo.style.display = 'none';
                });
            }
        });
    </script>
@endsection

<!-- Move Modal Outside Main Content to avoid overflow/z-index issues -->
<!-- Modal Editar Equipo (Admin) -->
<div id="modal-editar-equipo" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 10000; justify-content: center; align-items: center;">
    <div class="modal-content" style="background: white; padding: 2rem; border-radius: 16px; width: 90%; max-width: 500px; max-height: 90vh; overflow-y: auto;">
        <span class="close-modal" id="close-editar-equipo" style="position: absolute; top: 1rem; right: 1rem; cursor: pointer; font-size: 1.5rem;">&times;</span>
        <h2 style="margin-bottom: 1.5rem;">Editar Equipo</h2>
        <form id="form-editar-equipo" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="edit-nombre">Nombre del Equipo</label>
                <input type="text" id="edit-nombre" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="edit-evento_id">Evento</label>
                <select id="edit-evento_id" name="evento_id" required>
                    @foreach($eventos as $evento)
                        <option value="{{ $evento->id }}">{{ $evento->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Project Info Fields -->
            <div style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px;">
                <h3 style="font-size: 1.1em; margin-bottom: 10px; color: #4b5563;">Información del Proyecto</h3>
                
                <div class="form-group">
                    <label for="edit-project_name">Nombre del Proyecto</label>
                    <input type="text" id="edit-project_name" name="project_name" placeholder="Nombre del proyecto">
                </div>

                <div class="form-group">
                    <label for="edit-project_description">Descripción</label>
                    <textarea id="edit-project_description" name="project_description" rows="3" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 8px;" placeholder="Descripción breve..."></textarea>
                </div>

                <div class="form-group">
                    <label for="edit-technologies">Tecnologías</label>
                    <input type="text" id="edit-technologies" name="technologies" placeholder="Ej: Laravel, Vue">
                </div>

                <div class="form-group">
                    <label for="edit-github_repo">Repositorio Git</label>
                    <input type="url" id="edit-github_repo" name="github_repo" placeholder="https://github.com/usuario/repo">
                </div>

                <div class="form-group">
                    <label for="edit-github_pages">GitHub Pages / Demo URL</label>
                    <input type="url" id="edit-github_pages" name="github_pages" placeholder="https://usuario.github.io/repo">
                </div>
            </div>
            <div class="form-group">
                <label>Participantes del Equipo</label>
                <div id="edit-members-list" style="border: 1px solid #d1d5db; border-radius: 8px; padding: 10px; max-height: 150px; overflow-y: auto;">
                    <!-- Members populated by JS -->
                </div>
            </div>
            <button type="submit" class="btn-submit">Actualizar Equipo</button>
        </form>

        <!-- Hidden form for removing members -->
        <form id="form-remove-member" method="POST" style="display: none;">
            @csrf
            <input type="hidden" name="user_id" id="remove-user-id">
        </form>
    </div>
</div>
