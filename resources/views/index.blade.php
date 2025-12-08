@extends('layouts.app')

@section('content')
    <section class="hero-section">
        <h1>Potencia la colaboración en competencias de innovación</h1>
        <p>
            TeamSync es la plataforma definitiva para la gestión de equipos en eventos de innovación y
            tecnología, como Inovatec, Hackatec y más. Conecta a estudiantes de diversas carreras en roles
            clave —programadores, diseñadores, analistas de negocios y datos— para formar equipos
            dinámicos y competitivos.
        </p>
        <div class="hero-buttons">
            <a href="{{ route('register') }}" class="btn-primary">Únete Ahora →</a>
            <a href="{{ route('events.index') }}" class="btn-secondary">Ver Eventos</a>

        </div>
    </section>
    <section class="why-teamsync-section">
        <h2>¿Por qué elegir TeamSync?</h2>
        <p class="subtitle">Impulsa la creatividad, la colaboración y el éxito en cada competencia</p>
        <div class="feature-cards">
            <div class="card">
                <img src="{{ asset('img/dynamic-teams.png') }}" alt="Equipos Dinámicos">
                <h3>Equipos Dinámicos</h3>
                <p>Forma equipos multidisciplinarios con estudiantes de diferentes carreras y especialidades</p>
            </div>
            <div class="card">
                <img src="{{ asset('img/prestige-events.png') }}" alt="Eventos de Prestigio">
                <h3>Eventos de Prestigio</h3>
                <p>Participa en Inovatec, Hackatec y otros eventos reconocidos de innovación tecnológica</p>
            </div>
            <div class="card">
                <img src="{{ asset('img/real-time-tracking.png') }}" alt="Seguimiento en Tiempo Real">
                <h3>Seguimiento en Tiempo Real</h3>
                <p>Registra tu equipo, únete a grupos existentes y sigue el avance de proyectos en tiempo real</p>
            </div>
        </div>
    </section>
    <section class="roles-section">
        <h2>Roles Especializados</h2>
        <p class="subtitle">Encuentra tu lugar perfecto en el equipo según tu especialidad</p>
        <div class="role-cards">
            <div class="card">
                <img src="{{ asset('img/programmer.png') }}" alt="Programador">
                <h3>Programador</h3>
                <p>Desarrollo de software, aplicaciones y soluciones tecnológicas</p>
            </div>
            <div class="card">
                <img src="{{ asset('img/designer.png') }}" alt="Diseñador">
                <h3>Diseñador</h3>
                <p>UI/UX, diseño gráfico y experiencia de usuario</p>
            </div>
            <div class="card">
                <img src="{{ asset('img/business-analyst.png') }}" alt="Analista de Negocios">
                <h3>Analista de Negocios</h3>
                <p>Estrategia, modelo de negocio y análisis de mercado</p>
            </div>
            <div class="card">
                <img src="{{ asset('img/data-analyst.png') }}" alt="Analista de Datos">
                <h3>Analista de Datos</h3>
                <p>Análisis de datos, estadísticas y business intelligence</p>
            </div>
        </div>
    </section>
    <section class="cta-banner">
        <h2>¡TeamSync impulsa la creatividad, la colaboración y el éxito en cada competencia!</h2>
        <p>Únete a la plataforma que está transformando la manera de formar equipos en eventos de innovación</p>
        <div class="cta-buttons">
            <a href="{{ route('register') }}" class="btn-primary">Crear Cuenta Gratis</a>
            <a href="{{ route('events.index') }}" class="btn-secondary">Explorar Eventos</a>
        </div>
    </section>
@endsection
