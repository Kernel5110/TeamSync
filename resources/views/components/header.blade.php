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
                @auth
                    <li class="user-profile-item">
                        <a href="{{ route('perfil') }}" class="user-profile-link">
                            <span class="user-name">{{ Auth::user()->name }}</span>
                            <div class="user-avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                    <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </a>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn-logout" title="Cerrar Sesión">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                                </svg>
                            </button>
                        </form>
                    </li>
                @else
                    <li><a href="{{ route('login') }}" class="btn-login">Iniciar Sesion</a></li>
                @endauth
            </ul>
        </nav>
    </div>
</header>
