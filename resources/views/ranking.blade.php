@extends('layouts.app')

@section('title', 'Ranking del Evento - TeamSync')

@section('content')
<div class="container" style="max-width: 1000px; margin: 0 auto; padding: 40px 20px;">
    <div class="header-section" style="text-align: center; margin-bottom: 50px;">
        <h1 style="font-size: 2.5rem; font-weight: 800; color: #1f2937; margin-bottom: 10px;">Ranking de Ganadores</h1>
        <p style="color: #6b7280; font-size: 1.2rem;">{{ $evento->nombre }}</p>
    </div>

    @if($ranking->isEmpty())
        <div style="text-align: center; padding: 50px; background: white; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            <x-icon name="emoji_events" style="width: 96px; height: 96px; color: #9ca3af; margin-bottom: 20px;" />
            <h3 style="font-size: 1.5rem; color: #374151; margin-bottom: 10px;">Aún no hay resultados</h3>
            <p style="color: #6b7280;">Las evaluaciones aún no han comenzado o no hay equipos registrados.</p>
        </div>
    @else
        <!-- Top 3 Podium (Visual) -->
        @if($ranking->count() >= 3)
            <div class="podium-container" style="display: flex; justify-content: center; align-items: flex-end; gap: 20px; margin-bottom: 60px;">
                <!-- 2nd Place -->
                <div class="podium-item" style="text-align: center; width: 200px;">
                    <div style="margin-bottom: 10px; display: flex; justify-content: center;">
                        <x-icon name="emoji_events" style="width: 90px; height: 90px; color: #94a3b8;" />
                    </div>
                    <div style="background: white; padding: 20px; border-radius: 12px 12px 0 0; border-top: 4px solid #94a3b8; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); height: 180px; display: flex; flex-direction: column; justify-content: center;">
                        <h3 style="font-weight: 700; color: #1f2937; margin-bottom: 5px;">{{ $ranking[1]['equipo']->nombre }}</h3>
                        <p style="color: #64748b; font-weight: 600; font-size: 1.2rem;">{{ $ranking[1]['average_score'] }} pts</p>
                        <div style="margin-top: 10px; font-size: 0.9rem; color: #94a3b8;">2º Lugar</div>
                        <a href="{{ route('events.certificate', ['eventId' => $evento->id, 'teamId' => $ranking[1]['equipo']->id]) }}" target="_blank" style="margin-top: 10px; font-size: 0.8rem; color: #4f46e5; text-decoration: none; font-weight: 600;">Ver Certificado</a>
                    </div>
                </div>
                
                <!-- 1st Place -->
                <div class="podium-item" style="text-align: center; width: 220px;">
                    <div style="margin-bottom: 10px; display: flex; justify-content: center;">
                        <x-icon name="emoji_events" style="width: 120px; height: 120px; color: #fbbf24;" />
                    </div>
                    <div style="background: white; padding: 20px; border-radius: 12px 12px 0 0; border-top: 4px solid #fbbf24; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); height: 220px; display: flex; flex-direction: column; justify-content: center; position: relative; z-index: 10;">
                        <h3 style="font-weight: 800; color: #1f2937; margin-bottom: 5px; font-size: 1.3rem;">{{ $ranking[0]['equipo']->nombre }}</h3>
                        <p style="color: #d97706; font-weight: 700; font-size: 1.5rem;">{{ $ranking[0]['average_score'] }} pts</p>
                        <div style="margin-top: 10px; font-size: 1rem; color: #fbbf24; font-weight: 700;">1º Lugar</div>
                        <a href="{{ route('events.certificate', ['eventId' => $evento->id, 'teamId' => $ranking[0]['equipo']->id]) }}" target="_blank" style="margin-top: 15px; font-size: 0.9rem; color: #4f46e5; text-decoration: none; font-weight: 600; background-color: #e0e7ff; padding: 4px 10px; border-radius: 4px;">Ver Certificado</a>
                    </div>
                </div>

                <!-- 3rd Place -->
                <div class="podium-item" style="text-align: center; width: 200px;">
                    <div style="margin-bottom: 10px; display: flex; justify-content: center;">
                        <x-icon name="emoji_events" style="width: 90px; height: 90px; color: #b45309;" />
                    </div>
                    <div style="background: white; padding: 20px; border-radius: 12px 12px 0 0; border-top: 4px solid #b45309; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); height: 150px; display: flex; flex-direction: column; justify-content: center;">
                        <h3 style="font-weight: 700; color: #1f2937; margin-bottom: 5px;">{{ $ranking[2]['equipo']->nombre }}</h3>
                        <p style="color: #b45309; font-weight: 600; font-size: 1.2rem;">{{ $ranking[2]['average_score'] }} pts</p>
                        <div style="margin-top: 10px; font-size: 0.9rem; color: #b45309;">3º Lugar</div>
                        <a href="{{ route('events.certificate', ['eventId' => $evento->id, 'teamId' => $ranking[2]['equipo']->id]) }}" target="_blank" style="margin-top: 10px; font-size: 0.8rem; color: #4f46e5; text-decoration: none; font-weight: 600;">Ver Certificado</a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Full Ranking Table -->
        <div class="card" style="background: white; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                    <tr>
                        <th style="padding: 16px 24px; text-align: left; font-size: 0.85rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Posición</th>
                        <th style="padding: 16px 24px; text-align: left; font-size: 0.85rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Equipo</th>
                        <th style="padding: 16px 24px; text-align: left; font-size: 0.85rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Integrantes</th>
                        <th style="padding: 16px 24px; text-align: center; font-size: 0.85rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Evaluaciones</th>
                        <th style="padding: 16px 24px; text-align: right; font-size: 0.85rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Puntaje Promedio</th>
                        <th style="padding: 16px 24px; text-align: center; font-size: 0.85rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Constancia</th>
                    </tr>
                </thead>
                <tbody style="divide-y: 1px solid #e5e7eb;">
                    @foreach($ranking as $index => $item)
                        <tr style="transition: background-color 0.2s; hover: background-color: #f9fafb;">
                            <td style="padding: 16px 24px; white-space: nowrap;">
                                <div style="display: flex; align-items: center;">
                                    @if($index == 0)
                                        <x-icon name="emoji_events" style="color: #fbbf24; margin-right: 8px;" />
                                    @elseif($index == 1)
                                        <x-icon name="emoji_events" style="color: #94a3b8; margin-right: 8px;" />
                                    @elseif($index == 2)
                                        <x-icon name="emoji_events" style="color: #b45309; margin-right: 8px;" />
                                    @else
                                        <span style="font-weight: 600; color: #6b7280; width: 24px; text-align: center;">{{ $index + 1 }}</span>
                                    @endif
                                </div>
                            </td>
                            <td style="padding: 16px 24px;">
                                <div style="font-weight: 600; color: #1f2937;">{{ $item['equipo']->nombre }}</div>
                                <div style="font-size: 0.85rem; color: #6b7280;">{{ $item['equipo']->project_name ?? 'Sin nombre de proyecto' }}</div>
                            </td>
                            <td style="padding: 16px 24px;">
                                <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                    @foreach($item['equipo']->participantes as $participante)
                                        <span style="background-color: #f3f4f6; color: #4b5563; padding: 2px 8px; border-radius: 9999px; font-size: 0.75rem;">{{ $participante->user->name }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td style="padding: 16px 24px; text-align: center; color: #6b7280;">
                                {{ $item['evaluators_count'] }}
                            </td>
                            <td style="padding: 16px 24px; text-align: right;">
                                <span style="font-weight: 700; color: #4f46e5; font-size: 1.1rem;">{{ $item['average_score'] }}</span>
                                <span style="font-size: 0.85rem; color: #9ca3af;">/ 30</span>
                            </td>
                            <td style="padding: 16px 24px; text-align: center;">
                                <button onclick="openEmailModal('{{ route('events.certificate.email', ['eventId' => $evento->id, 'teamId' => $item['equipo']->id]) }}')" style="background-color: #4f46e5; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 0.85rem;">
                                    <x-icon name="email" style="width: 16px; height: 16px; vertical-align: middle;" /> Enviar
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- Email Modal -->
<div id="emailModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000;">
    <div style="background: white; padding: 24px; border-radius: 12px; width: 400px; max-width: 90%; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
        <h3 style="margin-top: 0; color: #1f2937;">Enviar Constancia por Correo</h3>
        <p style="color: #6b7280; font-size: 0.9rem; margin-bottom: 20px;">Ingresa el correo electrónico donde deseas recibir la constancia.</p>
        
        <form id="emailForm" action="" method="POST">
            @csrf
            <div style="margin-bottom: 16px;">
                <label for="email" style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 4px;">Correo Electrónico</label>
                <input type="email" name="email" id="email" required style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem;">
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeEmailModal()" style="background: white; border: 1px solid #d1d5db; color: #374151; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 500;">Cancelar</button>
                <button type="submit" style="background: #4f46e5; border: none; color: white; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: 500;">Enviar</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEmailModal(actionUrl) {
        document.getElementById('emailForm').action = actionUrl;
        document.getElementById('emailModal').style.display = 'flex';
    }

    function closeEmailModal() {
        document.getElementById('emailModal').style.display = 'none';
    }

    // Close modal when clicking outside
    document.getElementById('emailModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEmailModal();
        }
    });
</script>
@endsection
