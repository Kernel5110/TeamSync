@extends('layouts.app')

@section('title', 'Configuración - Admin')

@section('content')
<div class="contenedor-eventos" style="max-width: 1000px;">
    <div class="evento-header">
        <div class="evento-titulo">
            <x-icon name="settings" style="font-size: 2rem; color: #4f46e5;" />
            <h2>Configuración del Sistema</h2>
        </div>
        <a href="{{ route('start') }}" class="btn-detalles" style="padding: 8px 16px;">Volver al Inicio</a>
    </div>

    @if(session('success'))
        <div style="background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border: 1px solid #a7f3d0;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background-color: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; border: 1px solid #fca5a5;">
            <ul style="list-style: disc; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr; gap: 2rem; margin-bottom: 2rem;">
        <div class="tarjeta-evento" style="border-top: 4px solid #f59e0b;">
            <h3 style="font-size: 1.25rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem; display: flex; align-items: center; gap: 8px;">
                <x-icon name="tune" /> Configuración Global
            </h3>
            
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="maintenance_mode" value="1" {{ \App\Models\Setting::where('key', 'maintenance_mode')->value('value') == '1' ? 'checked' : '' }} style="width: 20px; height: 20px;">
                        <span style="font-weight: 500; color: #374151;">Modo Mantenimiento (Solo Admins pueden acceder)</span>
                    </label>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-weight: 500; color: #374151; margin-bottom: 5px;">Mensaje de Bienvenida</label>
                    <input type="text" name="welcome_message" value="{{ \App\Models\Setting::where('key', 'welcome_message')->value('value') }}" style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 6px;">
                </div>

                <button type="submit" class="btn-confirmar" style="width: auto; padding: 8px 16px; font-size: 0.9rem; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">Guardar Configuración</button>
            </form>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <!-- Instituciones Section -->
        <div class="tarjeta-evento" style="border-top: 4px solid #3b82f6;">
            <h3 style="font-size: 1.25rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem; display: flex; align-items: center; gap: 8px;">
                <x-icon name="school" /> Instituciones
            </h3>
            
            <form action="{{ route('admin.instituciones.store') }}" method="POST" style="margin-bottom: 1.5rem; display: flex; gap: 10px;">
                @csrf
                <input type="text" name="nombre" placeholder="Nueva Institución" required style="flex: 1; padding: 8px; border: 1px solid #d1d5db; border-radius: 6px;">
                <button type="submit" class="btn-confirmar" style="width: auto; padding: 8px 16px; font-size: 0.9rem;">Agregar</button>
            </form>

            <ul style="list-style: none; padding: 0; max-height: 300px; overflow-y: auto;">
                @foreach($instituciones as $inst)
                    <li style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f3f4f6;">
                        <span style="color: #4b5563;">{{ $inst->nombre }}</span>
                        <form action="{{ route('admin.instituciones.destroy', $inst->id) }}" method="POST" onsubmit="return confirm('¿Eliminar institución?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer;">
                                <x-icon name="delete" style="font-size: 1.2rem;" />
                            </button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Carreras Section -->
        <div class="tarjeta-evento" style="border-top: 4px solid #10b981;">
            <h3 style="font-size: 1.25rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem; display: flex; align-items: center; gap: 8px;">
                <x-icon name="menu_book" /> Carreras
            </h3>

            <form action="{{ route('admin.carreras.store') }}" method="POST" style="margin-bottom: 1.5rem; display: flex; gap: 10px;">
                @csrf
                <input type="text" name="nombre" placeholder="Nueva Carrera" required style="flex: 1; padding: 8px; border: 1px solid #d1d5db; border-radius: 6px;">
                <button type="submit" class="btn-confirmar" style="width: auto; padding: 8px 16px; font-size: 0.9rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">Agregar</button>
            </form>

            <ul style="list-style: none; padding: 0; max-height: 300px; overflow-y: auto;">
                @foreach($carreras as $carrera)
                    <li style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f3f4f6;">
                        <span style="color: #4b5563;">{{ $carrera->nombre }}</span>
                        <form action="{{ route('admin.carreras.destroy', $carrera->id) }}" method="POST" onsubmit="return confirm('¿Eliminar carrera?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer;">
                                <x-icon name="delete" style="font-size: 1.2rem;" />
                            </button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
