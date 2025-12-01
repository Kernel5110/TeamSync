@extends('layouts.app')

@section('title', 'Eventos - TeamSync')

@push('styles')
    @vite(['resources/css/eventos.css'])
@endpush

@section('content')
    <div class="contenedor-eventos">
        @foreach($eventos as $evento)
            <div class="tarjeta-evento">
                <div class="evento-header">
                    <div class="evento-titulo">
                        <div class="icono-evento {{ $loop->even ? 'icono-innovatec' : 'icono-hackatec' }}">
                            @if($loop->even)
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a10 10 0 1 0 10 10 10 10 0 0 0-10-10zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z"></path><path d="M12 6v6l4 2"></path></svg> <!-- Placeholder icon -->
                            @else
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path><path d="M4 22h16"></path><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"></path></svg>
                            @endif
                        </div>
                        <h2>{{ $evento->nombre }}</h2>
                    </div>
                    <span class="badge-proximo">Próximo</span>
                </div>

                <p class="evento-descripcion">
                    {{ $evento->descripcion }}
                </p>

                <div class="evento-detalles">
                    <div class="detalle-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        <span>{{ $evento->fecha_inicio->format('d') }}-{{ $evento->fecha_fin->format('d F Y') }}</span>
                    </div>
                    <div class="detalle-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        <span>{{ $evento->ubicacion }}</span>
                    </div>
                    <div class="detalle-item">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        <span>{{ $evento->capacidad }}</span>
                    </div>
                </div>

                <div class="evento-acciones">
                    <a href="#" class="btn-registrar">Registrar Equipo</a>
                    <a href="#" class="btn-detalles">Ver Detalles</a>
                </div>
            </div>
        @endforeach
    </div>

    <div class="contacto-seccion">
        <h2>¿No encuentras tu evento?</h2>
        <p>Contáctanos para agregar tu evento de innovación y tecnología</p>
        <a href="#" class="btn-contacto">Contactar Organizadores</a>
    </div>
@endsection
