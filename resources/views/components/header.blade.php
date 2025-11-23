<header class="header">
    <div class="top-header">
        <div class="logo">
            <img src="{{ asset('img/logo.png') }}" alt="TeamSync">
            <h1>TeamSync</h1>
        </div>
        <nav>
            <ul id="main-nav-ul">
                <li><a href="{{ route('index') }}">Inicio</a></li>
                <li><a href="{{ route('event') }}">Eventos</a></li>
                <li><a href="{{ route('team') }}">Equipo</a></li>
                <li><a href="#">Perfil</a></li>
                <li><a href="{{ route('login') }}" class="btn-login">Iniciar Sesion</a></li>
            </ul>
        </nav>
    </div>
</header>
