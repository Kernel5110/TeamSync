@extends('layouts.app')

@section('title', 'Dashboard - TeamSync')

@section('content')
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 20px;">
        @role('admin')
            <h1 style="font-size: 2rem; font-weight: 700; color: #1f2937; margin-bottom: 20px;">Dashboard Administrativo</h1>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px;">
                <!-- Card Eventos -->
                <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border-left: 5px solid #3b82f6;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="color: #6b7280; font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Total Eventos</p>
                            <h2 style="font-size: 2.5rem; font-weight: 800; color: #1f2937; margin: 5px 0;">{{ $stats['eventos'] }}</h2>
                        </div>
                        <div style="background-color: #eff6ff; p-3; border-radius: 50%; padding: 12px;">
                            <x-icon name="event" style="font-size: 30px; color: #3b82f6;" />
                        </div>
                    </div>
                    <a href="{{ route('events.index') }}" style="display: inline-block; margin-top: 10px; color: #3b82f6; font-size: 0.9rem; text-decoration: none; font-weight: 500;">Ver todos &rarr;</a>
                </div>

                <!-- Card Usuarios -->
                <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border-left: 5px solid #10b981;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="color: #6b7280; font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Usuarios Registrados</p>
                            <h2 style="font-size: 2.5rem; font-weight: 800; color: #1f2937; margin: 5px 0;">{{ $stats['usuarios'] }}</h2>
                        </div>
                        <div style="background-color: #ecfdf5; p-3; border-radius: 50%; padding: 12px;">
                            <x-icon name="group" style="font-size: 30px; color: #10b981;" />
                        </div>
                    </div>
                    <a href="{{ route('admin.users') }}" style="display: inline-block; margin-top: 10px; color: #10b981; font-size: 0.9rem; text-decoration: none; font-weight: 500;">Gestionar usuarios &rarr;</a>
                </div>

                <!-- Card Equipos -->
                <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border-left: 5px solid #8b5cf6;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="color: #6b7280; font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Equipos Activos</p>
                            <h2 style="font-size: 2.5rem; font-weight: 800; color: #1f2937; margin: 5px 0;">{{ $stats['equipos'] }}</h2>
                        </div>
                        <div style="background-color: #f5f3ff; p-3; border-radius: 50%; padding: 12px;">
                            <x-icon name="groups" style="font-size: 30px; color: #8b5cf6;" />
                        </div>
                    </div>
                    <a href="{{ route('admin.teams') }}" style="display: inline-block; margin-top: 10px; color: #8b5cf6; font-size: 0.9rem; text-decoration: none; font-weight: 500;">Ver equipos &rarr;</a>
                </div>

                <!-- Card Jueces -->
                <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border-left: 5px solid #f59e0b;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="color: #6b7280; font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Jueces</p>
                            <h2 style="font-size: 2.5rem; font-weight: 800; color: #1f2937; margin: 5px 0;">{{ $stats['jueces'] }}</h2>
                        </div>
                        <div style="background-color: #fffbeb; p-3; border-radius: 50%; padding: 12px;">
                            <x-icon name="gavel" style="font-size: 30px; color: #f59e0b;" />
                        </div>
                    </div>
                    <a href="{{ route('admin.users') }}" style="display: inline-block; margin-top: 10px; color: #f59e0b; font-size: 0.9rem; text-decoration: none; font-weight: 500;">Ver jueces &rarr;</a>
                </div>

                
                <!-- Card Logs -->
                <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border-left: 5px solid #64748b;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="color: #6b7280; font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Auditoría</p>
                            <h2 style="font-size: 1.5rem; font-weight: 800; color: #1f2937; margin: 5px 0;">Logs</h2>
                        </div>
                        <div style="background-color: #f1f5f9; p-3; border-radius: 50%; padding: 12px;">
                            <x-icon name="history" style="font-size: 30px; color: #64748b;" />
                        </div>
                    </div>
                    <a href="{{ route('admin.logs') }}" style="display: inline-block; margin-top: 10px; color: #64748b; font-size: 0.9rem; text-decoration: none; font-weight: 500;">Ver historial &rarr;</a>
                </div>

                <!-- Card Configuración -->
                <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border-left: 5px solid #ef4444;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="color: #6b7280; font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Sistema</p>
                            <h2 style="font-size: 1.5rem; font-weight: 800; color: #1f2937; margin: 5px 0;">Ajustes</h2>
                        </div>
                        <div style="background-color: #fef2f2; p-3; border-radius: 50%; padding: 12px;">
                            <x-icon name="settings" style="font-size: 30px; color: #ef4444;" />
                        </div>
                    </div>
                    <a href="{{ route('admin.settings') }}" style="display: inline-block; margin-top: 10px; color: #ef4444; font-size: 0.9rem; text-decoration: none; font-weight: 500;">Configuración global &rarr;</a>
                </div>
            </div>

            <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                <h3 style="margin-bottom: 15px; color: #374151;">Accesos Rápidos</h3>
                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <a href="{{ route('events.index') }}" class="btn-confirmar" style="width: auto; background-color: #3b82f6;">
                        <x-icon name="add_circle" style="vertical-align: middle;" /> Gestionar Eventos
                    </a>
                    <a href="{{ route('admin.settings') }}" class="btn-confirmar" style="width: auto; background-color: #6b7280;">
                        <x-icon name="settings" style="vertical-align: middle;" /> Configuración
                    </a>
                </div>
            </div>
        @elseif(auth()->user()->hasRole('juez'))
            <div class="header-section" style="margin-bottom: 30px;">
                <h1 style="font-size: 2rem; font-weight: 700; color: #1f2937;">Panel de Juez</h1>
                <p style="color: #6b7280;">Gestiona tus evaluaciones y eventos asignados.</p>
            </div>

            @if($judgeEvents->count() > 0)
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px;">
                    @foreach($judgeEvents as $evento)
                        <div class="card" style="background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); overflow: hidden; transition: transform 0.2s;">
                            <div style="background: linear-gradient(to right, #4f46e5, #3b82f6); padding: 15px 20px; color: white;">
                                <h3 style="margin: 0; font-size: 1.2rem; font-weight: 600;">{{ $evento->nombre }}</h3>
                                <p style="margin: 5px 0 0; font-size: 0.9rem; opacity: 0.9;">{{ $evento->fecha_inicio }}</p>
                            </div>
                            <div style="padding: 20px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                    <span style="color: #6b7280; font-size: 0.9rem;">Equipos Asignados</span>
                                    <span style="font-weight: 700; color: #1f2937; font-size: 1.1rem;">{{ $evento->equipos->count() }}</span>
                                </div>
                                
                                @php
                                    $evaluatedCount = $evento->equipos->sum(function($equipo) {
                                        return $equipo->evaluations_count;
                                    });
                                    $progress = $evento->equipos->count() > 0 ? ($evaluatedCount / $evento->equipos->count()) * 100 : 0;
                                @endphp

                                <div style="margin-bottom: 20px;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 0.85rem;">
                                        <span style="color: #4b5563;">Progreso</span>
                                        <span style="color: #4f46e5; font-weight: 600;">{{ $evaluatedCount }} / {{ $evento->equipos->count() }}</span>
                                    </div>
                                    <div style="background-color: #e5e7eb; border-radius: 9999px; height: 8px; overflow: hidden;">
                                        <div style="background-color: #4f46e5; height: 100%; width: {{ $progress }}%;"></div>
                                    </div>
                                </div>

                                <a href="{{ route('events.evaluate.show', $evento->id) }}" style="display: block; width: 100%; padding: 10px; text-align: center; background-color: #f3f4f6; color: #374151; text-decoration: none; border-radius: 8px; font-weight: 600; transition: background 0.2s;">
                                    Continuar Evaluando &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 50px; background: white; border-radius: 12px; color: #6b7280;">
                    <x-icon name="assignment_late" style="font-size: 48px; margin-bottom: 15px; color: #9ca3af;" />
                    <p style="font-size: 1.1rem;">No tienes eventos asignados para evaluar en este momento.</p>
                </div>
            @endif

        @else
            <!-- Vista para usuarios normales (Participantes) -->
            <!-- Vista para usuarios normales (Participantes) -->
            <div class="header-section" style="margin-bottom: 30px;">
                <h1 style="font-size: 2rem; font-weight: 700; color: #1f2937;">Hola, {{ auth()->user()->name }} 👋</h1>
                <p style="color: #6b7280;">Bienvenido a tu panel de control.</p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px;">
                <!-- Card Mi Equipo -->
                <div class="card" style="background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); overflow: hidden;">
                    <div style="background: linear-gradient(to right, #10b981, #059669); padding: 20px; color: white;">
                        <h3 style="margin: 0; font-size: 1.2rem; font-weight: 600;">Mi Equipo</h3>
                    </div>
                    <div style="padding: 25px;">
                        @if($equipo)
                            <div style="text-align: center; margin-bottom: 20px;">
                                @if($equipo->logo_path)
                                    <img src="{{ asset('storage/' . $equipo->logo_path) }}" alt="Logo" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 10px; border: 3px solid #e5e7eb;">
                                @else
                                    <div style="width: 80px; height: 80px; border-radius: 50%; background-color: #d1fae5; color: #059669; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 2rem; margin: 0 auto 10px;">
                                        {{ strtoupper(substr($equipo->nombre, 0, 2)) }}
                                    </div>
                                @endif
                                <h2 style="margin: 0; color: #1f2937; font-size: 1.5rem;">{{ $equipo->nombre }}</h2>
                                <p style="color: #6b7280; margin: 5px 0;">{{ $equipo->participantes->count() }} Miembros</p>
                            </div>
                            <a href="{{ route('teams.index') }}" style="display: block; width: 100%; padding: 12px; text-align: center; background-color: #f3f4f6; color: #374151; text-decoration: none; border-radius: 8px; font-weight: 600; transition: background 0.2s;">
                                Gestionar Equipo
                            </a>
                        @else
                            <div style="text-align: center; padding: 20px 0;">
                                <p style="color: #6b7280; margin-bottom: 20px;">No perteneces a ningún equipo.</p>
                                <a href="{{ route('teams.index') }}" style="display: inline-block; padding: 10px 20px; background-color: #10b981; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                                    Unirse o Crear Equipo
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Card Evento Actual -->
                @if($equipo && $evento)
                    <div class="card" style="background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); overflow: hidden;">
                        <div style="background: linear-gradient(to right, #3b82f6, #2563eb); padding: 20px; color: white;">
                            <h3 style="margin: 0; font-size: 1.2rem; font-weight: 600;">Evento Actual</h3>
                        </div>
                        <div style="padding: 25px;">
                            <h2 style="margin: 0 0 10px; color: #1f2937; font-size: 1.3rem;">{{ $evento->nombre }}</h2>
                            <p style="color: #6b7280; margin-bottom: 20px;">
                                Estado: 
                                <span style="font-weight: 600; color: {{ $evento->status_manual == 'Finalizado' ? '#059669' : '#d97706' }}">
                                    {{ $evento->status_manual ?? 'En Curso' }}
                                </span>
                            </p>

                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                @if($evento->status_manual == 'Finalizado' || $equipo->evaluations()->whereNotNull('finalized_at')->exists())
                                    <a href="{{ route('my.feedback') }}" style="display: flex; align-items: center; justify-content: center; padding: 12px; background-color: #eff6ff; color: #2563eb; text-decoration: none; border-radius: 8px; font-weight: 600;">
                                        <x-icon name="visibility" style="margin-right: 8px;" /> Ver Resultados
                                    </a>
                                    
                                    <a href="{{ route('events.certificate.download', ['eventId' => $evento->id, 'teamId' => $equipo->id]) }}" style="display: flex; align-items: center; justify-content: center; padding: 12px; background-color: #fffbeb; color: #d97706; text-decoration: none; border-radius: 8px; font-weight: 600;">
                                        <x-icon name="card_membership" style="margin-right: 8px;" /> Descargar Certificado
                                    </a>
                                @else
                                    <div style="padding: 15px; background-color: #f3f4f6; border-radius: 8px; text-align: center; color: #6b7280; font-size: 0.9rem;">
                                        Los resultados estarán disponibles cuando finalice el evento.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endrole
    </div>
@endsection
