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
                <div class="search-container" style="display: flex; gap: 5px; align-items: center;">
                    <input type="text" id="search-team-input" placeholder="Buscar equipo..." style="padding: 8px 15px; border: 1px solid #d1d5db; border-radius: 20px 0 0 20px; font-size: 0.9rem; width: 200px; outline: none;">
                    <button id="btn-search-team" style="padding: 8px 15px; background: #6366f1; border: none; border-radius: 0 20px 20px 0; cursor: pointer; color: white;" title="Buscar">
                        <x-icon name="search" style="font-size: 1.2rem;" />
                    </button>
                </div>
                <a href="#modal-crear-equipo" class="btn-nuevo">
                    <x-icon name="add" /> Nuevo Equipo
                </a>
            </div>
        </div>

        @if($myTeams && $myTeams->count() > 0)
            <div class="mis-equipos-list">
                @foreach($myTeams as $equipo)
                    @php
                        // Find the participant record for THIS team to check role
                        $myParticipant = $equipo->participants->where('user_id', auth()->id())->first();
                        $isLeader = $myParticipant && $myParticipant->rol == 'Líder';
                    @endphp
                    
                    <div class="equipo-detailed-card" style="background: white; border-radius: 16px; padding: 25px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); margin-bottom: 40px; border: 1px solid #f3f4f6;">
                        <div class="titulo-seccion" style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #f3f4f6;">
                            @if($equipo->logo_path)
                                <img src="{{ asset('storage/' . $equipo->logo_path) }}" alt="Logo {{ $equipo->nombre }}" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid #e5e7eb;">
                            @else
                                <div style="width: 60px; height: 60px; border-radius: 50%; background-color: #e0e7ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.5rem;">
                                    {{ strtoupper(substr($equipo->name, 0, 2)) }}
                                </div>
                            @endif
                            <div style="flex-grow: 1;">
                                <h2 style="margin: 0; font-size: 1.5rem; color: #1f2937;">{{ $equipo->name }}</h2>
                                @if($equipo->event)
                                    <span style="font-size: 0.9rem; background: #e0e7ff; color: #4338ca; padding: 4px 8px; border-radius: 6px; display: inline-block; margin-top: 5px;">
                                        Event: {{ $equipo->event->name }}
                                    </span>
                                @endif
                            </div>

                            @if($isLeader)
                                <div style="display: flex; gap: 10px;">
                                    <button type="button" class="btn-nuevo btn-agregar-miembro" data-id="{{ $equipo->id }}" style="padding: 8px 16px; font-size: 0.9rem;">
                                        <x-icon name="person_add" style="font-size: 18px;" /> Agregar
                                    </button>
                                    <button type="button" class="btn-editar-mi-equipo" data-id="{{ $equipo->id }}" 
                                            data-name="{{ $equipo->name }}"
                                            data-project-name="{{ $equipo->project_name }}"
                                            data-project-desc="{{ $equipo->project_description }}"
                                            data-repo="{{ $equipo->github_repo }}"
                                            data-pages="{{ $equipo->github_pages }}"
                                            data-technologies="{{ $equipo->technologies }}"
                                            style="padding: 8px; border-radius: 8px; border: 1px solid #d1d5db; background: white; cursor: pointer; color: #6b7280; transition: all 0.2s;" 
                                            title="Editar Equipo"
                                            onmouseover="this.style.borderColor='#4f46e5'; this.style.color='#4f46e5';"
                                            onmouseout="this.style.borderColor='#d1d5db'; this.style.color='#6b7280';">
                                        <x-icon name="edit" />
                                    </button>
                                </div>
                            @endif
                            
                            {{-- Leave Button for everyone (including leader) --}}
                            <form action="{{ route('teams.leave', $equipo->id) }}" method="POST" 
                                  onsubmit="return confirm('{{ $isLeader ? "Al salir, el liderazgo se transferirá al siguiente miembro más antiguo. Si no hay nadie más, el equipo se eliminará. ¿Continuar?" : "¿Estás seguro de que deseas salir de este equipo?" }}');" 
                                  style="margin-left: {{ $isLeader ? '10px' : 'auto' }};">
                                @csrf
                                <button type="submit" style="padding: 8px 16px; background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; border-radius: 8px; cursor: pointer; font-size: 0.9rem; transition: all 0.2s;"
                                        onmouseover="this.style.background='#fecaca';"
                                        onmouseout="this.style.background='#fee2e2';">
                                    <x-icon name="logout" style="font-size: 18px; vertical-align: middle;" /> Salir
                                </button>
                            </form>
                        </div>

                        <div class="miembros-section" style="margin-bottom: 25px;">
                            <h3 style="font-size: 1.1rem; color: #4b5563; margin-bottom: 15px;">Integrantes</h3>
                            <table class="tabla-miembros" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Institución</th>
                                        <th>Carrera</th>
                                        <th>Rol</th>
                                        @if($isLeader) <th>Acciones</th> @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($equipo->participants as $participante)
                                        <tr>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 10px;">
                                                    <div style="width: 32px; height: 32px; background: #f3f4f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 600; color: #6b7280;">
                                                        {{ strtoupper(substr($participante->user->name, 0, 1)) }}
                                                    </div>
                                                    {{ $participante->user->name }}
                                                </div>
                                            </td>
                                            <td>{{ $participante->institution }}</td>
                                            <td>{{ $participante->career->name }}</td>
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
                                            @if($isLeader)
                                                <td>
                                                    @if($participante->user_id !== auth()->id())
                                                        <form action="{{ route('teams.members.remove', $equipo->id) }}" method="POST" onsubmit="return confirm('¿Eliminar a {{ $participante->user->name }}?');" style="display:inline;">
                                                            @csrf
                                                            <input type="hidden" name="user_id" value="{{ $participante->user_id }}">
                                                            <button type="submit" style="border: none; background: #fee2e2; color: #ef4444; cursor: pointer; padding: 6px; border-radius: full; display: flex; align-items: center; justify-content: center; transition: background 0.2s;" title="Eliminar Miembro" onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="stats-grid">
                            <div class="stat-card blue">
                                <div class="stat-number">{{ $equipo->participants->count() }}</div>
                                <div class="stat-label">Total Miembros</div>
                            </div>
                            <div class="stat-card cyan">
                                <div class="stat-number">{{ $equipo->evaluations->count() }}</div>
                                <div class="stat-label">Evaluaciones</div>
                            </div>
                            <div class="stat-card green">
                                <div class="stat-number">{{ $equipo->project_name ? '1' : '0' }}</div>
                                <div class="stat-label">Proyectos Info</div>
                            </div>
                             <div class="stat-card orange">
                                <div class="stat-number">{{ (int)$equipo->total_score }}</div>
                                <div class="stat-label">Puntaje Total</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            @unlessrole('admin')
                @if($myPendingRequest)
                     <div class="tarjeta-miembros" style="text-align: center; padding: 3rem;">
                        <x-icon name="pending" style="font-size: 4rem; color: #f59e0b;" />
                        <h2 style="margin-top: 1rem; color: #374151;">Solicitud Enviada</h2>
                        <p style="color: #6b7280; margin-bottom: 2rem;">Has solicitado unirte al equipo <strong>{{ $myPendingRequest->team->name }}</strong>. Espera a que el líder acepte tu solicitud.</p>
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

        {{-- Invitations Section for Users --}}
        @if(isset($myInvitations) && count($myInvitations) > 0)
            <div class="tarjeta-miembros" style="margin-top: 2rem; border-left: 4px solid #10b981;">
                <div class="titulo-seccion" style="display: flex; align-items: center; gap: 10px;">
                    <x-icon name="mail" style="color: #10b981;" />
                    Invitaciones de Equipos
                </div>
                <p style="margin-bottom: 15px; color: #4b5563;">Has sido invitado a unirte a los siguientes equipos:</p>
                <table class="tabla-miembros">
                    <thead>
                        <tr>
                            <th>Equipo</th>
                            <th>Evento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($myInvitations as $invitation)
                            <tr>
                                <td style="font-weight: 600;">{{ $invitation->team->name }}</td>
                                <td>{{ $invitation->team->event->name ?? 'N/A' }}</td>
                                <td>
                                    <div style="display: flex; gap: 10px;">
                                        <form action="{{ route('invitations.accept', $invitation->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn-nuevo" style="background: #10b981; padding: 5px 15px; font-size: 0.9rem;">
                                                Aceptar
                                            </button>
                                        </form>
                                        <form action="{{ route('invitations.reject', $invitation->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn-nuevo" style="background: #ef4444; padding: 5px 15px; font-size: 0.9rem;">
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
                                <td>{{ $request->user->participant->institution ?? 'N/A' }}</td>
                                <td>{{ $request->user->participant->career->name ?? 'N/A' }}</td>
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
            <div class="section-divider" style="margin-top: 60px; margin-bottom: 20px; border-top: 1px solid #e5e7eb;"></div>
            <div class="otros-equipos-container">
                <div class="titulo-seccion" style="font-size: 1.5rem; color: #111827; margin-bottom: 20px;">Otros Equipos Disponibles</div>
                <div class="grid-equipos" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                    @foreach($otherTeams as $team)
                        <div class="card-equipo" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                                <div>
                                    <h3 style="margin: 0; font-size: 1.2rem; color: #111827;">{{ $team->name }}</h3>
                                    <span style="font-size: 0.9rem; color: #6b7280;">{{ $team->event->name ?? 'Sin Evento' }}</span>
                                </div>
                                <span class="badge-rol" style="background: #e0e7ff; color: #4338ca;">
                                    {{ $team->participants->count() }} Miembros
                                </span>
                            </div>
                            
                            <div style="margin-bottom: 15px;">
                                <p style="margin: 0; font-size: 0.9rem; color: #4b5563;">Líder: 
                                    @php
                                        $lider = $team->participants->where('rol', 'Líder')->first();
                                    @endphp
                                    {{ $lider ? $lider->user->name : 'N/A' }}
                                </p>
                            </div>

                            @php
                                $alreadyInEvent = $myTeams ? $myTeams->contains('event_id', $team->event_id) : false;
                                // Check specific request for this team if myPendingRequest is single, 
                                // ideally this should be a collection check but adhering to current controller data passed.
                                $isPending = $myPendingRequest && $myPendingRequest->team_id == $team->id;
                            @endphp

                            @if(!$alreadyInEvent && !$isPending && !Auth::user()->hasRole('admin'))
                                <form action="{{ route('teams.join', $team->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-nuevo" style="width: 100%; justify-content: center; background: #4f46e5;">
                                        Solicitar Unirse
                                    </button>
                                </form>
                            @elseif($isPending)
                                <button disabled class="btn-nuevo" style="width: 100%; justify-content: center; background: #9ca3af; cursor: not-allowed;">
                                    Solicitud Enviada
                                </button>
                            @elseif($alreadyInEvent)
                                <div style="text-align: center; color: #059669; font-size: 0.9rem; padding: 8px;">
                                    <x-icon name="check_circle" style="font-size: 16px; vertical-align: text-bottom;" /> Ya participas en este evento
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div style="margin-top: 20px; display: flex; justify-content: center;">
                    {{ $otherTeams->appends(['all_teams_page' => $allTeams ? $allTeams->currentPage() : 1])->links() }}
                </div>
            </div>
        @endif

    @role('admin')
        <div class="admin-teams-section" style="margin-top: 60px;">
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
                                    <td>{{ $t->name }}</td>
                                    <td>{{ $t->event->name ?? 'N/A' }}</td>
                                    <td>
                                        <ul style="margin: 0; padding-left: 20px; font-size: 0.9rem;">
                                            @foreach($t->participants as $p)
                                                <li>{{ $p->user->name }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>
                                        <button class="btn-editar-equipo"
                                                data-id="{{ $t->id }}"
                                                data-name="{{ $t->name }}"
                                                data-event-id="{{ $t->event_id }}"
                                                data-project-name="{{ $t->project_name }}"
                                                data-project-description="{{ $t->project_description }}"
                                                data-technologies="{{ $t->technologies }}"
                                                data-github-repo="{{ $t->github_repo }}"
                                                data-github-pages="{{ $t->github_pages }}"
                                                data-members="{{ $t->participants->map(function($p) { return ['id' => $p->user->id, 'name' => $p->user->name, 'rol' => $p->rol]; })->toJson() }}"
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
                        {{ $allTeams->appends(['other_teams_page' => $otherTeams ? $otherTeams->currentPage() : 1])->links() }}
                    </div>
                </div>
            @else
                <p>No hay equipos registrados en el sistema.</p>
            @endif
        </div>
    @endrole
    </div> {{-- Close contenedor-equipo --}}

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
                    <label for="edit-event_id">Evento</label>
                    <select id="edit-event_id" name="event_id" required>
                        @foreach($eventos as $evento)
                            <option value="{{ $evento->id }}">{{ $evento->name }}</option>
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
            const selectEvento = document.getElementById('edit-event_id');
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
                        const nombre = this.getAttribute('data-name');
                        const eventoId = this.getAttribute('data-event-id');
                        
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
                <!-- Logo field removed as requested -->
                <div class="form-group">
                    <label for="event_id">Evento</label>
                    <select id="event_id" name="event_id" required>
                        @foreach($eventos as $evento)
                            <option value="{{ $evento->id }}">{{ $evento->name }}</option>
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
            <form id="form-editar-mi-equipo" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="event_id" value="{{ $equipo->event_id }}">
                <div class="form-group">
                    <label for="mi-equipo-nombre">Nombre del Equipo</label>
                    <input type="text" id="mi-equipo-nombre" name="nombre" value="{{ $equipo->name }}" required>
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
            <form id="form-agregar-miembro" action="{{ route('teams.members.add') }}" method="POST">
                @csrf
                <input type="hidden" name="team_id" id="add-member-team-id">
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
            const searchBtn = document.getElementById('btn-search-team');
            const modalBuscar = document.getElementById('modal-buscar-equipo');
            const closeBuscar = document.getElementById('close-buscar-equipo');
            const resultsContainer = document.getElementById('search-results-container');

            function performSearch() {
                const query = searchInput.value.trim();

                if (query.length > 0) {
                    modalBuscar.style.display = 'flex';
                    resultsContainer.innerHTML = '<p style="text-align: center; padding: 20px;">Buscando...</p>';

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
                }
            }

            if (searchBtn && searchInput) {
                // Button Click
                searchBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    performSearch();
                });

                // Enter Key
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        performSearch();
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
            const btnsEditarMiEquipo = document.querySelectorAll('.btn-editar-mi-equipo');
            const modalEditarMiEquipo = document.getElementById('modal-editar-mi-equipo');
            const closeEditarMiEquipo = document.getElementById('close-editar-mi-equipo');
            const formEditarMiEquipo = document.getElementById('form-editar-mi-equipo');
            // Fields
            const inputMiNombre = document.getElementById('mi-equipo-nombre');
            const inputMiEventId = document.getElementsByName('event_id')[0]; // Assuming name is unique in this form context or use ID
            // Note: The edit modal for leader currently relies on fields. 
            // We need to populate them.
            
            btnsEditarMiEquipo.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    const project = this.dataset.projectName || '';
                    const desc = this.dataset.projectDesc || '';
                    const repo = this.dataset.repo || '';
                    const pages = this.dataset.pages || '';
                    const tech = this.dataset.technologies || '';

                    // Update Form Action
                    formEditarMiEquipo.action = `/teams/${id}`;
                    
                    // Populate Fields
                    if(inputMiNombre) inputMiNombre.value = name;
                    
                    // Populate Project Fields if they exist in the modal
                    const pName = document.getElementById('edit-project_name');
                    if(pName) pName.value = project;
                    const pDesc = document.getElementById('edit-project_description');
                    if(pDesc) pDesc.value = desc;
                    const pRepo = document.getElementById('edit-github_repo');
                    if(pRepo) pRepo.value = repo;
                    const pTech = document.getElementById('edit-technologies');
                    if(pTech) pTech.value = tech;


                    modalEditarMiEquipo.style.display = 'flex';
                });
            });

            if (closeEditarMiEquipo) {
                closeEditarMiEquipo.addEventListener('click', function() {
                    modalEditarMiEquipo.style.display = 'none';
                });
            }

            // Add Member Logic
            const btnsAgregar = document.querySelectorAll('.btn-agregar-miembro');
            const modalAgregar = document.getElementById('modal-agregar-miembro');
            const inputTeamId = document.getElementById('add-member-team-id');

            btnsAgregar.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.dataset.id;
                    inputTeamId.value = id;
                    modalAgregar.style.display = 'flex';
                });
            });

            // Close logic for add member
            const closeAgregar = modalAgregar.querySelector('.close-modal');
            if(closeAgregar) {
                closeAgregar.addEventListener('click', function(e) {
                    e.preventDefault();
                    modalAgregar.style.display = 'none';
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
                <label for="edit-event_id">Evento</label>
                <select id="edit-event_id" name="event_id" required>
                    @foreach($eventos as $evento)
                        <option value="{{ $evento->id }}">{{ $evento->name }}</option>
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
