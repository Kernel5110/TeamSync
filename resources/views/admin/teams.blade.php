@extends('layouts.app')

@section('title', 'Administrar Equipos - TeamSync')

@section('content')
<div class="container" style="max-width: 1000px; margin: 0 auto; padding: 40px 20px;">
    <h1 style="text-align: center; margin-bottom: 30px; color: #1f2937;">Administración de Equipos</h1>

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
                            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; color: #4b5563;">{{ $team->evento->nombre }}</td>
                            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; color: #4b5563;">
                                <ul style="margin: 0; padding-left: 20px;">
                                    @foreach($team->participantes as $participante)
                                        <li>
                                            {{ $participante->user->name }} 
                                            @if($participante->rol === 'Líder')
                                                <span style="background-color: #fef3c7; color: #d97706; padding: 2px 6px; border-radius: 4px; font-size: 0.7rem; font-weight: 600;">Líder</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid #e5e7eb;">
                                <form action="{{ route('team.delete', $team->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este equipo?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background-color: #ef4444; color: white; padding: 6px 12px; border: none; border-radius: 6px; font-size: 0.8rem; cursor: pointer;">Eliminar</button>
                                </form>
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
    </div>
</div>
@endsection
