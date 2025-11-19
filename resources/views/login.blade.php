<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>TeamSync - Iniciar Sesión</title>
        <link rel="stylesheet" href="{{ asset('style/login.css') }}">
    </head>
    <body>
        <header class="header">
            <div class="top-header">
                <div class="logo">
                    <img src="a" alt="TeamSync">
                    <h1>TeamSync</h1>
                </div>
                <nav aria-label="Navegación principal">
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
                <nav class="secundary-nav" aria-label="Navegación secundaria">
                    <ul>
                        <li><a href="{{ route('index') }}">Inicio</a></li>
                        <li><a href="{{ route('event') }}">Eventos</a></li>
                        <li><a href="{{ route('team') }}">Equipo</a></li>
                    </ul>
                </nav>
            </div>
        </header>

        <main class="login-main">
            <div class="login-card">
                <h2>Iniciar Sesión</h2>
                <h6>Ingresa tus credenciales para acceder a TeamSync</h6>

                <form method="post" class="login-form">
                    @csrf
                    <div class="form-group">
                        <label for="correo">Correo</label>
                        <div class="input-wrapper">
                            <span class="input-icon">✉</span>
                            <input type="email" id="correo" name="correo" placeholder="tu@email.com" required autocomplete="email">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="contrasena">Contraseña</label>
                        <div class="input-wrapper">
                            <span class="input-icon">🔒</span>
                            <input type="password" id="contrasena" name="contrasena" required autocomplete="current-password">
                            <button type="button" class="toggle-password" aria-label="Mostrar contraseña">👁</button>
                        </div>
                    </div>

                    <div class="form-links">
                        <a href="recuperar-contrasena.html">Olvide mi contraseña</a>
                        <a href="Registrar.html">Registrarme</a>
                    </div>

                    <button type="submit" class="submit-btn">Iniciar</button>
                </form>
            </div>
        </main>

        <footer>

        </footer>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const togglePassword = document.querySelector('.toggle-password');
                const passwordInput = document.getElementById('contrasena');
                
                if (togglePassword && passwordInput) {
                    togglePassword.addEventListener('click', function() {
                        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                        passwordInput.setAttribute('type', type);
                        this.textContent = type === 'password' ? '👁' : '👁‍🗨';
                        this.setAttribute('aria-label', type === 'password' ? 'Mostrar contraseña' : 'Ocultar contraseña');
                    });
                }
            });
        </script>
    </body>
</html>


