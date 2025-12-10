@extends('layouts.app')

@section('title', 'Administrar Equipos - TeamSync')

@section('content')
    <div class="container" style="max-width: 1000px; margin: 0 auto; padding: 40px 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="color: #1f2937; margin: 0;">Administración de Equipos</h1>
        <a href="{{ route('admin.users') }}" style="background-color: #4f46e5; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600;">Administrar Usuarios</a>
    </div>

    <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        <form action="{{ route('admin.teams') }}" method="GET" style="display: flex; gap: 10px; margin-bottom: 30px;">
            <input type="text" name="query" value="{{ $query ?? '' }}" placeholder="Buscar por equipo o evento..." style="flex: 1; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 1rem;">
            <button type="submit" style="background-color: #4f46e5; color: white; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">Buscar</button>
        </form>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                <thead>
                    <tr style="background-color: #f9fafb; text-align: left;">
                        <th style="padding: 12px; border-bottom: 2px solid #e5e7eb; color: #4b5563;">Equipo</th>
                        <th style="padding: 12px; border-bottom: 2px solid #e5e7eb; color: #4b5563;">Evento</th>
                        <th style="padding: 12px; border-bottom: 2px solid #e5e7eb; color: #4b5563;">Miembros</th>
                        <th style="padding: 12px; border-bottom: 2px solid #e5e7eb; color: #4b5563;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teams as $team)
                        <tr>
                            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; font-weight: 600; color: #1f2937;">{{ $team->nombre }}</td>
                            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; color: #4b5563;">{{ $team->event->nombre }}</td>
                            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; color: #4b5563;">
                                <ul style="margin: 0; padding-left: 20px;">
                                    @foreach($team->participants as $participante)
                                        <li>
                                            {{ $participante->user->name }} 
                                            @if($participante->rol === 'Líder')
                                                <span style="background-color: #fef3c7; color: #d97706; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; font-weight: 600;">Líder</span>
                                            @else
                                                <form action="{{ route('teams.members.remove', $team->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('¿Expulsar miembro?');">
                                                    @csrf
                                                    <input type="hidden" name="user_id" value="{{ $participante->user->id }}">
                                                    <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 0.8rem;">(Expulsar)</button>
                                                </form>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb;">
                                <form action="{{ route('teams.destroy', $team->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este equipo?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background-color: #ef4444; color: white; padding: 6px 12px; border: none; border-radius: 6px; font-size: 0.8rem; cursor: pointer;">Eliminar</button>
                                </form>
                                <button class="btn-editar-equipo" 
                                        data-id="{{ $team->id }}" 
                                        data-nombre="{{ $team->nombre }}" 
                                        data-evento-id="{{ $team->event_id }}"
                                        style="background-color: #3b82f6; color: white; padding: 6px 12px; border: none; border-radius: 6px; font-size: 0.8rem; cursor: pointer; margin-left: 5px;">
                                    Editar
                                </button>
                                <button class="btn-cambiar-lider" 
                                        data-id="{{ $team->id }}" 
                                        data-members="{{ json_encode($team->participants->map(function($p){ return ['id' => $p->user->id, 'name' => $p->user->name]; })) }}"
                                        style="background-color: #8b5cf6; color: white; padding: 6px 12px; border: none; border-radius: 6px; font-size: 0.8rem; cursor: pointer; margin-left: 5px;">
                                    Líder
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding: 20px; text-align: center; color: #6b7280;">No se encontraron equipos.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $teams->links('pagination::simple-tailwind') }}
        </div>
    </div>

    <!-- Edit Team Modal -->
    <div id="modal-editar-equipo" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000;">
        <div class="modal-content" style="background-color: white; padding: 20px; border-radius: 8px; width: 90%; max-width: 500px; position: relative;">
            <span class="close-modal" id="close-editar-equipo" style="position: absolute; top: 10px; right: 15px; font-size: 24px; cursor: pointer;">&times;</span>
            <h2 style="margin-bottom: 20px;">Editar Equipo</h2>
            <form id="form-editar-equipo" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="edit-nombre" style="display: block; margin-bottom: 5px; font-weight: 600;">Nombre del Equipo</label>
                    <input type="text" id="edit-nombre" name="nombre" required style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="edit-evento" style="display: block; margin-bottom: 5px; font-weight: 600;">Evento</label>
                    <select id="edit-evento" name="evento_id" required style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                        @foreach(\App\Models\Event::all() as $evento)
                            <option value="{{ $evento->id }}">{{ $evento->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" style="background-color: #4f46e5; color: white; padding: 10px 20px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; width: 100%;">Guardar Cambios</button>
            </form>
        </div>
    </div>

    <!-- Change Leader Modal -->
    <div id="modal-cambiar-lider" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000;">
        <div class="modal-content" style="background-color: white; padding: 20px; border-radius: 8px; width: 90%; max-width: 500px; position: relative;">
            <span class="close-modal" id="close-cambiar-lider" style="position: absolute; top: 10px; right: 15px; font-size: 24px; cursor: pointer;">&times;</span>
            <h2 style="margin-bottom: 20px;">Cambiar Líder</h2>
            <form id="form-cambiar-lider" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="new-leader-select" style="display: block; margin-bottom: 5px; font-weight: 600;">Nuevo Líder</label>
                    <select id="new-leader-select" name="new_leader_id" required style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                        <!-- Options populated by JS -->
                    </select>
                </div>
                <button type="submit" style="background-color: #8b5cf6; color: white; padding: 10px 20px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; width: 100%;">Guardar Cambios</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('modal-editar-equipo');
            const closeBtn = document.getElementById('close-editar-equipo');
            const form = document.getElementById('form-editar-equipo');
            const nombreInput = document.getElementById('edit-nombre');
            const eventoSelect = document.getElementById('edit-evento');
            const editBtns = document.querySelectorAll('.btn-editar-equipo');

            editBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault(); // Prevent default behavior just in case
                    console.log('Edit button clicked');
                    const id = this.getAttribute('data-id');
                    const nombre = this.getAttribute('data-nombre');
                    const eventoId = this.getAttribute('data-evento-id');

                    console.log('ID:', id, 'Nombre:', nombre, 'EventoID:', eventoId);

                    form.action = "/teams/" + id; 
                    nombreInput.value = nombre;
                    eventoSelect.value = eventoId;

                    modal.style.display = 'flex';
                });
            });

            // Change Leader Modal Logic
            const modalLider = document.getElementById('modal-cambiar-lider');
            const closeBtnLider = document.getElementById('close-cambiar-lider');
            const formLider = document.getElementById('form-cambiar-lider');
            const leaderSelect = document.getElementById('new-leader-select');
            const changeLeaderBtns = document.querySelectorAll('.btn-cambiar-lider');

            changeLeaderBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');
                    const members = JSON.parse(this.getAttribute('data-members'));

                    formLider.action = "/teams/" + id + "/leader";
                    leaderSelect.innerHTML = '';
                    
                    members.forEach(member => {
                        const option = document.createElement('option');
                        option.value = member.id;
                        option.textContent = member.name;
                        leaderSelect.appendChild(option);
                    });

                    modalLider.style.display = 'flex';
                });
            });

            closeBtnLider.addEventListener('click', function() {
                modalLider.style.display = 'none';
            });

            window.addEventListener('click', function(e) {
                if (e.target == modalLider) {
                    modalLider.style.display = 'none';
                }
            });

            closeBtn.addEventListener('click', function() {
                modal.style.display = 'none';
            });

            window.addEventListener('click', function(e) {
                if (e.target == modal) {
                    modal.style.display = 'none';
                }
            });
        });
    </script>
</div>
@endsection
