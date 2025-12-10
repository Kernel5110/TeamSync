@extends('layouts.app')

@section('title', 'Administrar Usuarios - TeamSync')

@section('content')
<div class="container" style="max-width: 1000px; margin: 0 auto; padding: 40px 20px;">
    <h1 style="text-align: center; margin-bottom: 30px; color: #1f2937;">Administración de Usuarios</h1>

    @if(session('success'))
        <div style="background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border: 1px solid #a7f3d0;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background-color: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border: 1px solid #fecaca;">
            {{ session('error') }}
        </div>
    @endif

    <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); margin-bottom: 30px;">
        <h2 style="margin-bottom: 20px; font-size: 1.25rem; color: #374151;">Crear Nuevo Usuario (Admin/Juez)</h2>
        <form action="{{ route('admin.users.store') }}" method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
            @csrf
            <div>
                <label for="name" style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 5px;">Nombre</label>
                <input type="text" name="name" id="name" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;">
            </div>
            <div>
                <label for="email" style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 5px;">Email</label>
                <input type="email" name="email" id="email" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;">
            </div>
            <div>
                <label for="password" style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 5px;">Contraseña</label>
                <input type="password" name="password" id="password" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px;">
            </div>
            <div>
                <label for="role" style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 5px;">Rol</label>
                <select name="role" id="role" required style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; background-color: white;">
                    <option value="juez">Juez</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>
            <button type="submit" style="background-color: #4f46e5; color: white; padding: 10px 20px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; height: 42px;">Crear Usuario</button>
        </form>
    </div>

    <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        <form action="{{ route('admin.users') }}" method="GET" style="display: flex; gap: 10px; margin-bottom: 30px;">
            <input type="text" name="query" value="{{ $query ?? '' }}" placeholder="Buscar por nombre o email..." style="flex: 1; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 1rem;">
            <button type="submit" style="background-color: #4f46e5; color: white; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">Buscar</button>
        </form>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                <thead>
                    <tr style="background-color: #f9fafb; text-align: left;">
                        <th style="padding: 12px; border-bottom: 2px solid #e5e7eb; color: #4b5563;">Nombre</th>
                        <th style="padding: 12px; border-bottom: 2px solid #e5e7eb; color: #4b5563;">Email</th>
                        <th style="padding: 12px; border-bottom: 2px solid #e5e7eb; color: #4b5563;">Roles</th>
                        <th style="padding: 12px; border-bottom: 2px solid #e5e7eb; color: #4b5563;">Experiencia</th>
                        <th style="padding: 12px; border-bottom: 2px solid #e5e7eb; color: #4b5563;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; font-weight: 600; color: #1f2937;">{{ $user->name }}</td>
                            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; color: #4b5563;">{{ $user->email }}</td>
                            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; color: #4b5563;">
                                @foreach($user->roles as $role)
                                    <span style="background-color: #e0e7ff; color: #4338ca; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; margin-right: 5px;">
                                        {{ ucfirst($role->name) }}
                                    </span>
                                @endforeach
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; color: #4b5563; font-size: 0.85rem;">
                                {{ $user->expertise ?? '-' }}
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb;">
                                @if(auth()->id() !== $user->id)
                                    <button class="btn-editar-usuario"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"
                                            data-email="{{ $user->email }}"
                                            data-role="{{ $user->roles->first() ? $user->roles->first()->name : 'participante' }}"
                                            title="Editar Usuario"
                                            style="background-color: #3b82f6; color: white; width: 32px; height: 32px; border: none; border-radius: 6px; cursor: pointer; margin-right: 5px; display: inline-flex; align-items: center; justify-content: center;">
                                        <x-icon name="edit" style="width: 18px; height: 18px;" />
                                    </button>
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Eliminar Usuario" style="background-color: #ef4444; color: white; width: 32px; height: 32px; border: none; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center;">
                                            <x-icon name="delete" style="width: 18px; height: 18px;" />
                                        </button>
                                    </form>
                                @else
                                    <span style="color: #9ca3af; font-size: 0.8rem; font-style: italic;">Tu cuenta</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="padding: 20px; text-align: center; color: #6b7280;">No se encontraron usuarios.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $users->links('pagination::simple-tailwind') }}
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="modal-editar-usuario" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000;">
        <div class="modal-content" style="background-color: white; padding: 20px; border-radius: 8px; width: 90%; max-width: 500px; position: relative;">
            <span class="close-modal" id="close-editar-usuario" style="position: absolute; top: 10px; right: 15px; font-size: 24px; cursor: pointer;">&times;</span>
            <h2 style="margin-bottom: 20px;">Editar Usuario</h2>
            <form id="form-editar-usuario" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="edit-name" style="display: block; margin-bottom: 5px; font-weight: 600;">Nombre</label>
                    <input type="text" id="edit-name" name="name" required style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="edit-email" style="display: block; margin-bottom: 5px; font-weight: 600;">Email</label>
                    <input type="email" id="edit-email" name="email" required style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                </div>
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="edit-role" style="display: block; margin-bottom: 5px; font-weight: 600;">Rol</label>
                    <select id="edit-role" name="role" required style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
                        <option value="participante">Participante</option>
                        <option value="juez">Juez</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <button type="submit" style="background-color: #4f46e5; color: white; padding: 10px 20px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; width: 100%;">Guardar Cambios</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('modal-editar-usuario');
            const closeBtn = document.getElementById('close-editar-usuario');
            const form = document.getElementById('form-editar-usuario');
            const nameInput = document.getElementById('edit-name');
            const emailInput = document.getElementById('edit-email');
            const roleSelect = document.getElementById('edit-role');
            const editBtns = document.querySelectorAll('.btn-editar-usuario');

            editBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    const email = this.getAttribute('data-email');
                    const role = this.getAttribute('data-role');

                    form.action = "/admin/users/" + id;
                    nameInput.value = name;
                    emailInput.value = email;
                    roleSelect.value = role;

                    modal.style.display = 'flex';
                });
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
