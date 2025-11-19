<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('style/event.css') }}">
</head>
<body>
    <header class="header">
        <div class="top-header">
            <div class="logo">
                <img src="a" alt="TeamSync">
                <h1>TeamSync</h1>
            </div>
            <nav>
                <ul id="main-nav-ul">
                    <li class="dropdown">
                        <a href="#">Espanol</a>
                        <ul class="dropdown-content">
                    <li><a href="#">English</a></li>
                    <li><a href="#">Francais</a></li>
                    <li><a href="#">Portugues</a></li>
                    </ul>
                    </li>
                    <li><a href="#" class="btn-regitrar">Registrarse</a></li>
                    <li><a href="{{ route('login') }}" class="btn-login">Iniciar Sesion</a></li>
                </ul>
            </nav>
        </div>
        <div class="nav-wrapper" id="nav-wrapper">
            <nav class="secundary-nav">
                <ul>
                    <li><a href="{{ route('index') }}">Inicio</a></li>
                    <li><a href="{{ route('event') }}">Eventos</a></li>
                    <li><a href="{{ route('team') }}">Equipo</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <h1>Eventos</h1>
</body>
</html>

