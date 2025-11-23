@extends('layouts.app')

@section('title', 'Comenzar - TeamSync')
@section('meta_description', 'Comienza tu viaje en TeamSync. Configura tu perfil y únete a la comunidad de innovadores.')

@push('styles')
    @vite(['resources/css/start.css'])
@endpush

@section('content')
    <div class="start-container">
        <div class="start-content">
            <div class="start-header">
                <h1>¡Comienza tu Aventura!</h1>
                <p>Únete a TeamSync en 3 simples pasos y empieza a colaborar con innovadores de todo el mundo</p>
            </div>

            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <h3>Crea tu Cuenta</h3>
                    <p>Regístrate con tu correo electrónico y completa tu perfil con tus habilidades y especialidades</p>
                </div>

                <div class="step-card">
                    <div class="step-number">2</div>
                    <h3>Explora Eventos</h3>
                    <p>Descubre competencias de innovación como Inovatec, Hackatec y más eventos tecnológicos</p>
                </div>

                <div class="step-card">
                    <div class="step-number">3</div>
                    <h3>Forma tu Equipo</h3>
                    <p>Únete a equipos existentes o crea el tuyo propio con estudiantes de diferentes especialidades</p>
                </div>
            </div>

            <div class="start-actions">
                <a href="#" class="start-btn">Crear Cuenta Gratis</a>
                <a href="{{ route('login') }}" class="secondary-btn">Ya tengo cuenta</a>
            </div>

            <div class="features-list">
                <h2>¿Qué obtienes con TeamSync?</h2>
                <div class="features-grid">
                    <div class="feature-item">
                        <span class="feature-icon">✨</span>
                        <span class="feature-text">Acceso a eventos exclusivos de innovación</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">👥</span>
                        <span class="feature-text">Conexión con estudiantes talentosos</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">🎯</span>
                        <span class="feature-text">Gestión eficiente de equipos</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">📊</span>
                        <span class="feature-text">Seguimiento de proyectos en tiempo real</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">🏆</span>
                        <span class="feature-text">Participación en competencias prestigiosas</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">💡</span>
                        <span class="feature-text">Desarrollo de habilidades colaborativas</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
