@if(count($teams) > 0)
    <div class="teams-list">
        @foreach($teams as $team)
            <div class="team-item" style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="margin: 0 0 5px 0; color: #1f2937;">{{ $team->nombre }}</h3>
                    <p style="margin: 0; color: #6b7280; font-size: 0.9rem;">Evento: {{ $team->evento->nombre }}</p>
                    <p style="margin: 5px 0 0 0; color: #6b7280; font-size: 0.9rem;">Miembros: {{ $team->participantes->count() }}</p>
                </div>
                
                @php
                    $user = auth()->user();
                    $participante = $user->participante;
                    $hasTeam = $participante && $participante->equipo_id;
                    $pendingRequest = \App\Models\Solicitud::where('user_id', $user->id)
                        ->where('equipo_id', $team->id)
                        ->where('status', 'pending')
                        ->exists();
                    $isAdmin = $user->hasRole('admin');
                @endphp

                @if(!$isAdmin)
                    @if(!$hasTeam && !$pendingRequest)
                        <form action="{{ route('team.requestJoin', $team->id) }}" method="POST">
                            @csrf
                            <button type="submit" style="background-color: #10b981; color: white; padding: 8px 16px; border: none; border-radius: 6px; font-weight: 500; cursor: pointer;">Solicitar Unirse</button>
                        </form>
                    @elseif($pendingRequest)
                        <span style="background-color: #fef3c7; color: #d97706; padding: 6px 12px; border-radius: 6px; font-size: 0.9rem; font-weight: 500;">Solicitud Pendiente</span>
                    @elseif($hasTeam)
                        <span style="color: #9ca3af; font-size: 0.9rem;">Ya tienes equipo</span>
                    @endif
                @endif
            </div>
        @endforeach
    </div>
@else
    <p style="text-align: center; color: #6b7280; padding: 20px;">No se encontraron equipos con ese nombre.</p>
@endif
