@extends('layouts.app')

@section('title', 'Feedback de Evaluación - TeamSync')

@section('content')
<div class="container" style="max-width: 900px; margin: 0 auto; padding: 40px 20px;">
    <div style="margin-bottom: 30px;">
        <a href="{{ route('start') }}" style="color: #6b7280; text-decoration: none; display: inline-flex; align-items: center; font-weight: 500;">
            <x-icon name="arrow_back" style="margin-right: 5px; width: 18px;" /> Volver al Inicio
        </a>
    </div>

    <div class="header-section" style="text-align: center; margin-bottom: 40px;">
        <h1 style="font-size: 2rem; font-weight: 800; color: #1f2937; margin-bottom: 10px;">Resultados de Evaluación</h1>
        <p style="color: #6b7280; font-size: 1.1rem;">{{ $equipo->nombre }} - {{ $evento->nombre }}</p>
    </div>

    @if($evaluations->isEmpty())
        <div style="text-align: center; padding: 50px; background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
            <x-icon name="pending" style="font-size: 64px; color: #9ca3af; margin-bottom: 20px;" />
            <h3 style="font-size: 1.5rem; color: #374151; margin-bottom: 10px;">Sin Resultados Disponibles</h3>
            <p style="color: #6b7280;">Aún no hay evaluaciones finalizadas visibles para tu equipo.</p>
        </div>
    @else
        <div style="display: grid; gap: 30px;">
            @foreach($evaluations as $index => $eval)
                <div class="card" style="background: white; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); overflow: hidden;">
                    <div style="background-color: #f9fafb; padding: 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                        <h3 style="margin: 0; color: #1f2937; font-size: 1.1rem;">Juez #{{ $index + 1 }}</h3>
                        <span style="background-color: #d1fae5; color: #065f46; padding: 4px 10px; border-radius: 9999px; font-size: 0.8rem; font-weight: 600;">Evaluación Completada</span>
                    </div>
                    
                    <div style="padding: 25px;">
                        <div style="margin-bottom: 25px;">
                            <h4 style="color: #4b5563; margin-bottom: 15px; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.05em;">Puntajes</h4>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                                @foreach($eval->scores as $score)
                                    <div style="background-color: #f3f4f6; padding: 15px; border-radius: 8px; text-align: center;">
                                        <div style="font-size: 1.5rem; font-weight: 700; color: #4f46e5;">{{ $score->score }}/10</div>
                                        <div style="font-size: 0.85rem; color: #6b7280; margin-top: 5px;">{{ $score->criterion->name ?? 'Criterio' }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        @if($eval->comments)
                            <div>
                                <h4 style="color: #4b5563; margin-bottom: 10px; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.05em;">Comentarios</h4>
                                <div style="background-color: #fffbeb; border: 1px solid #fcd34d; padding: 15px; border-radius: 8px; color: #92400e; line-height: 1.6;">
                                    {{ $eval->comments }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
